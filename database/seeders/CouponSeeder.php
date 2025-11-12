<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coupon;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        Coupon::updateOrCreate(['code' => 'BIENVENIDA'], [
            'type' => 'percent',
            'amount' => 10,           // 10%
            'min_total' => 0,
            'role_only' => null,
            'max_uses' => 1000,
            'max_uses_per_user' => 1,
            'starts_at' => now()->subDay(),
            'ends_at'   => now()->addMonths(6),
            'is_active' => true,
        ]);

        Coupon::updateOrCreate(['code' => 'MAYORISTA1000'], [
            'type' => 'fixed',
            'amount' => 1000,         // $1000
            'min_total' => 5000,
            'role_only' => 'vendor',  // solo vendor
            'max_uses' => null,
            'max_uses_per_user' => 5,
            'starts_at' => null,
            'ends_at'   => null,
            'is_active' => true,
        ]);
    }
}
