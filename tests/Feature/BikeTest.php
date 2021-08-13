<?php

namespace Tests\Feature;

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

        $this->actingAs($user)->get(route('bikes.index'))->assertStatus(200);
    }

    /**
     * Test if unauthenticated user cannot view any Bike.
     * 
     * @return void
     */
    public function test_view_bike_show_as_unauthenticated_user() {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_ADMIN
        ]);

        $brand = \App\Models\Brand::factory()->create();

        $bike = \App\Models\Bike::factory()->create();

        $this->get(route('bikes.show', $bike))
            ->assertRedirect(route('auth.login.index'));
    }

    /**
     * Test if authenticated user can view any bike.
     * 
     * @return void
     */
    public function test_view_bike_show_as_authenticated_user() {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF
        ]);

        $brand = \App\Models\Brand::factory()->create();

        $bike = \App\Models\Bike::factory()->create();

        $this->actingAs($user)
            ->get(route('bikes.show', $bike))
            ->assertStatus(200)
            ->assertSee($brand->brand_name)
            ->assertSee($bike->bike_name);
    }

    /**
     * Test if Staff cannot view Create Bike page.
     * 
     * @return void
     */
    public function test_view_create_bike_as_staff() {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF
        ]);
        
        $this->actingAs($user)->get(route('bikes.create'))->assertStatus(403);
    }

    /**
     * Test if Manager can view Create Bike page.
     * 
     * @return void
     */
    public function test_view_create_bike_as_manager() {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_MANAGER
        ]);

        $brands = \App\Models\Brand::factory()->count(100)->create();

        $this->actingAs($user)->get(route('bikes.create'))->assertStatus(200);

        // Check if all brands appear on the selection.
        $view = $this->view('content.bike.create', compact('brands'));
        foreach ($brands as $brand) {
            $view->assertSee($brand->brand_name);
        }
    }

    /**
     * Test if Staff cannot create Bike.
     * 
     * @return void
     */
    public function test_create_bike_as_staff() {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF
        ]);

        $brand = \App\Models\Brand::factory()->create();

        $this->actingAs($user)
            ->post(route('bikes.store', [
                'brand_id' => $brand->id,
                'bike_name' => 'Cheeki breeki iv domke!',
                'bike_description' => 'Cheeki breeki iv domke!',
                'bike_stock' => 1337,
                'bike_buy_price' => 1337,
                'bike_sell_price' => 1337,
            ]))
            ->assertStatus(403);

        $this->assertDatabaseCount('bikes', 0);
    }

    /**
     * Test if Manager can create new Bike.
     * 
     * @return void
     */
    public function test_create_bike_as_manager() {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_MANAGER
        ]);

        $brand = \App\Models\Brand::factory()->create();

        $formData = [
            'brand_id' => $brand->id,
            'bike_name' => 'Cheeki breeki',
            'bike_description' => 'Cheeki breeki iv domke!',
            'bike_stock' => 1337,
            'bike_buy_price' => 1337,
            'bike_sell_price' => 1337,
        ];

        $this->actingAs($user)
            ->followingRedirects()
            ->from(route('bikes.create'))
            ->post(route('bikes.store', $formData))
            ->assertSee($formData['bike_name'])
            ->assertSee($formData['bike_description'])
            ->assertSee($formData['bike_stock']);
        
        $this->assertDatabaseCount('bikes', 1);
    }

    /**
     * Test if invalid data cannot be used to create Bike.
     * 
     * @return void
     */
    public function test_create_bike_with_invalid_data() {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_ADMIN
        ]);

        $brand = \App\Models\Brand::factory()->create();

        // Brand doesn't exist.
        $formData = [
            'brand_id' => 1337,
            'bike_name' => \Illuminate\Support\Str::random(10),
            'bike_description' => \Illuminate\Support\Str::random(100),
            'bike_stock' => 1337,
            'bike_buy_price' => 1337,
            'bike_sell_price' => 1337
        ];

        $this->actingAs($user)
            ->from(route('bikes.create'))
            ->post(route('bikes.store'), $formData)
            ->assertSessionHasErrors(['brand_id']);

        // Name and description are too long.
        $formData['brand_id'] = $brand->id;
        $formData['bike_name'] = \Illuminate\Support\Str::random(1337);
        $formData['bike_description'] = \Illuminate\Support\Str::random(1337);

        $this->actingAs($user)
            ->from(route('bikes.create'))
            ->post(route('bikes.store'), $formData)
            ->assertSessionHasErrors([
                'bike_name',
                'bike_description'
            ]);

        // Invalid integers.
        $formData['bike_stock'] = -1;
        $formData['bike_buy_price'] = -1;
        $formData['bike_sell_price'] = -1;

        $this->actingAs($user)
            ->from(route('bikes.create'))
            ->post(route('bikes.store'), $formData)
            ->assertSessionHasErrors([
                'bike_stock',
                'bike_buy_price',
                'bike_sell_price'
            ]);

        // No data
        $this->actingAs($user)
            ->from(route('bikes.create'))
            ->post(route('bikes.store'))
            ->assertSessionHasErrors([
                'brand_id',
                'bike_name',
                'bike_description',
                'bike_stock',
                'bike_buy_price',
                'bike_sell_price'
            ]);
        
        // Afterall, no bikes should be created.
        $this->assertDatabaseCount('bikes', 0);
    }

    /**
     * Test if Staff cannot view Edit Bike page.
     * 
     * @return void
     */
    public function test_view_edit_bike_as_staff() {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF
        ]);

        $brand = \App\Models\Brand::factory()->create();

        $bike = \App\Models\Bike::factory()->create();

        $this->actingAs($user)
            ->get(route('bikes.edit', $bike))->assertStatus(403);
    }

    /**
     * Test if Manager can view Edit Bike page.
     * 
     * @return void
     */
    public function test_view_edit_bike_as_manager() {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_MANAGER
        ]);

        $brands = \App\Models\Brand::factory()->count(100)->create();

        $bike = \App\Models\Bike::factory()->create();

        $this->actingAs($user)
            ->get(route('bikes.edit', $bike))
            ->assertStatus(200);

        $view = $this->view('content.bike.update', compact('brands', 'bike'));
        $view->assertSee($bike->bike_name)
            ->assertSee($bike->bike_description);

        foreach ($brands as $brand) {
            $view->assertSee($brand->brand_name);
        }
    }

    /**
     * Test if Staff cannot edit Bike.
     * 
     * @return void
     */
    public function test_edit_bike_as_staff() {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF
        ]);

        $brand = \App\Models\Brand::factory()->create();

        $bike = \App\Models\Bike::factory()->create();

        $formData = [
            'brand_id' => $brand->id,
            'bike_name' => $bike->bike_name,
            'bike_description' => $bike->bike_description,
            'bike_stock' => $bike->bike_stock + 1337,
            'bike_buy_price' => $bike->bike_buy_price,
            'bike_sell_price' => $bike->bike_sell_price,
        ];

        $this->actingAs($user)
            ->put(route('bikes.update', $bike), $formData)
            ->assertStatus(403);
    }

    /**
     * Test if Manager can edit Bike
     *
     * @return void
     */
    public function test_edit_bike_as_manager() {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_MANAGER
        ]);

        $brand = \App\Models\Brand::factory()->create();

        $bike = \App\Models\Bike::factory()->create();

        $newStock = random_int(1337, 7331);

        $formData = [
            'brand_id' => $brand->id,
            'bike_name' => $bike->bike_name,
            'bike_description' => $bike->bike_description,
            'bike_stock' => $newStock,
            'bike_buy_price' => $bike->bike_buy_price,
            'bike_sell_price' => $bike->bike_sell_price,
        ];

        $this->actingAs($user)
            ->followingRedirects()
            ->from(route('bikes.edit', $bike))
            ->put(route('bikes.update', $bike), $formData)
            ->assertSee($newStock);

        $this->assertEquals(
            $bike->fresh()->bike_stock, 
            $newStock
        );
    }

    /**
     * Test if invalid data cannot be used to edit a Bike.
     * 
     * @return void
     */
    public function test_edit_bike_with_invalid_data() {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_ADMIN
        ]);

        $brand = \App\Models\Brand::factory()->create();

        $bike = \App\Models\Bike::factory()->create();

        // Brand doesn't exist.
        $formData = [
            'brand_id' => 1337,
            'bike_name' => \Illuminate\Support\Str::random(10),
            'bike_description' => \Illuminate\Support\Str::random(100),
            'bike_stock' => 1337,
            'bike_buy_price' => 1337,
            'bike_sell_price' => 1337,
        ];

        $this->actingAs($user)
            ->from(route('bikes.edit', $bike))
            ->put(route('bikes.update', $bike), $formData)
            ->assertSessionHasErrors(['brand_id']);

        // Name and description are too long.
        $formData['brand_id'] = $brand->id;
        $formData['bike_name'] = \Illuminate\Support\Str::random(1337);
        $formData['bike_description'] = \Illuminate\Support\Str::random(1337);

        $this->actingAs($user)
            ->from(route('bikes.edit', $bike))
            ->put(route('bikes.update', $bike), $formData)
            ->assertSessionHasErrors([
                'bike_name',
                'bike_description'
            ]);

        // Invalid integers.
        $formData['bike_stock'] = -1;
        $formData['bike_buy_price'] = -1;
        $formData['bike_sell_price'] = -1;

        $this->actingAs($user)
            ->from(route('bikes.edit', $bike))
            ->put(route('bikes.update', $bike), $formData)
            ->assertSessionHasErrors([
                'bike_stock',
                'bike_buy_price',
                'bike_sell_price'
            ]);

        // No data
        $this->from(route('bikes.edit', $bike))
            ->put(route('bikes.update', $bike))
            ->assertSessionHasErrors([
                'brand_id',
                'bike_name',
                'bike_description',
                'bike_stock',
                'bike_buy_price',
                'bike_sell_price'
            ]);
    }

    /**
     * Test if Staff cannot delete Bike.
     * 
     * @return void
     */
    public function test_delete_bike_as_staff() {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF
        ]);

        $brand = \App\Models\Brand::factory()->create();

        $bike = \App\Models\Bike::factory()->create();
        
        $this->actingAs($user)
            ->delete(route('bikes.update', $bike))
            ->assertStatus(403);
    }

    /**
     * Test if Manager can delete Bike.
     * 
     * @return void
     */
    public function test_delete_bike_as_manager() {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_MANAGER
        ]);

        $brand = \App\Models\Brand::factory()->create();

        $bike = \App\Models\Bike::factory()->create();
        
        $this->actingAs($user)
            ->followingRedirects()
            ->from(route('bikes.edit', $bike))
            ->delete(route('bikes.update', $bike))
            ->assertDontSee($bike->brand_name);

        $this->assertSoftDeleted($bike);
    }
}
