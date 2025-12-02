<?php

namespace Database\Seeders;

use App\Models\Tour;
use App\Models\TourDate;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class TourSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedTours();
        $this->seedAdminUser();
    }

    private function seedTours(): void
    {
        $tours = [
            [
                'title' => 'Patagonia Esencial',
                'description' => 'Circuito de glaciares y estepa patagónica con guías bilingües.',
                'base_price' => 285000,
                'days' => 7,
                'origin' => 'Buenos Aires, Argentina',
                'destination' => 'El Calafate, Argentina',
                'is_active' => true,
                'dates' => [
                    $this->datePayload(2, 7, 24, 318000),
                    $this->datePayload(6, 7, 18, 305000),
                ],
            ],
            [
                'title' => 'Norte Andino Express',
                'description' => 'Descubrí Salta, Purmamarca y Cafayate con degustaciones regionales.',
                'base_price' => 198000,
                'days' => 5,
                'origin' => 'Córdoba, Argentina',
                'destination' => 'Salta & Jujuy, Argentina',
                'is_active' => true,
                'dates' => [
                    $this->datePayload(3, 5, 30, 215000),
                    $this->datePayload(8, 5, 26, 225000),
                ],
            ],
            [
                'title' => 'Caribe Premium para Equipos',
                'description' => 'Paquete all-inclusive orientado a incentivos corporativos en Riviera Maya.',
                'base_price' => 642000,
                'days' => 6,
                'origin' => 'Ciudad de México, México',
                'destination' => 'Riviera Maya, México',
                'is_active' => true,
                'dates' => [
                    $this->datePayload(4, 6, 40, 715000),
                    $this->datePayload(10, 6, 32, 699000),
                ],
            ],
        ];

        foreach ($tours as $payload) {
            $dates = $payload['dates'];
            unset($payload['dates']);

            /** @var \App\Models\Tour $tour */
            $tour = Tour::updateOrCreate(
                ['title' => $payload['title']],
                $payload
            );

            foreach ($dates as $dateData) {
                $start = $dateData['start_date'];
                $end = $dateData['end_date'];

                /** @var \App\Models\TourDate $date */
                $date = TourDate::withTrashed()->updateOrCreate(
                    [
                        'tour_id' => $tour->id,
                        'start_date' => $start->toDateString(),
                    ],
                    [
                        'end_date' => $end->toDateString(),
                        'capacity' => $dateData['capacity'],
                        'available' => $dateData['available'],
                        'price' => $dateData['price'],
                        'is_active' => $dateData['is_active'],
                    ]
                );

                if ($date->trashed()) {
                    $date->restore();
                }
            }
        }
    }

    private function seedAdminUser(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@travelcart.test'],
            [
                'name' => 'TravelCart Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'admin',
                'is_admin' => true,
                'is_approved' => true,
            ]
        );
    }

    private function datePayload(int $weeksAhead, int $durationDays, int $capacity, int $price): array
    {
        $start = Carbon::now()->addWeeks($weeksAhead)->startOfDay();
        $end = (clone $start)->addDays($durationDays);

        return [
            'start_date' => $start,
            'end_date' => $end,
            'capacity' => $capacity,
            'available' => $capacity,
            'price' => $price,
            'is_active' => true,
        ];
    }
}
