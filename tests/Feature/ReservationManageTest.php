<?php

namespace Tests\Feature;

use App\Models\GuestBookingRequest;
use App\Models\Room;
use App\Models\Setting;
use App\Models\User;
use App\Support\BookingEmailSender;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ReservationManageTest extends TestCase
{
    use DatabaseTransactions;

    private Room $room;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-10-30 08:00:00');
        Http::fake([
            'api.resend.com/*' => Http::response(['id' => 'email_test'], 200),
        ]);
        config([
            'services.resend.key' => 'test-key',
            'services.booking_notification.to' => 'frontdesk@example.com',
            'mail.from.address' => 'bookings@example.com',
            'mail.from.name' => 'Isange',
        ]);

        if (! Setting::query()->exists()) {
            Setting::forceCreate([
                'company' => 'Isange',
                'email' => 'hotel@example.com',
                'logo' => 'logo.png',
            ]);
        }

        $this->room = Room::forceCreate([
            'roomName' => 'Manage Test Room '.uniqid(),
            'category' => 'double',
            'accommodation_type' => Room::TYPE_ROOM,
            'slug' => 'manage-test-'.uniqid(),
            'image' => 'room.jpg',
            'description' => 'A room.',
            'price' => 100,
            'maxAdults' => 3,
            'maxChildren' => 2,
            'status' => 'active',
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_acknowledgement_email_includes_a_manage_link(): void
    {
        $booking = $this->booking();

        $this->assertTrue(BookingEmailSender::sendGuestAcknowledgement($booking));

        Http::assertSent(function ($request) use ($booking) {
            $body = (string) ($request->data()['text'] ?? '');

            return str_contains($body, 'Manage or cancel this reservation')
                && str_contains($body, '/reservation/'.$booking->public_id.'/manage');
        });
    }

    public function test_guest_can_cancel_before_check_in_and_the_hotel_is_notified(): void
    {
        $booking = $this->booking();

        $this->get($booking->guestManageUrl())
            ->assertOk()
            ->assertSee('Update this reservation')
            ->assertSee('Cancel reservation')
            ->assertSee($booking->public_id);

        $this->from($booking->guestManageUrl())
            ->post($booking->guestCancelUrl())
            ->assertRedirect()
            ->assertSessionHas('success');

        $booking->refresh();
        $this->assertSame(GuestBookingRequest::STATUS_CANCELED_BY_GUEST, $booking->status);
        $this->assertNotNull($booking->canceled_at);

        Http::assertSent(function ($request) use ($booking) {
            $data = $request->data();

            return str_contains((string) ($data['subject'] ?? ''), 'Reservation canceled by guest')
                && str_contains((string) ($data['text'] ?? ''), (string) $booking->public_id);
        });
    }

    public function test_guest_cannot_cancel_or_update_on_the_check_in_date(): void
    {
        $booking = $this->booking();
        Carbon::setTestNow('2026-11-10 09:00:00');

        $this->get($booking->guestManageUrl())
            ->assertOk()
            ->assertSee('Updates and cancellation close on the check-in date')
            ->assertDontSee('Cancel reservation');

        $this->from($booking->guestManageUrl())
            ->post($booking->guestCancelUrl())
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->from($booking->guestManageUrl())
            ->post($booking->guestUpdateUrl(), $this->payload($this->room->id, [
                'additional_requests' => 'A late note',
            ]))
            ->assertRedirect()
            ->assertSessionHas('error');

        $booking->refresh();
        $this->assertSame(GuestBookingRequest::STATUS_PENDING, $booking->status);
        $this->assertNull($booking->additional_requests);
    }

    public function test_guest_update_notifies_the_hotel_and_reopens_a_confirmed_stay(): void
    {
        $booking = $this->booking([
            'status' => GuestBookingRequest::STATUS_CONFIRMED,
            'confirmed_at' => now(),
        ]);

        $this->from($booking->guestManageUrl())
            ->post($booking->guestUpdateUrl(), $this->payload($this->room->id, [
                'check_out' => '2026-11-14',
                'children' => 1,
                'additional_requests' => 'Late arrival',
            ]))
            ->assertRedirect()
            ->assertSessionHas('success');

        $booking->refresh();
        $this->assertSame('2026-11-14', $booking->check_out->toDateString());
        $this->assertSame(1, (int) $booking->children);
        $this->assertSame('Late arrival', $booking->additional_requests);
        $this->assertSame(GuestBookingRequest::STATUS_PENDING, $booking->status);
        $this->assertNull($booking->confirmed_at);

        Http::assertSent(function ($request) {
            $data = $request->data();

            return str_contains((string) ($data['subject'] ?? ''), 'Reservation updated')
                && str_contains((string) ($data['text'] ?? ''), 'Check-out: 2026-11-12 → 2026-11-14')
                && in_array('frontdesk@example.com', (array) ($data['to'] ?? []), true);
        });
    }

    public function test_special_request_alone_keeps_a_confirmed_reservation_confirmed(): void
    {
        $booking = $this->booking([
            'status' => GuestBookingRequest::STATUS_CONFIRMED,
            'confirmed_at' => now(),
        ]);

        $this->from($booking->guestManageUrl())
            ->post($booking->guestUpdateUrl(), $this->payload($this->room->id, [
                'additional_requests' => 'Extra towels',
            ]))
            ->assertRedirect()
            ->assertSessionHas('success');

        $booking->refresh();
        $this->assertSame(GuestBookingRequest::STATUS_CONFIRMED, $booking->status);
        $this->assertSame('Extra towels', $booking->additional_requests);
        $this->assertNotNull($booking->confirmed_at);
    }

    public function test_guest_cannot_exceed_room_capacity(): void
    {
        $booking = $this->booking();

        $this->from($booking->guestManageUrl())
            ->post($booking->guestUpdateUrl(), $this->payload($this->room->id, [
                'adults' => 5,
            ]))
            ->assertRedirect()
            ->assertSessionHasErrors('adults');

        $this->assertSame(2, (int) $booking->fresh()->adults);
    }

    public function test_regular_admin_cannot_cancel_after_check_in_but_super_admin_can(): void
    {
        $booking = $this->booking([
            'status' => GuestBookingRequest::STATUS_CONFIRMED,
            'confirmed_at' => now(),
        ]);
        Carbon::setTestNow('2026-11-11 09:00:00');

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->actingAs($admin)
            ->post(route('bookings.updateStatus', $booking->id), [
                'status' => GuestBookingRequest::STATUS_CANCELED_BY_HOTEL,
                'notify_guest' => '0',
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertSame(GuestBookingRequest::STATUS_CONFIRMED, $booking->fresh()->status);

        $super = User::query()->where('email', User::SUPER_ADMIN_EMAIL)->first()
            ?? User::factory()->create([
                'email' => User::SUPER_ADMIN_EMAIL,
                'role' => User::ROLE_ADMIN,
            ]);

        $this->actingAs($super)
            ->post(route('bookings.updateStatus', $booking->id), [
                'status' => GuestBookingRequest::STATUS_CANCELED_BY_HOTEL,
                'notify_guest' => '1',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $booking->refresh();
        $this->assertSame(GuestBookingRequest::STATUS_CANCELED_BY_HOTEL, $booking->status);
        $this->assertNotNull($booking->canceled_at);
    }

    public function test_report_lists_confirmed_canceled_and_no_show_reservations(): void
    {
        $confirmed = $this->booking([
            'guest_name' => 'Report Confirmed Guest',
            'guest_email' => 'report-confirmed-'.uniqid().'@example.com',
            'status' => GuestBookingRequest::STATUS_CONFIRMED,
        ]);
        $canceled = $this->booking([
            'guest_name' => 'Report Canceled Guest',
            'guest_email' => 'report-canceled-'.uniqid().'@example.com',
            'status' => GuestBookingRequest::STATUS_CANCELED_BY_GUEST,
            'canceled_at' => now(),
        ]);
        $noShow = $this->booking([
            'guest_name' => 'Report NoShow Guest',
            'guest_email' => 'report-noshow-'.uniqid().'@example.com',
            'status' => GuestBookingRequest::STATUS_NO_SHOW,
        ]);

        foreach ([$confirmed, $canceled, $noShow] as $booking) {
            $booking->forceFill([
                'created_at' => '2035-01-15 10:00:00',
                'updated_at' => '2035-01-15 10:00:00',
            ])->save();
        }

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $response = $this->actingAs($admin)->get(route('bookings.print', [
            'start_date' => '2035-01-15',
            'end_date' => '2035-01-15',
        ]));

        $response->assertOk();
        $response->assertSee('(guest 1, hotel 0)');
        $response->assertSee('Confirmed reservations (1)');
        $response->assertSee('Canceled reservations (1)');
        $response->assertSee('No-show reservations (1)');
        $response->assertSee('Report Confirmed Guest');
        $response->assertSee('Report Canceled Guest');
        $response->assertSee('Report NoShow Guest');
        $response->assertSee('Canceled by guest');
    }

    public function test_manage_link_without_a_signature_is_rejected(): void
    {
        $booking = $this->booking();

        $this->get('/reservation/'.$booking->public_id.'/manage')->assertForbidden();
        $this->post('/reservation/'.$booking->public_id.'/cancel')->assertForbidden();
    }

    private function nextReference(): string
    {
        do {
            $ref = (string) random_int(1000, 9999);
        } while (GuestBookingRequest::query()->where('public_id', $ref)->exists());

        return $ref;
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function booking(array $overrides = []): GuestBookingRequest
    {
        $checkIn = '2026-11-10';
        $checkOut = '2026-11-12';

        return GuestBookingRequest::create(array_merge([
            'public_id' => $this->nextReference(),
            'room_id' => $this->room->id,
            'cart_items' => [
                'rooms' => [[
                    'room_id' => $this->room->id,
                    'name' => $this->room->roomName,
                    'slug' => $this->room->slug,
                    'price' => 100,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'nights' => 2,
                    'adults' => 2,
                    'children' => 0,
                ]],
                'experiences' => [],
            ],
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'guest_name' => 'Aline Uwase',
            'guest_phone' => '+250788000111',
            'guest_email' => 'aline-'.uniqid().'@example.com',
            'guest_country' => 'Rwanda',
            'fulfillment_choice' => 'email',
            'payment_method' => 'pay_at_hotel',
            'total_usd' => 200,
            'adults' => 2,
            'children' => 0,
            'status' => GuestBookingRequest::STATUS_PENDING,
            'message_body' => 'Booking body',
        ], $overrides));
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(int $roomId, array $overrides = []): array
    {
        return array_merge([
            'check_in' => '2026-11-10',
            'check_out' => '2026-11-12',
            'adults' => 2,
            'children' => 0,
            'room_id' => $roomId,
            'additional_requests' => '',
        ], $overrides);
    }
}
