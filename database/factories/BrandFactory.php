<?php

namespace Database\Factories;

use App\Models\Brand;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class BrandFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Brand::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'brand_name' => Str::random(50),
            'brand_description' => Str::random(100),
            'created_by_user' => \App\Models\User::all()->random()->id,
            'updated_by_user' => \App\Models\User::all()->random()->id,
        ];
    }
}
