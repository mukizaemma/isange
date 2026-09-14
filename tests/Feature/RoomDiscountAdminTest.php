<?php

namespace Tests\Feature;

use App\Models\DiscountClosedDate;
use App\Models\GuestBookingRequest;
use App\Models\Room;
use App\Models\Setting;
use App\Models\User;
use App\Support\DiscountStayAvailability;
use App\Support\RoomDiscountPromotion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomDiscountAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_apply_and_remove_a_discount_across_all_priced_rooms(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $first = $this->room('Garden Room', 100);
        $second = $this->room('Family Room', 200);
        $unpriced = $this->room('Call for Price', null);

        $this->actingAs($admin)
            ->post(route('rooms.bulkDiscount'), [
                'action' => 'apply',
                'bulk_discount_type' => Room::DISCOUNT_PERCENT,
                'bulk_discount_value' => 25,
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame(75.0, $first->fresh()->salePriceUsd());
        $this->assertSame(150.0, $second->fresh()->salePriceUsd());
        $this->assertFalse($unpriced->fresh()->discount_enabled);
        $this->assertSame(25.0, RoomDiscountPromotion::maximumPercent());

        $this->actingAs($admin)
            ->post(route('rooms.bulkDiscount'), ['action' => 'remove'])
            ->assertRedirect()
            ->assertSessionHas('success');

        $first->refresh();
        $this->assertFalse($first->discount_enabled);
        $this->assertNull($first->discount_type);
        $this->assertNull($first->discount_value);
        $this->assertNull(RoomDiscountPromotion::maximumPercent());
    }

    public function test_individual_room_edit_overrides_or_removes_the_bulk_discount(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $room = $this->room('Garden Suite', 100);

        $this->actingAs($admin)->post(route('rooms.bulkDiscount'), [
            'action' => 'apply',
            'bulk_discount_type' => Room::DISCOUNT_PERCENT,
            'bulk_discount_value' => 30,
        ])->assertRedirect();

        $this->actingAs($admin)->post(route('updateRoom', $room->id), [
            'roomName' => $room->roomName,
            'accommodation_type' => Room::TYPE_ROOM,
            'price' => 100,
            'discount_enabled' => 1,
            'discount_type' => Room::DISCOUNT_PERCENT,
            'discount_value' => 10,
            'description' => 'Updated room.',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertSame(90.0, $room->fresh()->bookingPriceUsd(true));

        $this->actingAs($admin)->post(route('updateRoom', $room->id), [
            'roomName' => $room->roomName,
            'accommodation_type' => Room::TYPE_ROOM,
            'price' => 100,
            'discount_enabled' => 0,
            'description' => 'Updated room.',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $room->refresh();
        $this->assertFalse($room->discount_enabled);
        $this->assertNull($room->discount_type);
        $this->assertNull($room->discount_value);
        $this->assertSame(100.0, $room->bookingPriceUsd(true));
    }

    public function test_promotion_strip_uses_the_highest_live_room_discount(): void
    {
        $this->room('Ten Percent Room', 100, 10);
        $this->room('Twenty Five Percent Room', 200, 25);

        $this->get(route('aboutUs'))
            ->assertOk()
            ->assertSee('Save Up to <strong>25%</strong>', false)
            ->assertDontSee('Save Up to <strong>30%</strong>', false);
    }

    public function test_promotion_strip_and_unlock_flow_are_hidden_when_no_discount_is_set(): void
    {
        $this->room('Full Price Room', 100);

        $this->get(route('aboutUs'))
            ->assertOk()
            ->assertDontSee('Save Up to', false)
            ->assertDontSee('Lower Than OTA Prices', false)
            ->assertDontSee('id="unlockDiscountModal"', false);

        $this->get(route('booking.checkout'))
            ->assertOk()
            ->assertDontSee('Save Up to', false)
            ->assertDontSee('Book on Discount', false)
            ->assertDontSee('id="unlockDiscountModal"', false);

        $this->get(route('guest.discount'))
            ->assertRedirect(route('booking.checkout'));

        $this->postJson(route('guest.discount.code.request'), [
            'email' => 'nodiscount@example.com',
        ])->assertStatus(422);
    }

    public function test_bulk_percent_discount_drives_the_public_promotion_percent(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->room('Garden Room', 100);
        $this->room('Family Room', 200);

        $this->actingAs($admin)->post(route('rooms.bulkDiscount'), [
            'action' => 'apply',
            'bulk_discount_type' => Room::DISCOUNT_PERCENT,
            'bulk_discount_value' => 18,
        ])->assertRedirect();

        $this->assertSame(18.0, RoomDiscountPromotion::maximumPercent());

        $this->get(route('aboutUs'))
            ->assertOk()
            ->assertSee('Save Up to <strong>18%</strong>', false)
            ->assertSee('Up to <strong>18%</strong> Lower Than OTA Prices', false);

        $this->get(route('guest.discount'))
            ->assertOk()
            ->assertSee('Unlock up to 18% off room rates', false);
    }

    public function test_admin_can_close_and_open_discount_nights(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->room('Garden Room', 100, 20);

        $this->actingAs($admin)
            ->post(route('rooms.discountNights'), [
                'action' => 'close_range',
                'range_from' => '2026-09-20',
                'range_to' => '2026-09-21',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertTrue(DiscountStayAvailability::isNightClosed('2026-09-20'));
        $this->assertTrue(DiscountStayAvailability::isNightClosed('2026-09-21'));
        $this->assertFalse(DiscountStayAvailability::isOpenForStay('2026-09-20', '2026-09-22'));
        $this->assertTrue(DiscountStayAvailability::isOpenForStay('2026-09-22', '2026-09-24'));

        $this->actingAs($admin)
            ->post(route('rooms.discountNights'), [
                'action' => 'toggle',
                'date' => '2026-09-20',
            ])
            ->assertRedirect();

        $this->assertFalse(DiscountStayAvailability::isNightClosed('2026-09-20'));
        $this->assertFalse(DiscountStayAvailability::isOpenForStay('2026-09-20', '2026-09-22'));
    }

    public function test_closed_night_blocks_discount_for_the_whole_stay(): void
    {
        $room = $this->room('Garden Room', 100, 25);
        $guest = User::factory()->create([
            'role' => User::ROLE_GUEST,
            'email_verified_at' => now(),
        ]);
        DiscountClosedDate::query()->create(['closed_on' => '2026-10-02']);

        $this->assertTrue($room->discountAppliesForStay(true, '2026-10-04', '2026-10-06'));
        $this->assertFalse($room->discountAppliesForStay(true, '2026-10-01', '2026-10-04'));
        $this->assertSame(75.0, $room->pricingForStay(true, '2026-10-04', '2026-10-06')['price']);
        $this->assertSame(100.0, $room->pricingForStay(true, '2026-10-01', '2026-10-04')['price']);

        $this->actingAs($guest)->withSession([
            'guest_discount_unlocked_user_id' => $guest->id,
            'guest_discount_expires_at' => now()->addHours(2)->timestamp,
        ]);

        $this->getJson(route('booking.catalog', [
            'check_in' => '2026-10-01',
            'check_out' => '2026-10-04',
        ]))
            ->assertOk()
            ->assertJsonPath('discount_open', false)
            ->assertJsonPath('rooms.0.discount_applied', false)
            ->assertJsonPath('rooms.0.price', 100);

        $this->getJson(route('booking.catalog', [
            'check_in' => '2026-10-04',
            'check_out' => '2026-10-06',
        ]))
            ->assertOk()
            ->assertJsonPath('discount_open', true)
            ->assertJsonPath('rooms.0.discount_applied', true)
            ->assertJsonPath('rooms.0.price', 75);
    }

    public function test_discount_nights_calendar_shows_overlapping_bookings(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $room = $this->room('Garden Room', 100, 15);

        GuestBookingRequest::query()->create([
            'room_id' => $room->id,
            'check_in' => '2026-11-10',
            'check_out' => '2026-11-12',
            'guest_name' => 'Busy Guest',
            'guest_phone' => '250780000000',
            'guest_email' => 'busy@example.com',
            'guest_country' => 'Rwanda',
            'fulfillment_choice' => 'email',
            'message_body' => 'Test booking',
            'status' => GuestBookingRequest::STATUS_CONFIRMED,
        ]);

        $this->actingAs($admin)
            ->get(route('getRooms', ['month' => '2026-11']))
            ->assertOk()
            ->assertSee('Busy nights')
            ->assertSee('1 bk');
    }

    public function test_promo_dates_on_the_discount_form_limit_public_stays_and_the_calendar(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        Setting::query()->create(['title' => 'Isange']);
        $this->room('Garden Room', 100);

        $this->actingAs($admin)->post(route('rooms.bulkDiscount'), [
            'action' => 'apply',
            'bulk_discount_type' => Room::DISCOUNT_PERCENT,
            'bulk_discount_value' => 10,
            'discount_from' => '2026-09-14',
            'discount_to' => '2026-09-20',
        ])->assertRedirect()->assertSessionHas('success');

        $this->assertSame('2026-09-14', optional(Setting::query()->first()->discount_starts_on)->toDateString());
        $this->assertSame('14–20 Sep 2026', RoomDiscountPromotion::periodLabel());
        $this->assertTrue(DiscountStayAvailability::isOpenForStay('2026-09-14', '2026-09-16'));
        $this->assertFalse(DiscountStayAvailability::isOpenForStay('2026-09-10', '2026-09-12'));
        $this->assertFalse(DiscountStayAvailability::isNightOff('2026-09-14'));
        $this->assertTrue(DiscountStayAvailability::isNightOff('2026-09-21'));

        $this->get(route('aboutUs'))
            ->assertOk()
            ->assertSee('Save Up to <strong>10%</strong>', false)
            ->assertSee('For stays 14–20 Sep 2026', false);

        $this->actingAs($admin)
            ->post(route('rooms.discountNights'), [
                'action' => 'toggle',
                'date' => '2026-09-15',
            ])
            ->assertRedirect();
        $this->assertTrue(DiscountStayAvailability::isNightClosed('2026-09-15'));
        $this->assertFalse(DiscountStayAvailability::isOpenForStay('2026-09-14', '2026-09-16'));

        $this->actingAs($admin)
            ->post(route('rooms.discountNights'), [
                'action' => 'toggle',
                'date' => '2026-09-22',
            ])
            ->assertRedirect()
            ->assertSessionHas('error');
    }

    private function room(string $name, ?float $price, ?float $discount = null): Room
    {
        return Room::create([
            'roomName' => $name,
            'category' => 'double',
            'accommodation_type' => Room::TYPE_ROOM,
            'slug' => str($name)->slug(),
            'image' => 'room.jpg',
            'description' => 'A room.',
            'price' => $price,
            'discount_enabled' => $discount !== null,
            'discount_type' => $discount !== null ? Room::DISCOUNT_PERCENT : null,
            'discount_value' => $discount,
        ]);
    }
}
