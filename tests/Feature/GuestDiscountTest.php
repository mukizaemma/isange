<?php

namespace Tests\Feature;

use App\Models\GuestUpdate;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GuestDiscountTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_register_and_verify_with_a_four_digit_code(): void
    {
        config([
            'services.resend.key' => 'test-key',
            'mail.from.address' => 'bookings@example.com',
            'mail.from.name' => 'Isange Paradise',
        ]);
        Http::fake(['api.resend.com/*' => Http::response(['id' => 'email-1'], 200)]);

        $response = $this->post(route('guest.discount.register'), [
            'name' => 'Test Guest',
            'email' => 'guest@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'marketing_opt_in' => '1',
        ]);

        $response->assertRedirect(route('guest.discount.verify'));
        $user = User::where('email', 'guest@example.com')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertSame(User::ROLE_GUEST, $user->role);
        $this->assertTrue($user->marketing_opt_in);
        $this->assertNotNull($user->email_otp_hash);

        $request = Http::recorded()->first()[0];
        preg_match('/>(\d{4})</', (string) $request['html'], $matches);
        $this->assertArrayHasKey(1, $matches);

        $this->post(route('guest.discount.verify.store'), ['code' => $matches[1]])
            ->assertRedirect(route('booking.checkout'));

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        $this->assertTrue($user->hasUnlockedDiscount());
        $this->assertNull($user->email_otp_hash);
    }

    public function test_room_discount_is_only_exposed_to_an_eligible_guest(): void
    {
        $room = Room::create([
            'roomName' => 'Garden Room',
            'category' => 'double',
            'slug' => 'garden-room',
            'image' => 'room.jpg',
            'description' => 'A garden room.',
            'price' => 100,
            'price_rwf' => 130000,
            'discount_enabled' => true,
            'discount_type' => Room::DISCOUNT_PERCENT,
            'discount_value' => 30,
        ]);

        $this->assertSame(100.0, $room->bookingPriceUsd(false));
        $this->assertSame(70.0, $room->bookingPriceUsd(true));

        $pendingGuest = User::factory()->create([
            'role' => User::ROLE_GUEST,
            'email_verified_at' => null,
        ]);
        $verifiedGuest = User::factory()->create([
            'role' => User::ROLE_GUEST,
            'email_verified_at' => now(),
        ]);

        $this->assertFalse($pendingGuest->hasUnlockedDiscount());
        $this->assertFalse($verifiedGuest->hasUnlockedDiscount());
        $this->withSession(['guest_discount_unlocked_user_id' => $verifiedGuest->id]);
        $this->assertTrue($verifiedGuest->hasUnlockedDiscount());
        $this->assertTrue(Hash::check('password', $verifiedGuest->password));
    }

    public function test_new_and_returning_guests_use_the_same_modal_otp_and_returns_are_counted(): void
    {
        config([
            'services.resend.key' => 'test-key',
            'mail.from.address' => 'bookings@example.com',
            'mail.from.name' => 'Isange Paradise',
        ]);
        Http::fake(['api.resend.com/*' => Http::response(['id' => 'email-modal'], 200)]);

        $this->postJson(route('guest.discount.code.request'), [
            'email' => 'returning@example.com',
        ])->assertOk()->assertJsonPath('attempts', 3);

        $guest = User::where('email', 'returning@example.com')->firstOrFail();
        $this->assertSame(User::ROLE_GUEST, $guest->role);

        $firstEmail = Http::recorded()->last()[0];
        preg_match('/>(\d{4})</', (string) $firstEmail['html'], $firstCode);
        $this->postJson(route('guest.discount.code.verify'), ['code' => $firstCode[1]])
            ->assertOk()
            ->assertJsonPath('discount_unlocked', true);

        $guest->refresh();
        $this->assertSame(1, $guest->discount_unlock_count);
        $this->assertAuthenticatedAs($guest);
        $this->assertSame($guest->id, session('guest_discount_unlocked_user_id'));

        auth()->logout();
        session()->flush();

        $this->postJson(route('guest.discount.code.request'), [
            'email' => 'returning@example.com',
        ])->assertOk();
        $secondEmail = Http::recorded()->last()[0];
        preg_match('/>(\d{4})</', (string) $secondEmail['html'], $secondCode);
        $this->postJson(route('guest.discount.code.verify'), ['code' => $secondCode[1]])
            ->assertOk();

        $this->assertSame(2, $guest->fresh()->discount_unlock_count);
    }

    public function test_modal_otp_is_locked_after_three_wrong_attempts(): void
    {
        config([
            'services.resend.key' => 'test-key',
            'mail.from.address' => 'bookings@example.com',
        ]);
        Http::fake(['api.resend.com/*' => Http::response(['id' => 'email-modal'], 200)]);

        $this->postJson(route('guest.discount.code.request'), [
            'email' => 'attempts@example.com',
        ])->assertOk();

        $email = Http::recorded()->last()[0];
        preg_match('/>(\d{4})</', (string) $email['html'], $sentCode);
        $wrongCode = $sentCode[1] === '0000' ? '9999' : '0000';

        $this->postJson(route('guest.discount.code.verify'), ['code' => $wrongCode])
            ->assertStatus(422)
            ->assertJsonPath('attempts_remaining', 2);
        $this->postJson(route('guest.discount.code.verify'), ['code' => $wrongCode])
            ->assertStatus(422)
            ->assertJsonPath('attempts_remaining', 1);
        $this->postJson(route('guest.discount.code.verify'), ['code' => $wrongCode])
            ->assertStatus(422)
            ->assertJsonPath('locked', true);
    }

    public function test_admin_can_open_latest_guest_reporting_page(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        User::factory()->create([
            'name' => 'Newest Guest',
            'role' => User::ROLE_GUEST,
            'marketing_opt_in' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.guests.index'))
            ->assertOk()
            ->assertSee('Newest Guest')
            ->assertSee('Returning guests in range')
            ->assertSee('Only verified guests who explicitly opted in');
    }

    public function test_admin_update_only_sends_to_an_opted_in_verified_guest(): void
    {
        config([
            'services.resend.key' => 'test-key',
            'mail.from.address' => 'bookings@example.com',
            'mail.from.name' => 'Isange Paradise',
        ]);
        Http::fake(['api.resend.com/*' => Http::response(['id' => 'email-2'], 200)]);

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $guest = User::factory()->create([
            'role' => User::ROLE_GUEST,
            'marketing_opt_in' => true,
            'marketing_unsubscribe_token' => str_repeat('a', 64),
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.guests.updates.send'), [
                'title' => 'Seasonal update',
                'description' => 'A new guest experience is available.',
                'recipient_mode' => 'selected',
                'guest_ids' => [$guest->id],
            ])
            ->assertSessionHas('success');

        $update = GuestUpdate::firstOrFail();
        $this->assertSame(1, $update->recipient_count);
        $this->assertSame(1, $update->sent_count);
        $this->assertNotNull($update->recipients()->firstOrFail()->sent_at);
        Http::assertSentCount(1);
    }

    public function test_verified_guest_login_returns_to_public_booking_page(): void
    {
        $guest = User::factory()->create([
            'role' => User::ROLE_GUEST,
            'email_verified_at' => now(),
        ]);

        $this->post('/login', [
            'email' => $guest->email,
            'password' => 'password',
        ])->assertRedirect(route('booking.checkout'));
    }
}
