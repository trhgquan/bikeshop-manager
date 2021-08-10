<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BikeTest extends TestCase
{
    /**
     * Test if non-authenticated user cannot view bikes index page.
     *
     * @return void
     */
    public function test_bikes_index_no_authenticate() {
        $this->get(route('bikes.index'))
            ->assertRedirect(route('auth.login.index'));
    }

    /**
     * Test if authenticated user can view bikes index page.
     * 
     * @return void
     */
    public function test_bikes_index_authenticated() {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->make([
            'role' => \App\Models\Role::ROLE_STAFF
        ]);
        Auth::login($user);

        $this->get(route('bikes.index'))
            ->assertStatus(200);
    }
}
