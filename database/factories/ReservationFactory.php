<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\Tour;
use App\Models\TourDate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        return [
            'order_id'     => null,
            'tour_id'      => Tour::factory(),
            'tour_date_id' => TourDate::factory(),
            'vendor_id'    => User::factory()->vendor(),
            'qty'          => 1,
            'status'       => 'pending',
            'locator'      => Str::upper(Str::random(6)),
            'total_amount' => 10000,
        ];
    }
}
