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
     * Test if non-auth user redirected back to login page or not.
     * 
     * @return void
     */
    public function test_view_brands_index_as_not_authentiated_user() {
        $this->get(route('brands.index'))
            ->assertRedirect(route('auth.login.index'));
    }

    /**
     * Test if any authenticated user can view every brand.
     *
     * @return void
     */
    public function test_view_brands_index_as_authenticated_user()
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        // Log user in.
        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF
        ]);
        Auth::login($user);

        // Anyone can view every Brand.
        $this->get(route('brands.index'))->assertStatus(200);
    }

    /**
     * Test if a non-authenticated user can view a brand.
     * 
     * @return void
     */
    public function test_view_brands_show_as_non_authenticated_user()
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_ADMIN,
        ]);
        
        $brand = \App\Models\Brand::factory()->create([
            'created_by_user' => $user->id,
            'updated_by_user' => $user->id,
        ]);

        $this->assertDatabaseCount('users', 1)
            ->assertDatabaseCount('brands', 1);

        // Anyone can view any Brand.
        $this->get(route('brands.show', $brand))
            ->assertRedirect(route('auth.login.index'));
    }

    /**
     * Test if any authenticated user can view any brand.
     *
     * @return void
     */
    public function test_view_brands_show_as_authenticated_user()
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_ADMIN
        ]);

        $brand = \App\Models\Brand::factory()->create();

        $this->assertDatabaseCount('users', 1)
            ->assertDatabaseCount('brands', 1);

        Auth::login($user);

        // Anyone can view any Brand.
        $this->get(route('brands.show', $brand))->assertStatus(200);
    }

    /**
     * Test if a staff can view Create Brand page.
     * 
     * @return void
     */
    public function test_view_create_brand_as_staff() {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF
        ]);
        Auth::login($user);

        $this->get(route('brands.create'))->assertStatus(403);
    }

    /**
     * Test if a manager can view Create Brand page.
     * 
     * @return void
     */
    public function test_view_create_brand_as_manager() {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_ADMIN
        ]);
        Auth::login($user);

        $this->get(route('brands.create'))->assertStatus(200);
    }

    /**
     * Test if a Staff cannot create a Brand.
     * 
     * @return void
     */
    public function test_create_brand_as_staff() {
        Session::start();
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF
        ]);
        Auth::login($user);

        $response = $this->actingAs($user)->post(route('brands.store'), [
            'brand_name' => 'Lorem ipsum',
            'brand_description' => 'Lorem ipsum dolor sit amet, cost',
            '_token' => Session::token()
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseCount('brands', 0);
    }

    /**
     * Test if a Manager can create a Brand.
     * 
     * @return void
     */
    public function test_create_brand_as_manager() {
        Session::start();
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_MANAGER
        ]);

        Auth::login($user);

        $response = $this->actingAs($user)->post(route('brands.store'), [
            'brand_name' => 'Lorem ipsum',
            'brand_description' => 'Lorem ipsum dolor sit amet, cost',
            '_token' => Session::token(),
        ]);

        $this->assertDatabaseCount('brands', 1);
        
        $brand = \App\Models\Brand::first();
        $response->assertRedirect(route('brands.show', $brand));
    }

    /**
     * Test if a Staff cannot view Edit Brand page.
     * 
     * @return void
     */
    public function test_view_edit_brand_as_staff() {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF
        ]);

        $brand = \App\Models\Brand::factory()->create();

        $this->assertDatabaseCount('users', 1)
            ->assertDatabaseCount('brands', 1);

        Auth::login($user);

        $this->get(route('brands.edit', $brand))->assertStatus(403);
    }

    /**
     * Test if a Manager can view Edit Brand page.
     * 
     * @return void
     */
    public function test_view_edit_brand_as_manager() {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_MANAGER
        ]);

        $brand = \App\Models\Brand::factory()->create([
            'created_by_user' => $user->id,
            'updated_by_user' => $user->id
        ]);

        $this->assertDatabaseCount('users', 1)
            ->assertDatabaseCount('brands', 1);

        Auth::login($user);

        $this->get(route('brands.edit', $brand))->assertStatus(200);
    }

    public function test_edit_brand_as_staff() {
        Session::start();
        $this->seed(\Database\Seeders\RoleSeeder::class);
        
        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF
        ]);

        $brand = \App\Models\Brand::factory()->create();

        Auth::login($user);

        $response = $this->put(route('brands.update', $brand), [
            'brand_name' => 'Cheeky breeky iv damke!',
            '_token' => Session::token()
        ]);

        $this->assertTrue($brand->brand_name !== 'Cheeky breeky iv damke!');
        $response->assertStatus(403);
    }

    /**
     * Test if Manager can edit a Brand.
     * 
     * @return void
     */
    public function test_edit_brand_as_manager() {
        Session::start();
        $this->seed(\Database\Seeders\RoleSeeder::class);
        
        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_MANAGER
        ]);

        $brand = \App\Models\Brand::factory()->create();

        Auth::login($user);

        $response = $this->put(route('brands.update', $brand), [
            'brand_name' => 'Cheeky breeky iv damke!',
            'brand_description' => $brand->brand_description,
            '_token' => Session::token()
        ]);
    
        $this->assertEquals(
            \App\Models\Brand::find($brand->id)->first()->brand_name, 
            'Cheeky breeky iv damke!'
        );
        $response->assertStatus(302)
            ->assertRedirect(route('brands.edit', $brand));
    }

    /**
     * Test if a Staff cannot delete a Brand.
     * 
     * @return void
     */
    public function test_delete_brand_as_staff() {
        Session::start();
        $this->seed(\Database\Seeders\RoleSeeder::class);
        
        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF
        ]);

        $brand = \App\Models\Brand::factory()->create();

        $this->assertDatabaseCount('brands', 1);

        Auth::login($user);

        $response = $this->delete(route('brands.update', $brand), [
            '_token' => Session::token()
        ]);

        $this->assertDatabaseCount('brands', 1);
        $response->assertStatus(403);
    }

    /**
     * Test if a Manager can delete a Brand.
     * 
     * @return void
     */
    public function test_delete_brand_as_manager() {
        Session::start();
        $this->seed(\Database\Seeders\RoleSeeder::class);
        
        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_MANAGER
        ]);

        $brand = \App\Models\Brand::factory()->create();

        $this->assertDatabaseCount('brands', 1);

        Auth::login($user);

        $response = $this->delete(route('brands.update', $brand), [
            '_token' => Session::token()
        ]);

        $this->assertSoftDeleted($brand);
        $response->assertStatus(302)
            ->assertRedirect(route('brands.index'));
    }
}
