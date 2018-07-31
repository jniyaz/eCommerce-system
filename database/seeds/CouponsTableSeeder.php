<?php

use App\Coupon;
use Illuminate\Database\Seeder;

class CouponsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Coupon::create([
            'code' => 'ABC30',
            'type' => 'fixed',
            'value' => 30
        ]);

        Coupon::create([
            'code' => 'DEF30',
            'type' => 'percent',
            'percent_off' => 40
        ]);
    }
}
