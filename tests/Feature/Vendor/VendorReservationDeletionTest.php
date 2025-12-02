<?php

namespace Tests\Feature\Vendor;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorReservationDeletionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function vendor_deleting_a_reservation_cancels_and_hides_the_order()
    {
        $vendor = User::factory()->vendor()->create();

        $reservation = Reservation::factory()->create([
            'vendor_id' => $vendor->id,
            'status' => 'pending_payment',
        ]);

        $order = $reservation->order;

        $this->actingAs($vendor)
            ->delete(route('vendor.reservations.destroy', $reservation))
            ->assertRedirect(route('vendor.reservations.index'));

        $this->assertSoftDeleted('reservations', ['id' => $reservation->id]);

        $order->refresh();

        $this->assertEquals('cancelled', $order->status);
        $this->assertSoftDeleted('orders', ['id' => $order->id]);
    }
}
