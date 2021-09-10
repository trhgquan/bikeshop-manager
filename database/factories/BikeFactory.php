<?php

namespace Database\Factories;

use App\Models\Bike;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BikeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Bike::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $buyPrice = random_int(1, 1337);

        return [
            'brand_id'         => \App\Models\Brand::all()->random()->id,
            'bike_name'        => Str::random(20),
            'bike_description' => Str::random(100),
            'bike_stock'       => random_int(1, 100),
            'bike_buy_price'   => $buyPrice,
            'bike_sell_price'  => $buyPrice * 2,
            'created_by_user'  => \App\Models\User::all()->random()->id,
            'updated_by_user'  => \App\Models\User::all()->random()->id,
        ];
    }
}
