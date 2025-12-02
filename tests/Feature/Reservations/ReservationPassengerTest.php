<?php

namespace Tests\Feature\Reservations;

use App\Models\Order;
use App\Models\Reservation;
use App\Models\ReservationPassenger;
use App\Models\Tour;
use App\Models\TourDate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ReservationPassengerTest extends TestCase
{
    use RefreshDatabase;

    public function test_same_passenger_cannot_be_added_twice_for_same_tour(): void
    {
        $buyer = User::factory()->create();
        $vendor = User::factory()->vendor()->create(['is_approved' => true]);
        $tour = Tour::factory()->create(['vendor_id' => $vendor->id]);
        $date = TourDate::factory()->for($tour)->create([
            'capacity' => 5,
            'available' => 5,
        ]);

        $orderA = Order::create([
            'user_id' => $buyer->id,
            'code' => Str::random(8),
            'status' => 'pending_payment',
            'total' => 250000,
        ]);

        $reservationA = Reservation::create([
            'order_id' => $orderA->id,
            'tour_id' => $tour->id,
            'tour_date_id' => $date->id,
            'vendor_id' => $vendor->id,
            'qty' => 1,
            'status' => 'pending_payment',
            'locator' => Str::upper(Str::random(6)),
            'total_amount' => 250000,
        ]);

        ReservationPassenger::create([
            'reservation_id' => $reservationA->id,
            'document_number' => 'DOC-123',
            'first_name' => 'Ana',
            'last_name' => 'Pérez',
        ]);

        $orderB = Order::create([
            'user_id' => $buyer->id,
            'code' => Str::random(8),
            'status' => 'awaiting_passengers',
            'total' => 250000,
        ]);

        $reservationB = Reservation::create([
            'order_id' => $orderB->id,
            'tour_id' => $tour->id,
            'tour_date_id' => $date->id,
            'vendor_id' => $vendor->id,
            'qty' => 1,
            'status' => 'awaiting_passengers',
            'locator' => Str::upper(Str::random(6)),
            'hold_expires_at' => now()->addMinutes(10),
            'total_amount' => 250000,
        ]);

        $response = $this->actingAs($buyer)->post(route('reservations.passengers.store', $reservationB), [
            'passengers' => [[
                'first_name' => 'Ana',
                'last_name' => 'Pérez',
                'document_number' => 'DOC-123',
                'birth_date' => '1990-01-01',
                'sex' => 'F',
            ]],
        ]);

        $response->assertSessionHasErrors('passengers.0.document_number');
        $this->assertDatabaseMissing('reservation_passengers', [
            'reservation_id' => $reservationB->id,
            'document_number' => 'DOC-123',
        ]);
    }
}

