<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\UserServices;

class AccountSeeder extends Seeder
{
    /**
     * UserServices will be using.
     *
     * @param  \App\Services\UserServices
     */
    protected $userServices;

    /**
     * Constructor for AccountSeeder
     *
     * @return void
     */
    public function __construct() {
        $this->userServices = new UserServices;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Add a sample account.
        $this->userServices->createUser([
            'name' => 'Trần Hoàng Quân',
            'username' => 'thquan',
            'email' => 'thquan@fit.hcmus.edu.vn',
            'password' => 'thquan@fit.hcmus.edu.vn',
            'role' => \App\Models\Role::ROLE_ADMIN
        ]);

        $this->userServices->createUser([
            'name' => 'Trần Lùi Xuống',
            'username' => 'tlxuong',
            'email' => 'tlxuong@fit.hcmus.edu.vn',
            'password' => 'tlxuong@fit.hcmus.edu.vn',
            'role' => \App\Models\Role::ROLE_MANAGER,
        ]);
    }
}
