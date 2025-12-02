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
        $tour = Tour::factory()->create(['vendor_id' => $vendor->id, 'title' => 'Tour Andes']);
        $date = TourDate::factory()->create(['tour_id' => $tour->id]);

        $reservation = Reservation::factory()
            ->for($tour, 'tour')
            ->for($date, 'tourDate')
            ->create([
                'vendor_id' => $vendor->id,
                'total_amount' => 50000,
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
        $export->assertHeader('Content-Type', 'application/vnd.ms-excel');
        $export->assertSee($passenger->document_number);
        $export->assertSee('Tour Andes');
    }
}
