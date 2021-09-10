<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Brand::create([
            'brand_name'        => 'Sukhoi',
            'brand_description' => 'Sukhoi aeroplanes have been flying over Russian sky!',
            'created_by_user'   => 1,
            'updated_by_user'   => 1,
        ]);

        Brand::create([
            'brand_name'        => 'Mikoyan',
            'brand_description' => 'Mikoyan aeroplanes have been flying over Russian and Soviet sky!',
            'created_by_user'   => 2,
            'updated_by_user'   => 2,
        ]);
    }
}
