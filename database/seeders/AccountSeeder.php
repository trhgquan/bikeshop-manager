<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
            'password' => Hash::make('thquan@fit.hcmus.edu.vn')
        ]);
    }
}
