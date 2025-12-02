<?php

namespace Database\Factories;

use App\Models\Tour;
use App\Models\TourDate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<TourDate>
 */
class TourDateFactory extends Factory
{
    protected $model = TourDate::class;

    public function definition(): array
    {
        $start = Carbon::now()->addDays($this->faker->numberBetween(7, 60))->startOfDay();
        $end = (clone $start)->addDays($this->faker->numberBetween(3, 10));
        $capacity = $this->faker->numberBetween(10, 40);

        return [
            'tour_id' => Tour::factory(),
            'start_date' => $start,
            'end_date' => $end,
            'capacity' => $capacity,
            'available' => $capacity,
            'price' => $this->faker->numberBetween(120000, 800000),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function past(): static
    {
        return $this->state(function () {
            $start = Carbon::now()->subDays(5)->startOfDay();

            return [
                'start_date' => $start,
                'end_date' => (clone $start)->addDays(3),
            ];
        });
    }
}
