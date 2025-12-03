<?php

namespace Tests\Feature\Vendor;

use App\Models\Reservation;
use App\Models\ReservationPassenger;
use App\Models\ReservationPayment;
use App\Models\Order;
use App\Models\Tour;
use App\Models\TourDate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorTourDeletionFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_vendor_must_confirm_before_deleting_tour_with_reservations_and_can_export_trash(): void
    {
        $vendor = User::factory()->create(['role' => 'vendor', 'is_approved' => true]);
        $buyer = User::factory()->create();

        $tour = Tour::factory()->for($vendor, 'vendor')->create();
        $date = TourDate::factory()->for($tour)->create(['capacity' => 10, 'available' => 9]);

        $order = Order::factory()->for($buyer)->create();

        $reservation = Reservation::factory()
            ->for($order)
            ->for($tour)
            ->for($date)
            ->for($vendor, 'vendor')
            ->create(['status' => 'pending_payment', 'qty' => 1, 'total_amount' => 50000]);

        ReservationPassenger::factory()->for($reservation)->create([
            'first_name' => 'Ana',
            'last_name' => 'Lopez',
            'document_number' => '123',
        ]);

        ReservationPayment::create([
            'reservation_id' => $reservation->id,
            'vendor_id' => $vendor->id,
            'amount' => 20000,
        ]);

        $this->actingAs($vendor)
            ->delete(route('vendor.tours.destroy', $tour))
            ->assertRedirect(route('vendor.tours.confirm-delete', $tour));

        $this->actingAs($vendor)
            ->get(route('vendor.tours.confirm-delete', $tour))
            ->assertOk()
            ->assertSee('reservas activas');

        $this->actingAs($vendor)
            ->delete(route('vendor.tours.destroy', $tour), ['confirm' => 1])
            ->assertRedirect(route('vendor.tours.trash'));

        $this->assertSoftDeleted('tours', ['id' => $tour->id]);
        $this->assertSoftDeleted('reservations', ['id' => $reservation->id]);
        $this->assertSoftDeleted('orders', ['id' => $order->id]);

        $trash = $this->actingAs($vendor)->get(route('vendor.tours.trash'));
        $trash->assertOk()->assertSee($tour->title);

        $export = $this->actingAs($vendor)->get(route('vendor.tours.trash.export', $tour->id));
        $export->assertOk();
        $export->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertStringContainsString('Ana Lopez', $export->getContent());
        $this->assertStringContainsString('20.000', $export->getContent());
    }
}
