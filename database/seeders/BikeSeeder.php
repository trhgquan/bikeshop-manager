<?php

namespace Database\Seeders;

use App\Models\Bike;
use Illuminate\Database\Seeder;

class BikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Bike::create([
            'brand_id'         => 1,
            'bike_name'        => 'Sukhoi SU-23',
            'bike_description' => 'Sukhoi SU-23 had served in the Vietnam War!',
            'bike_stock'       => 100,
            'bike_buy_price'   => 100,
            'bike_sell_price'  => 200,
            'created_by_user'  => 1,
            'updated_by_user'  => 1,
        ]);

        Bike::create([
            'brand_id'         => 2,
            'bike_name'        => 'Mikoyan MiG-21',
            'bike_description' => 'Mikoyan MiG-21 had served in the Vietnam War!',
            'bike_stock'       => 100,
            'bike_buy_price'   => 100,
            'bike_sell_price'  => 250,
            'created_by_user'  => 2,
            'updated_by_user'  => 2,
        ]);
    }
}
