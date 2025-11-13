<?php

namespace Database\Factories;

use App\Models\Tour;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tour>
 */
class TourFactory extends Factory
{
    protected $model = Tour::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->unique()->sentence(3),
            'description' => $this->faker->paragraph(),
            'base_price' => $this->faker->numberBetween(150000, 750000),
            'days' => $this->faker->numberBetween(3, 12),
            'origin' => $this->faker->city().', '.$this->faker->country(),
            'destination' => $this->faker->city().', '.$this->faker->country(),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
