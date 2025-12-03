<?php

namespace Tests\Feature\Admin\Vendor;

use App\Models\Reservation;
use App\Models\ReservationPassenger;
use App\Models\Tour;
use App\Models\TourDate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_vendor_metrics_and_export_passengers(): void
    {
        $admin = User::factory()->admin()->create();
        $vendor = User::factory()->create(['role' => 'vendor', 'is_approved' => true, 'legajo' => 'VEN-100']);
        $buyerOne = User::factory()->create(['name' => 'Comprador Uno']);
        $buyerTwo = User::factory()->create(['name' => 'Comprador Dos']);
        $tour = Tour::factory()->create(['vendor_id' => $vendor->id, 'title' => 'Tour Andes']);
        $date = TourDate::factory()->create(['tour_id' => $tour->id, 'capacity' => 5]);

        $reservation = Reservation::factory()
            ->for($tour, 'tour')
            ->for($date, 'tourDate')
            ->create([
                'vendor_id'    => $vendor->id,
                'total_amount' => 50000,
                'qty'          => 2,
                'order_id'     => \Database\Factories\OrderFactory::new()->for($buyerOne)->create()->id,
            ]);

        Reservation::factory()
            ->for($tour, 'tour')
            ->for($date, 'tourDate')
            ->create([
                'vendor_id'    => $vendor->id,
                'total_amount' => 20000,
                'qty'          => 1,
                'order_id'     => \Database\Factories\OrderFactory::new()->for($buyerTwo)->create()->id,
            ]);

        $passenger = ReservationPassenger::create([
            'reservation_id'  => $reservation->id,
            'first_name'      => 'Ana',
            'last_name'       => 'Paz',
            'document_number' => '123',
            'birth_date'      => '1990-01-01',
            'sex'             => 'F',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.vendors.show', $vendor))
            ->assertOk()
            ->assertSee('Tour Andes')
            ->assertSee('50000');

        $export = $this->actingAs($admin)->get(route('admin.vendors.passengers', $vendor));

        $export->assertOk();
        $export->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $export->assertSee($passenger->document_number);
        $export->assertSee('Tour Andes');

        $analytics = $this->actingAs($admin)->get(route('admin.vendors.analytics', $vendor));
        $analytics->assertOk();
        $analytics->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $analytics->assertSee('Comprador Uno');
    }
}
