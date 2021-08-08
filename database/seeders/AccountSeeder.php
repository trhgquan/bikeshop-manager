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
        \App\Models\User::create([
            'name' => 'Trần Hoàng Quân',
            'username' => 'thquan',
            'email' => 'thquan@fit.hcmus.edu.vn',
            'password' => Hash::make('thquan@fit.hcmus.edu.vn'),
            'role' => \App\Models\Role::ROLE_ADMIN
        ]);

        \App\Models\User::create([
            'name' => 'Trần Lùi Xuống',
            'username' => 'tlxuong',
            'email' => 'tlxuong@fit.hcmus.edu.vn',
            'password' => Hash::make('tlxuong@fit.hcmus.edu.vn'),
            'role' => \App\Models\Role::ROLE_MANAGER,
        ]);
    }
}
