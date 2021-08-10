<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BrandTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Check if non-auth user redirected back to login page or not.
     * 
     * @return void
     */
    public function test_view_brands_index_as_not_authentiated_user() {
        $response = $this->get(route('brands.index'));

        $response->assertRedirect(route('auth.login.index'));
    }

    /**
     * Check if any authenticated user can view every brand.
     *
     * @return void
     */
    public function test_view_brands_index_as_authenticated_user()
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->seed(\Database\Seeders\AccountSeeder::class);

        // Log user in.
        $user = \App\Models\User::where('id', 1)->first();
        Auth::login($user);

        // Anyone can view every Brand.
        $response = $this->get(route('brands.index'));

        $response->assertStatus(200);
    }


    public function test_view_brands_show_as_non_authenticated_user()
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->seed(\Database\Seeders\AccountSeeder::class);

        // Log user in.
        $user = \App\Models\User::where('username', '=', 'tlxuong')->first();

        $brand = \App\Models\Brand::create([
            'brand_name' => 'Lorem ipsum',
            'brand_description' => 'Lorem ipsum dolor sit amet, cost',
            'created_by_user' => $user->id,
            'updated_by_user' => $user->id
        ]);

        // Anyone can view any Brand.
        $response = $this->get(route('brands.show', $brand));

        $response->assertRedirect(route('auth.login.index'));
    }

    /**
     * Check if any authenticated user can view any brand.
     *
     * @return void
     */
    public function test_view_brands_show_as_authenticated_user()
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->seed(\Database\Seeders\AccountSeeder::class);

        // Log user in.
        $user = \App\Models\User::where('username', '=', 'tlxuong')->first();

        $brand = \App\Models\Brand::create([
            'brand_name' => 'Lorem ipsum',
            'brand_description' => 'Lorem ipsum dolor sit amet, cost',
            'created_by_user' => $user->id,
            'updated_by_user' => $user->id
        ]);

        Auth::login($user);

        // Anyone can view any Brand.
        $response = $this->get(route('brands.show', $brand));

        $response->assertStatus(200);
    }

    public function test_view_create_brand_as_staff() {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::create([
            'name' => 'F. D. Thomas',
            'username' => 'meovantomy',
            'password' => Hash::make('meovantomy'),
            'email' => 'meovantomy@gmail.com',
            'role' => \App\Models\Role::ROLE_STAFF
        ]);

        // User cannot create
        Auth::login($user);

        $response = $this->get(route('brands.create'));

        $response->assertStatus(403);
    }

    public function test_view_create_brand_as_manager() {
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->seed(\Database\Seeders\AccountSeeder::class);

        $user = \App\Models\User::where('username', '=', 'tlxuong')->first();
        Auth::login($user);

        $response = $this->get(route('brands.create'));
        $response->assertStatus(200);
    }

    public function test_create_brand_as_staff() {
        Session::start();
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->seed(\Database\Seeders\AccountSeeder::class);

        $user = \App\Models\User::create([
            'name' => 'F. D. Thomas',
            'username' => 'meovantomy',
            'password' => Hash::make('meovantomy'),
            'email' => 'meovantomy@gmail.com',
            'role' => \App\Models\Role::ROLE_STAFF
        ]);

        // User cannot create
        Auth::login($user);

        $response = $this->post(route('brands.store'), [
            'brand_name' => 'Lorem ipsum',
            'brand_description' => 'Lorem ipsum dolor sit amet, cost',
            '_token' => Session::token(),
        ]);

        $response->assertStatus(403);
    }

    public function test_create_brand_as_manager() {
        Session::start();

        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->seed(\Database\Seeders\AccountSeeder::class);

        $user = \App\Models\User::where('username', '=', 'tlxuong')->first();

        Auth::login($user);

        $response = $this->post(route('brands.store'), [
            'brand_name' => 'Lorem ipsum',
            'brand_description' => 'Lorem ipsum dolor sit amet, cost',
            '_token' => Session::token(),
        ]);

        $response->assertStatus(302);
    }

    public function test_view_edit_brand_as_staff() {
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->seed(\Database\Seeders\AccountSeeder::class);

        $user = \App\Models\User::where('username', '=', 'thquan')->first();

        $brand = \App\Models\Brand::create([
            'brand_name' => 'Lorem ipsum',
            'brand_description' => 'Lorem ipsum dolor sit amet, cost',
            'created_by_user' => $user->id,
            'updated_by_user' => $user->id
        ]);

        $user = \App\Models\User::create([
            'name' => 'F. D. Thomas',
            'username' => 'meovantomy',
            'password' => Hash::make('meovantomy'),
            'email' => 'meovantomy@gmail.com',
            'role' => \App\Models\Role::ROLE_STAFF
        ]);

        Auth::login($user);

        $response = $this->get(route('brands.edit', $brand));

        $response->assertStatus(403);
    }

    public function test_view_edit_brand_as_manager() {
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->seed(\Database\Seeders\AccountSeeder::class);

        $user = \App\Models\User::where('username', '=', 'tlxuong')->first();

        $brand = \App\Models\Brand::create([
            'brand_name' => 'Lorem ipsum',
            'brand_description' => 'Lorem ipsum dolor sit amet, cost',
            'created_by_user' => $user->id,
            'updated_by_user' => $user->id
        ]);

        Auth::login($user);

        $response = $this->get(route('brands.edit', $brand));

        $response->assertStatus(200);
    }

    public function test_edit_brand_as_staff() {
        Session::start();
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->seed(\Database\Seeders\AccountSeeder::class);
        
        $user = \App\Models\User::create([
            'name' => 'F. D. Thomas',
            'username' => 'meovantomy',
            'password' => Hash::make('meovantomy'),
            'email' => 'meovantomy@gmail.com',
            'role' => \App\Models\Role::ROLE_STAFF
        ]);

        $brand = \App\Models\Brand::create([
            'brand_name' => 'Lorem ipsum',
            'brand_description' => 'Lorem ipsum dolor sit amet, cost',
            'created_by_user' => $user->id,
            'updated_by_user' => $user->id
        ]);

        Auth::login($user);

        $response = $this->put(route('brands.update', $brand), [
            'brand_name' => 'Cheeky breeky iv damke!',
            '_token' => Session::token()
        ]);

        $response->assertStatus(403);
    }

    public function test_edit_brand_as_manager() {
        Session::start();
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->seed(\Database\Seeders\AccountSeeder::class);
        
        $user = \App\Models\User::where('username', '=', 'tlxuong')->first();

        $brand = \App\Models\Brand::create([
            'brand_name' => 'Lorem ipsum',
            'brand_description' => 'Lorem ipsum dolor sit amet, cost',
            'created_by_user' => $user->id,
            'updated_by_user' => $user->id
        ]);

        Auth::login($user);

        $response = $this->put(route('brands.update', $brand), [
            'brand_name' => 'Cheeky breeky iv damke!',
            '_token' => Session::token()
        ]);

        $response->assertStatus(302);
    }

    public function test_delete_brand_as_staff() {
        Session::start();
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->seed(\Database\Seeders\AccountSeeder::class);
        
        $user = \App\Models\User::where('username', '=', 'tlxuong')->first();

        $brand = \App\Models\Brand::create([
            'brand_name' => 'Lorem ipsum',
            'brand_description' => 'Lorem ipsum dolor sit amet, cost',
            'created_by_user' => $user->id,
            'updated_by_user' => $user->id
        ]);

        $user = \App\Models\User::create([
            'name' => 'F. D. Thomas',
            'username' => 'meovantomy',
            'password' => Hash::make('meovantomy'),
            'email' => 'meovantomy@gmail.com',
            'role' => \App\Models\Role::ROLE_STAFF
        ]);

        Auth::login($user);

        $response = $this->delete(route('brands.update', $brand), [
            '_token' => Session::token()
        ]);

        $response->assertStatus(403);
    }

    public function test_delete_brand_as_manager() {
        Session::start();
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->seed(\Database\Seeders\AccountSeeder::class);
        
        $user = \App\Models\User::where('username', '=', 'tlxuong')->first();

        $brand = \App\Models\Brand::create([
            'brand_name' => 'Lorem ipsum',
            'brand_description' => 'Lorem ipsum dolor sit amet, cost',
            'created_by_user' => $user->id,
            'updated_by_user' => $user->id
        ]);

        Auth::login($user);

        $response = $this->delete(route('brands.update', $brand), [
            '_token' => Session::token()
        ]);

        $response->assertStatus(302);
    }
}
