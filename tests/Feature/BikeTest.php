<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BikeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Resource to be used in testing.
     *
     * @var mixed
     */
    protected $user;
    protected $manager;
    protected $admin;
    protected $brands;
    protected $bikes;

    /**
     * Default models created.
     *
     * @var int
     */
    protected $created = 5;

    /**
     * Setting up testing resources.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $this->user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF,
        ]);

        $this->manager = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_MANAGER,
        ]);

        $this->admin = \App\Models\User::factory()->create();

        $this->brands = \App\Models\Brand::factory()
            ->count($this->created)
            ->create();

        $this->bikes = \App\Models\Bike::factory()
            ->count($this->created)
            ->create();
    }

    /**
     * Test if non-authenticated user cannot view bikes index page.
     *
     * @return void
     */
    public function test_view_bikes_index_as_not_authenticated_user()
    {
        $this->get(route('bikes.index'))
            ->assertRedirect(route('auth.login.index'));
    }

    /**
     * Test if authenticated user can view bikes index page.
     *
     * @return void
     */
    public function test_view_bikes_index_as_authenticated_user()
    {
        $this->actingAs($this->user)
            ->get(route('bikes.index'))
            ->assertStatus(200);
    }

    /**
     * Test if unauthenticated user cannot view any Bike.
     *
     * @return void
     */
    public function test_view_bike_show_as_unauthenticated_user()
    {
        $this->get(route('bikes.show', $this->bikes->random()))
            ->assertRedirect(route('auth.login.index'));
    }

    /**
     * Test if authenticated user can view any bike.
     *
     * @return void
     */
    public function test_view_bike_show_as_authenticated_user()
    {
        $bike = $this->bikes->random();

        $this->actingAs($this->user)
            ->get(route('bikes.show', $bike))
            ->assertStatus(200)
            ->assertSee($bike->brand->brand_name)
            ->assertSee($bike->bike_name);
    }

    /**
     * Test if Staff cannot view Create Bike page.
     *
     * @return void
     */
    public function test_view_create_bike_as_staff()
    {
        $this->actingAs($this->user)
            ->get(route('bikes.create'))
            ->assertStatus(403);
    }

    /**
     * Test if Manager can view Create Bike page.
     *
     * @return void
     */
    public function test_view_create_bike_as_manager()
    {
        $this->actingAs($this->manager)
            ->get(route('bikes.create'))
            ->assertStatus(200);

        // Check if all brands appear on the selection.
        $view = $this->view('content.bike.create', [
            'brands' => $this->brands,
        ]);
        foreach ($this->brands as $brand) {
            $view->assertSee($brand->brand_name);
        }
    }

    /**
     * Test if Staff cannot create Bike.
     *
     * @return void
     */
    public function test_create_bike_as_staff()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->actingAs($this->user)
            ->post(route('bikes.store', [
                'brand_id'         => $this->brands->random()->id,
                'bike_name'        => 'Cheeki breeki iv domke!',
                'bike_description' => 'Cheeki breeki iv domke!',
                'bike_stock'       => 1337,
                'bike_buy_price'   => 1337,
                'bike_sell_price'  => 1337,
            ]))
            ->assertStatus(403);

        $this->assertDatabaseCount('bikes', $this->created);
    }

    /**
     * Test if Manager can create new Bike.
     *
     * @return void
     */
    public function test_create_bike_as_manager()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $brand = $this->brands->random();

        $formData = [
            'brand_id'         => $brand->id,
            'bike_name'        => 'Cheeki breeki',
            'bike_description' => 'Cheeki breeki iv domke!',
            'bike_stock'       => 1337,
            'bike_buy_price'   => 1337,
            'bike_sell_price'  => 1337,
        ];

        $this->actingAs($this->manager)
            ->followingRedirects()
            ->from(route('bikes.create'))
            ->post(route('bikes.store', $formData))
            ->assertSee($brand->brand_name)
            ->assertSee($formData['bike_name'])
            ->assertSee($formData['bike_description'])
            ->assertSee($formData['bike_stock']);

        $this->assertDatabaseCount('bikes', $this->created + 1);
    }

    /**
     * Test if invalid data cannot be used to create Bike.
     *
     * @return void
     */
    public function test_create_bike_with_invalid_data()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $brand = $this->brands->random();

        // Brand doesn't exist.
        $formData = [
            'brand_id'         => 1337,
            'bike_name'        => \Illuminate\Support\Str::random(10),
            'bike_description' => \Illuminate\Support\Str::random(100),
            'bike_stock'       => 1337,
            'bike_buy_price'   => 1337,
            'bike_sell_price'  => 1337,
        ];

        $this->actingAs($this->manager)
            ->from(route('bikes.create'))
            ->post(route('bikes.store'), $formData)
            ->assertSessionHasErrors(['brand_id']);

        // Name and description are too long.
        $formData['brand_id'] = $brand->id;
        $formData['bike_name'] = \Illuminate\Support\Str::random(1337);
        $formData['bike_description'] = \Illuminate\Support\Str::random(1337);

        $this->actingAs($this->manager)
            ->from(route('bikes.create'))
            ->post(route('bikes.store'), $formData)
            ->assertSessionHasErrors([
                'bike_name',
                'bike_description',
            ]);

        // Invalid integers.
        $formData['bike_stock'] = -1;
        $formData['bike_buy_price'] = -1;
        $formData['bike_sell_price'] = -1;

        $this->actingAs($this->manager)
            ->from(route('bikes.create'))
            ->post(route('bikes.store'), $formData)
            ->assertSessionHasErrors([
                'bike_stock',
                'bike_buy_price',
                'bike_sell_price',
            ]);

        // No data
        $this->actingAs($this->manager)
            ->from(route('bikes.create'))
            ->post(route('bikes.store'))
            ->assertSessionHasErrors([
                'brand_id',
                'bike_name',
                'bike_description',
                'bike_stock',
                'bike_buy_price',
                'bike_sell_price',
            ]);

        // Afterall, no bikes should be created.
        $this->assertDatabaseCount('bikes', $this->created);
    }

    /**
     * Test if Staff cannot view Edit Bike page.
     *
     * @return void
     */
    public function test_view_edit_bike_as_staff()
    {
        $this->actingAs($this->user)
            ->get(route('bikes.edit', $this->bikes->random()))
            ->assertStatus(403);
    }

    /**
     * Test if Manager can view Edit Bike page.
     *
     * @return void
     */
    public function test_view_edit_bike_as_manager()
    {
        $bike = $this->bikes->random();

        $this->actingAs($this->manager)
            ->get(route('bikes.edit', $bike))
            ->assertStatus(200);

        $view = $this->view('content.bike.update', [
            'brands' => $this->brands,
            'bike'   => $bike,
        ]);
        $view->assertSee($bike->bike_name)
            ->assertSee($bike->bike_description);

        foreach ($this->brands as $brand) {
            $view->assertSee($brand->brand_name);
        }
    }

    /**
     * Test if Staff cannot edit Bike.
     *
     * @return void
     */
    public function test_edit_bike_as_staff()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $bike = $this->bikes->random();

        $formData = [
            'brand_id'         => $bike->brand->id,
            'bike_name'        => $bike->bike_name,
            'bike_description' => $bike->bike_description,
            'bike_stock'       => $bike->bike_stock + 1337,
            'bike_buy_price'   => $bike->bike_buy_price,
            'bike_sell_price'  => $bike->bike_sell_price,
        ];

        $this->actingAs($this->user)
            ->put(route('bikes.update', $bike), $formData)
            ->assertStatus(403);
    }

    /**
     * Test if Manager can edit Bike.
     *
     * @return void
     */
    public function test_edit_bike_as_manager()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $newStock = random_int(1337, 7331);

        $bike = $this->bikes->random();

        $formData = [
            'brand_id'         => $bike->brand->id,
            'bike_name'        => $bike->bike_name,
            'bike_description' => $bike->bike_description,
            'bike_stock'       => $newStock,
            'bike_buy_price'   => $bike->bike_buy_price,
            'bike_sell_price'  => $bike->bike_sell_price,
        ];

        $this->actingAs($this->manager)
            ->followingRedirects()
            ->from(route('bikes.edit', $bike))
            ->put(route('bikes.update', $bike), $formData)
            ->assertSee($newStock);

        $this->actingAs($this->user)
            ->get(route('bikes.show', $bike))
            ->assertSee($newStock)
            ->assertSee($this->manager->nameAndUsername());

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
    public function test_edit_bike_with_invalid_data()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $bike = $this->bikes->random();

        // Brand doesn't exist.
        $formData = [
            'brand_id'         => 1337,
            'bike_name'        => \Illuminate\Support\Str::random(10),
            'bike_description' => \Illuminate\Support\Str::random(100),
            'bike_stock'       => 1337,
            'bike_buy_price'   => 1337,
            'bike_sell_price'  => 1337,
        ];

        $this->actingAs($this->manager)
            ->from(route('bikes.edit', $bike))
            ->put(route('bikes.update', $bike), $formData)
            ->assertSessionHasErrors(['brand_id']);

        // Name and description are too long.
        $formData['brand_id'] = $bike->brand->id;
        $formData['bike_name'] = \Illuminate\Support\Str::random(1337);
        $formData['bike_description'] = \Illuminate\Support\Str::random(1337);

        $this->actingAs($this->manager)
            ->from(route('bikes.edit', $bike))
            ->put(route('bikes.update', $bike), $formData)
            ->assertSessionHasErrors([
                'bike_name',
                'bike_description',
            ]);

        // Invalid integers.
        $formData['bike_stock'] = -1;
        $formData['bike_buy_price'] = -1;
        $formData['bike_sell_price'] = -1;

        $this->actingAs($this->manager)
            ->from(route('bikes.edit', $bike))
            ->put(route('bikes.update', $bike), $formData)
            ->assertSessionHasErrors([
                'bike_stock',
                'bike_buy_price',
                'bike_sell_price',
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
                'bike_sell_price',
            ]);
    }

    /**
     * Test if Staff cannot delete Bike.
     *
     * @return void
     */
    public function test_delete_bike_as_staff()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->actingAs($this->user)
            ->delete(route('bikes.update', $this->bikes->random()))
            ->assertStatus(403);
    }

    /**
     * Test if Manager can delete Bike.
     *
     * @return void
     */
    public function test_delete_bike_as_manager()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $bike = $this->bikes->random();

        $this->actingAs($this->manager)
            ->followingRedirects()
            ->from(route('bikes.edit', $bike))
            ->delete(route('bikes.update', $bike))
            ->assertDontSee($bike->brand_name);

        $this->assertSoftDeleted($bike);
    }
}
