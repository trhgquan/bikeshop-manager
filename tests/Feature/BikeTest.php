<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BikeTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test if non-authenticated user cannot view bikes index page.
     *
     * @return void
     */
    public function test_view_bikes_index_as_not_authenticated_user() {
        $this->get(route('bikes.index'))
            ->assertRedirect(route('auth.login.index'));
    }

    /**
     * Test if authenticated user can view bikes index page.
     * 
     * @return void
     */
    public function test_view_bikes_index_as_authenticated_user() {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->make([
            'role' => \App\Models\Role::ROLE_STAFF
        ]);
        Auth::login($user);

        $this->get(route('bikes.index'))
            ->assertStatus(200);
    }

    public function test_view_bike_show_as_unauthenticated_user() {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_ADMIN
        ]);

        $brand = \App\Models\Brand::factory()->create();

        $bike = \App\Models\Bike::factory()->create();

        $this->assertDatabaseCount('users', 1)
            ->assertDatabaseCount('brands', 1)
            ->assertDatabaseCount('bikes', 1);

        $this->get(route('bikes.show', $bike))
            ->assertRedirect(route('auth.login.index'));
    }

    public function test_view_bike_show_as_authenticated_user() {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF
        ]);

        $brand = \App\Models\Brand::factory()->create();

        $bike = \App\Models\Bike::factory()->create();

        $this->assertDatabaseCount('users', 1)
            ->assertDatabaseCount('brands', 1)
            ->assertDatabaseCount('bikes', 1);

        Auth::login($user);

        $this->get(route('bikes.show', $bike))->assertStatus(200);
    }

    public function test_view_create_bike_as_staff() {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF
        ]);

        Auth::login($user);
        $this->get(route('bikes.create'))->assertStatus(403);
    }

    public function test_view_create_bike_as_manager() {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_MANAGER
        ]);

        $brands = \App\Models\Brand::factory()->count(100)->create();

        Auth::login($user);
        $this->get(route('bikes.create'))->assertStatus(200);

        // Check if all brands appear on the selection.
        $view = $this->view('content.bike.create', compact('brands'));
        foreach ($brands as $brand) {
            $view->assertSee($brand->brand_name);
        }
    }

    public function test_create_bike_as_staff() {
        Session::start();
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF
        ]);

        $brand = \App\Models\Brand::factory()->create();

        Auth::login($user);
        $response = $this->post(route('bikes.store', [
            'brand_id' => $brand->id,
            'bike_name' => 'Cheeki breeki iv domke!',
            'bike_description' => 'Cheeki breeki iv domke!',
            'bike_stock' => 1337,
            'bike_buy_price' => 1337,
            'bike_sell_price' => 1337,
            '_token' => Session::token()
        ]));

        $this->assertDatabaseCount('bikes', 0);
        $response->assertStatus(403);
    }

    public function test_create_bike_as_manager() {
        Session::start();
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_MANAGER
        ]);

        $brand = \App\Models\Brand::factory()->create();

        Auth::login($user);
        $response = $this->post(route('bikes.store', [
            'brand_id' => $brand->id,
            'bike_name' => 'Cheeki breeki',
            'bike_description' => 'Cheeki breeki iv domke!',
            'bike_stock' => 1337,
            'bike_buy_price' => 1337,
            'bike_sell_price' => 1337,
            '_token' => Session::token()
        ]));

        $this->assertDatabaseCount('bikes', 1);

        $bike = \App\Models\Bike::first();
        $response->assertRedirect(route('bikes.show', $bike));
    }

    public function test_view_edit_bike_as_staff() {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF
        ]);

        $brand = \App\Models\Brand::factory()->create();

        $bike = \App\Models\Bike::factory()->create();

        Auth::login($user);
        $this->get(route('bikes.edit', $bike))->assertStatus(403);
    }

    public function test_view_edit_bike_as_manager() {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_MANAGER
        ]);

        $brand = \App\Models\Brand::factory()->create();

        $bike = \App\Models\Bike::factory()->create();

        Auth::login($user);
        $this->get(route('bikes.edit', $bike))->assertStatus(200);
    }

    public function test_edit_bike_as_staff() {
        Session::start();
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF
        ]);

        $brand = \App\Models\Brand::factory()->create();

        $bike = \App\Models\Bike::factory()->create();

        Auth::login($user);
        $response = $this->put(route('bikes.update', $bike), [
            'brand_id' => $brand->id,
            'bike_name' => $bike->bike_name,
            'bike_description' => $bike->bike_description,
            'bike_stock' => $bike->bike_stock + 1337,
            'bike_buy_price' => $bike->bike_buy_price,
            'bike_sell_price' => $bike->bike_sell_price,
            '_token' => Session::token()
        ]);

        $response->assertStatus(403);
    }

    public function test_edit_bike_as_manager() {
        Session::start();
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_MANAGER
        ]);

        $brand = \App\Models\Brand::factory()->create();

        $bike = \App\Models\Bike::factory()->create();

        Auth::login($user);
        $response = $this->put(route('bikes.update', $bike), [
            'brand_id' => $brand->id,
            'bike_name' => $bike->bike_name,
            'bike_description' => $bike->bike_description,
            'bike_stock' => $bike->bike_stock + 1337,
            'bike_buy_price' => $bike->bike_buy_price,
            'bike_sell_price' => $bike->bike_sell_price,
            '_token' => Session::token()
        ]);

        $response->assertRedirect(route('bikes.edit', $bike));
    }

    public function test_delete_bike_as_staff() {
        Session::start();
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF
        ]);
        Auth::login($user);

        $brand = \App\Models\Brand::factory()->create();

        $bike = \App\Models\Bike::factory()->create();
        
        $response = $this->delete(route('bikes.update', $bike), [
            '_token' => Session::token()
        ]);

        $response->assertStatus(403);
    }

    public function test_delete_bike_as_manager() {
        Session::start();
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_MANAGER
        ]);
        Auth::login($user);

        $brand = \App\Models\Brand::factory()->create();

        $bike = \App\Models\Bike::factory()->create();
        
        $response = $this->delete(route('bikes.update', $bike), [
            '_token' => Session::token()
        ]);

        $this->assertSoftDeleted($bike);
        $response->assertRedirect(route('bikes.index'));
    }
}
