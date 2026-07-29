<?php

namespace Tests\Feature;

use App\Models\Room;
use App\Models\User;
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
