<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Add a sample account.
        DB::table('users')->insert([
            'name' => 'Tran Hoang Quan',
            'username' => 'thquan',
            'email' => 'thquan@fit.hcmus.edu.vn',
            'password' => Hash::make('thquan@fit.hcmus.edu.vn'),
            'api_token' => hash('sha256', Str::random(60)),
        ]);

        DB::table('users')->insert([
            'name' => 'Tran Lui Xuong',
            'username' => 'tlxuong',
            'email' => 'tlxuong@fit.hcmus.edu.vn',
            'password' => Hash::make('tlxuong@fit.hcmus.edu.vn'),
            'api_token' => hash('sha256', Str::random(60)),
        ]);
    }
}
