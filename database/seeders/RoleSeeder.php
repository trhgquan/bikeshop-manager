<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Role::create([
            'id'        => \App\Models\Role::ROLE_ADMIN,
            'role_name' => 'Admin',
        ]);

        \App\Models\Role::create([
            'id'        => \App\Models\Role::ROLE_MANAGER,
            'role_name' => 'Quản lý',
        ]);

        \App\Models\Role::create([
            'id'        => \App\Models\Role::ROLE_STAFF,
            'role_name' => 'Nhân viên',
        ]);
    }
}
