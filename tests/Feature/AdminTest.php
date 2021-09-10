<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Accounts to be used.
     *
     * @var \App\Models\User
     */
    protected $user;
    protected $manager;
    protected $admin;
    protected $example;

    /**
     * Number of accounts pre-created.
     *
     * @var int
     */
    protected $current_accounts;

    /**
     * Current password of accounts.
     *
     * @var string
     */
    protected $current_password = 'l33t1337';

    /**
     * Setting up test resources.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $this->user = \App\Models\User::factory()->create([
            'role'     => \App\Models\Role::ROLE_STAFF,
            'password' => Hash::make($this->current_password),
        ]);

        $this->example = \App\Models\User::factory()->create([
            'role'     => \App\Models\Role::ROLE_STAFF,
            'password' => Hash::make($this->current_password),
        ]);

        $this->manager = \App\Models\User::factory()->create([
            'role'     => \App\Models\Role::ROLE_MANAGER,
            'password' => Hash::make($this->current_password),
        ]);

        $this->admin = \App\Models\User::factory()->create([
            'password' => Hash::make($this->current_password),
        ]);

        $this->current_accounts = \App\Models\User::all()->count();
    }

    /**
     * Test if normal user cannot access to Admin Dashboard.
     *
     * @return void
     */
    public function test_access_admin_dashboard_as_admin()
    {
        $this->actingAs($this->user)
            ->get(route('users.index'))
            ->assertStatus(403);

        $this->actingAs($this->user)
            ->get(route('users.create'))
            ->assertStatus(403);

        $this->actingAs($this->user)
            ->get(route('users.edit', $this->user))
            ->assertStatus(403);

        $this->actingAs($this->manager)
            ->get(route('users.create'))
            ->assertStatus(403);

        $this->actingAs($this->manager)
            ->get(route('users.index'))
            ->assertStatus(403);

        $this->actingAs($this->manager)
            ->get(route('users.edit', $this->manager))
            ->assertStatus(403);

        $this->actingAs($this->admin)
            ->get(route('users.create'))
            ->assertStatus(200);

        $this->actingAs($this->admin)
            ->get(route('users.index'))
            ->assertStatus(200);

        $this->actingAs($this->admin)
            ->get(route('users.edit', $this->user))
            ->assertStatus(200);

        $this->actingAs($this->admin)
            ->get(route('users.edit', $this->manager))
            ->assertStatus(200);

        $this->actingAs($this->admin)
            ->get(route('users.edit', $this->admin))
            ->assertStatus(403);
    }

    /**
     * Test if we cannot create user with existing username / email.
     *
     * @return void
     */
    public function test_create_user_with_invalid_data()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $accounts = \App\Models\User::factory()->count(10)->create();

        // So basically this test will make sure username and email can not be
        // any of 100 usernames and emails above.
        foreach ($accounts as $account) {
            $password = \Illuminate\Support\Str::random(15);
            $this->actingAs($account)
                ->post(route('users.store'), [
                    'name'        => $account->name,
                    'username'    => $account->username,
                    'email'       => $account->email,
                    'password'    => $password,
                    're_password' => $password,
                    'role'        => \App\Models\Role::all()
                        ->except(\App\Models\Role::ROLE_ADMIN)
                        ->random()->id,
                ])
                ->assertSessionHasErrors([
                    'username',
                    'email',
                ]);
        }

        $email = $this->faker->unique()->safeEmail();

        $this->actingAs($accounts->random())
            ->post(route('users.store'), [
                'name'        => $this->faker->name(),
                'username'    => \Illuminate\Support\Str::random(6),
                'email'       => $email,
                'password'    => $password,
                're_password' => $password,
                'role'        => \App\Models\Role::ROLE_ADMIN,
            ])
            ->assertSessionHasErrors(['role']);

        $this->assertDatabaseCount('users', 10 + $this->current_accounts);
    }

    /**
     * Test if we can create new user with valid data.
     *
     * @return void
     */
    public function test_create_user_with_valid_data()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $email = $this->faker->unique()->safeEmail();
        $username = \Illuminate\Support\Str::random(6);
        $password = \Illuminate\Support\Str::random(15);

        $this->actingAs($this->admin)
            ->post(route('users.store'), [
                'name'        => \Illuminate\Support\Str::random(20),
                'username'    => $username,
                'email'       => $email,
                'password'    => $password,
                're_password' => $password,
                'role'        => \App\Models\Role::all()
                    ->except(\App\Models\Role::ROLE_ADMIN)
                    ->random()->id,
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('users', $this->current_accounts + 1);
        $this->assertTrue(auth()->attempt([
            'username' => $username,
            'password' => $password,
        ]));
    }

    /**
     * Test if we cannot create user with a non-admin account.
     *
     * @return void
     */
    public function test_create_user_with_non_admin()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $email = $this->faker->unique()->safeEmail();
        $username = \Illuminate\Support\Str::random(6);
        $password = \Illuminate\Support\Str::random(15);

        $formData = [
            'name'        => $this->faker->name(),
            'username'    => $username,
            'email'       => $email,
            'password'    => $password,
            're_password' => $password,
            'role'        => \App\Models\Role::all()
                ->except(\App\Models\Role::ROLE_ADMIN)
                ->random()->id,
        ];

        $this->actingAs($this->user)
            ->post(route('users.store'), $formData)
            ->assertStatus(403);

        $this->actingAs($this->manager)
            ->post(route('users.store'), $formData)
            ->assertStatus(403);

        $this->assertDatabaseCount('users', $this->current_accounts);
    }

    /**
     * Test user update with various data.
     *
     * @return void
     */
    public function test_update_user_role_by_admin_function()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->actingAs($this->user)
            ->from(route('users.edit', $this->example))
            ->put(route('users.update.role', $this->example), [
                'role' => \App\Models\Role::ROLE_MANAGER,
            ])
            ->assertStatus(403);

        $this->actingAs($this->manager)
            ->from(route('users.edit', $this->example))
            ->put(route('users.update.role', $this->example), [
                'role' => \App\Models\Role::ROLE_MANAGER,
            ])
            ->assertStatus(403);

        $this->assertEquals(
            $this->example->fresh()->role,
            \App\Models\Role::ROLE_STAFF
        );

        $this->actingAs($this->admin)
            ->from(route('users.edit', $this->example))
            ->put(route('users.update.role', $this->example), [
                'role' => \App\Models\Role::ROLE_ADMIN,
            ])
            ->assertSessionHasErrors();

        $this->assertEquals(
            $this->example->fresh()->role,
            \App\Models\Role::ROLE_STAFF
        );

        $this->actingAs($this->admin)
            ->from(route('users.edit', $this->example))
            ->put(route('users.update.role', $this->example), [
                'role' => \App\Models\Role::ROLE_MANAGER,
            ])
            ->assertSessionHasNoErrors();

        $this->assertEquals(
            $this->example->fresh()->role,
            \App\Models\Role::ROLE_MANAGER
        );
    }

    /**
     * Test if Admin can update User password.
     *
     * @return void
     */
    public function test_update_user_password_by_admin_function()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $new_password = '1337l33t';

        $this->actingAs($this->user)
            ->from(route('users.edit', $this->example))
            ->put(route('users.update.password', $this->example), [
                'new_password' => $new_password,
                're_password'  => $new_password,
            ])
            ->assertStatus(403);

        $this->actingAs($this->manager)
            ->from(route('users.edit', $this->example))
            ->put(route('users.update.password', $this->example), [
                'new_password' => $new_password,
                're_password'  => $new_password,
            ])
            ->assertStatus(403);

        $this->assertFalse(auth()->attempt([
            'username' => $this->example->username,
            'password' => $new_password,
        ]));

        $this->actingAs($this->admin)
            ->from(route('users.edit', $this->example))
            ->put(route('users.update.password', $this->example))
            ->assertSessionHasErrors();

        $this->actingAs($this->admin)
            ->from(route('users.edit', $this->example))
            ->put(route('users.update.password', $this->example), [
                'new_password' => '1337',
                're_password'  => $new_password,
            ])
            ->assertSessionHasErrors(['new_password', 're_password']);

        $this->actingAs($this->admin)
            ->from(route('users.edit', $this->example))
            ->put(route('users.update.password', $this->example), [
                'new_password' => $new_password,
                're_password'  => $new_password,
            ])
            ->assertSessionHasNoErrors();

        $this->assertTrue(auth()->attempt([
            'username' => $this->example->username,
            'password' => $new_password,
        ]));
    }

    /**
     * Test if non-admin user cannot delete user.
     *
     * @return void
     */
    public function test_delete_user_with_non_admin()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->actingAs($this->user)
            ->delete(route('users.destroy', $this->example))
            ->assertStatus(403);

        $this->actingAs($this->manager)
            ->delete(route('users.destroy', $this->example))
            ->assertStatus(403);

        $this->example->update(['role' => \App\Models\Role::ROLE_ADMIN]);

        $this->actingAs($this->admin)
            ->delete(route('users.destroy', $this->example))
            ->assertStatus(403);

        $this->assertDatabaseHas('users', [
            'id'         => $this->example->id,
            'deleted_at' => null,
        ]);
    }

    /**
     * Test if we can delete a user with an Admin account,
     * but data remains.
     *
     * @return void
     */
    public function test_delete_user_also_remove_relatives_records()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $brand = \App\Models\Brand::factory()->create([
            'created_by_user' => $this->user,
        ]);
        $bike = \App\Models\Bike::factory()->create([
            'created_by_user' => $this->user,
        ]);
        $order = \App\Models\Order::factory()->create([
            'created_by_user' => $this->user,
        ]);

        $order->bikes()->attach($bike->id, [
            'order_value'      => 1,
            'order_buy_price'  => $bike->bike_buy_price,
            'order_sell_price' => $bike->bike_sell_price,
        ]);

        $this->actingAs($this->admin)
            ->from(route('users.edit', $this->user))
            ->delete(route('users.destroy', $this->user))
            ->assertSessionHasNoErrors();

        $this->assertSoftDeleted($this->user);

        $this->get(route('brands.show', $brand))
            ->assertStatus(200)
            ->assertSee($this->user->nameAndUsername());

        $this->get(route('bikes.show', $bike))
            ->assertStatus(200)
            ->assertSee($this->user->nameAndUsername());

        $this->get(route('orders.show', $order))
            ->assertStatus(200)
            ->assertSee($this->user->nameAndUsername());
    }
}
