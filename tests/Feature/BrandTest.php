<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BrandTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Resources during test.
     * 
     * @var mixed
     */
    protected $user, $manager, $admin;
    protected $brand;

    /**
     * Models created by default.
     * 
     * @var int
     */
    protected $created = 1;

    /**
     * Setting up all test resources.
     * 
     * @return void
     */
    public function setUp() : void  {
        parent::setUp();

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $this->user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF
        ]);

        $this->manager = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_MANAGER
        ]);

        $this->admin = \App\Models\User::factory()->create();

        $this->brand = \App\Models\Brand::factory()->create();
    }

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
    public function test_view_brands_index_as_authenticated_user() {
        // Anyone can view every Brand.
        $this->actingAs($this->user)
            ->get(route('brands.index'))
            ->assertStatus(200);
    }

    /**
     * Test if a non-authenticated user cannot view a brand.
     * 
     * @return void
     */
    public function test_view_brands_show_as_non_authenticated_user() {
        $this->get(route('brands.show', $this->brand))
            ->assertRedirect(route('auth.login.index'));
    }

    /**
     * Test if any authenticated user can view any brand.
     *
     * @return void
     */
    public function test_view_brands_show_as_authenticated_user() {
        $this->actingAs($this->user)
            ->get(route('brands.show', $this->brand))
            ->assertStatus(200)
            ->assertSee($this->brand->brand_name)
            ->assertSee($this->brand->brand_description);
    }

    /**
     * Test if a staff can view Create Brand page.
     * 
     * @return void
     */
    public function test_view_create_brand_as_staff() {
        $this->actingAs($this->user)
            ->get(route('brands.create'))
            ->assertStatus(403);
    }

    /**
     * Test if a manager can view Create Brand page.
     * 
     * @return void
     */
    public function test_view_create_brand_as_manager() {
        $this->actingAs($this->manager)
            ->get(route('brands.create'))
            ->assertStatus(200);
    }

    /**
     * Test if a Staff cannot create a Brand.
     * 
     * @return void
     */
    public function test_create_brand_as_staff() {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $this->actingAs($this->user)
            ->post(route('brands.store'), [
                'brand_name' => 'Lorem ipsum',
                'brand_description' => 'Lorem ipsum dolor sit amet, cost',
            ])
            ->assertStatus(403);

        $this->assertDatabaseCount('brands', $this->created);
    }

    /**
     * Test if a Manager can create a Brand.
     * 
     * @return void
     */
    public function test_create_brand_as_manager() {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $formData = [
            'brand_name' => 'Lorem ipsum',
            'brand_description' => 'Lorem ipsum dolor sit amet, cost',
        ];

        $this->actingAs($this->manager)
            ->followingRedirects()
            ->from(route('brands.create'))
            ->post(route('brands.store'), $formData)
            ->assertSee($formData['brand_name'])
            ->assertSee($formData['brand_description']);

        $this->assertDatabaseCount('brands', $this->created + 1);
    }

    /**
     * Test create Brand with invalid data.
     * 
     * @return void
     */
    public function test_create_brand_with_invalid_data() {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        // Brand name too long.
        $formData = [
            'brand_name' => \Illuminate\Support\Str::random(1337),
            'brand_description' => \Illuminate\Support\Str::random(100),
        ];

        $this->actingAs($this->manager)
            ->from(route('brands.create'))
            ->post(route('brands.store'), $formData)
            ->assertSessionHasErrors(['brand_name']);

        // Brand name too sort.
        $formData['brand_name'] = \Illuminate\Support\Str::random(5);

        $this->actingAs($this->manager)
            ->from(route('brands.create'))
            ->post(route('brands.store'), $formData)
            ->assertSessionHasErrors(['brand_name']);

        // Brand description is too long.
        $formData['brand_name'] = \Illuminate\Support\Str::random(10);
        $formData['brand_description'] = \Illuminate\Support\Str::random(1337);

        $this->actingAs($this->manager)
            ->from(route('brands.create'))
            ->post(route('brands.store'), $formData)
            ->assertSessionHasErrors(['brand_description']);

        // Brand description is too short.
        $formData['brand_description'] = \Illuminate\Support\Str::random(3);

        $this->actingAs($this->manager)
            ->from(route('brands.create'))
            ->post(route('brands.store'), $formData)
            ->assertSessionHasErrors(['brand_description']);

        // Both are too long.
        $formData['brand_name'] = \Illuminate\Support\Str::random(1337);
        $formData['brand_description'] = \Illuminate\Support\Str::random(1337);

        $this->actingAs($this->manager)
            ->from(route('brands.create'))
            ->post(route('brands.store'), $formData)
            ->assertSessionHasErrors(['brand_name', 'brand_description']);

        // No data.
        $this->actingAs($this->manager)
            ->from(route('brands.create'))
            ->post(route('brands.store'))
            ->assertSessionHasErrors(['brand_name']);

        // At the end, no new brands should be created.
        $this->assertDatabaseCount('brands', $this->created);
    }

    /**
     * Test if a Staff cannot view Edit Brand page.
     * 
     * @return void
     */
    public function test_view_edit_brand_as_staff() {
        $this->actingAs($this->user)
            ->get(route('brands.edit', $this->brand))
            ->assertStatus(403);
    }

    /**
     * Test if a Manager can view Edit Brand page.
     * 
     * @return void
     */
    public function test_view_edit_brand_as_manager() {
        $this->actingAs($this->manager)
            ->get(route('brands.edit', $this->brand))
            ->assertStatus(200)
            ->assertSee($this->brand->brand_name)
            ->assertSee($this->brand->brand_description);
    }

    /**
     * Test if a Staff cannot edit a Brand.
     * 
     * @return void
     */
    public function test_edit_brand_as_staff() {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->actingAs($this->user)
            ->put(route('brands.update', $this->brand), [
                'brand_name' => 'Cheeky breeky iv damke!',
            ])
            ->assertStatus(403);

        $this->assertNotEquals(
            $this->brand->fresh()->brand_name,
            'Cheeky breeky iv damke!'
        );
    }

    /**
     * Test if Manager can edit a Brand.
     * 
     * @return void
     */
    public function test_edit_brand_as_manager() {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $formData = [
            'brand_name' => 'Cheeky breeky iv damke!',
            'brand_description' => 'Lorem ipsum dolor sit amet',
        ];

        $this->actingAs($this->manager)
            ->followingRedirects()
            ->from(route('brands.edit', $this->brand))
            ->put(route('brands.update', $this->brand), $formData)
            ->assertSee($formData['brand_name'])
            ->assertSee($formData['brand_description']);

        $this->assertEquals(
            $this->brand->fresh()->brand_name,
            $formData['brand_name']
        );

        $this->assertEquals(
            $this->brand->fresh()->brand_description, 
            $formData['brand_description']
        );

        $this->actingAs($this->user)
            ->get(route('brands.show', $this->brand))
            ->assertSee($this->manager->nameAndUsername());
    }

    /**
     * Test edit Brand with invalid data.
     * 
     * @return void
     */
    public function test_edit_brand_with_invalid_data() {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        // Brand name too long.
        $formData = [
            'brand_name' => \Illuminate\Support\Str::random(1337),
            'brand_description' => $this->brand->brand_description,
        ];

        $this->actingAs($this->manager)
            ->from(route('brands.edit', $this->brand))
            ->put(route('brands.update', $this->brand), $formData)
            ->assertSessionHasErrors(['brand_name']);

        $this->assertNotEquals(
            $this->brand->fresh()->brand_name,
            $formData['brand_name']
        );

        // Brand name too sort.
        $formData['brand_name'] = \Illuminate\Support\Str::random(5);

        $this->actingAs($this->manager)
            ->from(route('brands.edit', $this->brand))
            ->put(route('brands.update', $this->brand), $formData)
            ->assertSessionHasErrors(['brand_name']);

        $this->assertNotEquals(
            $this->brand->fresh()->brand_name,
            $formData['brand_name']
        );

        // Brand description is too long.
        $formData['brand_name'] = \Illuminate\Support\Str::random(10);
        $formData['brand_description'] = \Illuminate\Support\Str::random(1337);

        $this->actingAs($this->manager)
            ->from(route('brands.edit', $this->brand))
            ->put(route('brands.update', $this->brand), $formData)
            ->assertSessionHasErrors(['brand_description']);

        $this->assertNotEquals(
            $this->brand->fresh()->brand_description,
            $formData['brand_description']
        );

        // Brand description is too short.
        $formData['brand_description'] = \Illuminate\Support\Str::random(3);

        $this->actingAs($this->manager)
            ->from(route('brands.edit', $this->brand))
            ->put(route('brands.update', $this->brand), $formData)
            ->assertSessionHasErrors(['brand_description']);

        $this->assertNotEquals(
            $this->brand->fresh()->brand_description,
            $formData['brand_description']
        );

        // Both are too long.
        $formData['brand_name'] = \Illuminate\Support\Str::random(1337);
        $formData['brand_description'] = \Illuminate\Support\Str::random(1337);

        $this->actingAs($this->manager)
            ->from(route('brands.edit', $this->brand))
            ->put(route('brands.update', $this->brand), $formData)
            ->assertSessionHasErrors(['brand_name', 'brand_description']);

        $this->assertNotEquals(
            $this->brand->fresh()->brand_name,
            $formData['brand_name']
        );

        $this->assertNotEquals(
            $this->brand->fresh()->brand_description,
            $formData['brand_description']
        );

        // Nothing sent
        $this->actingAs($this->manager)
            ->from(route('brands.edit', $this->brand))
            ->put(route('brands.update', $this->brand))
            ->assertSessionHasErrors(['brand_name']);
    }

    /**
     * Test if a Staff cannot delete a Brand.
     * 
     * @return void
     */
    public function test_delete_brand_as_staff() {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->actingAs($this->user)
            ->delete(route('brands.update', $this->brand))
            ->assertStatus(403);

        $this->assertDatabaseCount('brands', $this->created);
    }

    /**
     * Test if a Manager can delete a Brand.
     * 
     * @return void
     */
    public function test_delete_brand_as_manager() {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $bikes = \App\Models\Bike::factory()->count(100)->create();

        $this->actingAs($this->manager)
            ->followingRedirects()
            ->from(route('brands.edit', $this->brand))
            ->delete(route('brands.update', $this->brand))
            ->assertDontSee($this->brand->brand_name);

        $this->assertSoftDeleted($this->brand);

        // All bikes also deleted, too.
        foreach ($bikes as $bike) {
            $this->assertSoftDeleted($bike);
        }
    }
}
