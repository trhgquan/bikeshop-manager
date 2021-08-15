<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test if normal user cannot access to Admin Dashboard.
     *
     * @return void
     */
    public function test_access_admin_dashboard_as_admin() {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $staff = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF
        ]);
        $manager = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_MANAGER
        ]);
        $admin = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_ADMIN
        ]);

        $this->actingAs($staff)
            ->get(route('users.index'))
            ->assertStatus(403);

        $this->actingAs($staff)
            ->get(route('users.create'))
            ->assertStatus(403);

        $this->actingAs($staff)
            ->get(route('users.edit', $staff))
            ->assertStatus(403);

        $this->actingAs($manager)
            ->get(route('users.create'))
            ->assertStatus(403);

        $this->actingAs($manager)
            ->get(route('users.index'))
            ->assertStatus(403);

        $this->actingAs($manager)
            ->get(route('users.edit', $manager))
            ->assertStatus(403);

        $this->actingAs($admin)
            ->get(route('users.create'))
            ->assertStatus(200);

        $this->actingAs($admin)
            ->get(route('users.index'))
            ->assertStatus(200);

        $this->actingAs($admin)
            ->get(route('users.edit', $staff))
            ->assertStatus(200);

        $this->actingAs($admin)
            ->get(route('users.edit', $manager))
            ->assertStatus(200);

        $this->actingAs($admin)
            ->get(route('users.edit', $admin))
            ->assertStatus(403);
    }

    /**
     * Test if we cannot create user with existing username / email.
     * 
     * @return void
     */
    public function test_create_user_with_invalid_data() {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $accounts = \App\Models\User::factory()->count(10)->create();

        // So basically this test will make sure username and email can not be 
        // any of 100 usernames and emails above.
        foreach ($accounts as $account) {
            $password = \Illuminate\Support\Str::random(15);
            $this->actingAs($account)
                ->post(route('users.store'), [
                    'name' => $account->name,
                    'username' => $account->username,
                    'email' => $account->email,
                    'password' => $password,
                    're_password' => $password,
                    'role' => \App\Models\Role::all()
                        ->except(\App\Models\Role::ROLE_ADMIN)
                        ->random()->id
                ])
                ->assertSessionHasErrors([
                    'username',
                    'email'
                ]);
        }

        $email = $this->faker->unique()->safeEmail();

        $this->actingAs($accounts->random())
            ->post(route('users.store'), [
                'name' => $this->faker->name(),
                'username' => \Illuminate\Support\Str::random(6),
                'email' => $email,
                'password' => $password,
                're_password' => $password,
                'role' => \App\Models\Role::ROLE_ADMIN
            ])
            ->assertSessionHasErrors(['role']);
        
        $this->assertDatabaseCount('users', 10);
    }

    /**
     * Test if we can create new user with valid data.
     * 
     * @return void
     */
    public function test_create_user_with_valid_data() {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $admin = \App\Models\User::factory()->create();
        $email = $this->faker->unique()->safeEmail();
        $username = \Illuminate\Support\Str::random(6);
        $password = \Illuminate\Support\Str::random(15);

        $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => \Illuminate\Support\Str::random(20),
                'username' => $username,
                'email' => $email,
                'password' => $password,
                're_password' => $password,
                'role' => \App\Models\Role::all()
                    ->except(\App\Models\Role::ROLE_ADMIN)
                    ->random()->id
            ])
            ->assertSessionHasNoErrors();
        
        $this->assertDatabaseCount('users', 2);
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
    public function test_create_user_with_non_admin() {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $staff = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF
        ]);
        $manager = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_MANAGER
        ]);

        $email = $this->faker->unique()->safeEmail();
        $username = \Illuminate\Support\Str::random(6);
        $password = \Illuminate\Support\Str::random(15);

        $formData = [
            'name' => $this->faker->name(),
            'username' => $username,
            'email' => $email,
            'password' => $password,
            're_password' => $password,
            'role' => \App\Models\Role::all()
                ->except(\App\Models\Role::ROLE_ADMIN)
                ->random()->id
        ];

        $this->actingAs($staff)
            ->post(route('users.store'), $formData)
            ->assertStatus(403);
        
        $this->actingAs($manager)
            ->post(route('users.store'), $formData)
            ->assertStatus(403);

        $this->assertDatabaseCount('users', 2);
    }

    /**
     * Test user update with various data.
     * 
     * @return void
     */
    public function test_update_user_role_by_admin_function() {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $staff = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF
        ]);
        $manager = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_MANAGER
        ]);
        $admin = \App\Models\User::factory()->create();
        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF
        ]);
        
        $this->actingAs($staff)
            ->from(route('users.edit', $user))
            ->put(route('users.update', $user), [
                'role' => \App\Models\Role::ROLE_MANAGER
            ])
            ->assertStatus(403);

        $this->actingAs($manager)
            ->from(route('users.edit', $user))
            ->put(route('users.update', $user), [
                'role' => \App\Models\Role::ROLE_MANAGER
            ])
            ->assertStatus(403);

        $this->assertEquals(
            $user->fresh()->role,
            \App\Models\Role::ROLE_STAFF
        );
        
        $this->actingAs($admin)
            ->from(route('users.edit', $user))
            ->put(route('users.update', $user), [
                'role' => \App\Models\Role::ROLE_ADMIN
            ])
            ->assertSessionHasErrors();
        
        $this->assertEquals(
            $user->fresh()->role,
            \App\Models\Role::ROLE_STAFF
        );

        $this->actingAs($admin)
            ->from(route('users.edit', $user))
            ->put(route('users.update', $user), [
                'role' => \App\Models\Role::ROLE_MANAGER
            ])
            ->assertSessionHasNoErrors();
        
        $this->assertEquals(
            $user->fresh()->role,
            \App\Models\Role::ROLE_MANAGER
        );
    }

    public function test_update_user_password_by_admin_function() {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $staff = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF
        ]);
        $manager = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_MANAGER
        ]);
        $admin = \App\Models\User::factory()->create();
        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF
        ]);
        
        $new_password = 'l33t1337';

        $this->actingAs($staff)
            ->from(route('users.edit', $user))
            ->put(route('users.update', $user), [
                'new_password' => $new_password,
                're_password' => $new_password
            ])
            ->assertStatus(403);

        $this->actingAs($manager)
            ->from(route('users.edit', $user))
            ->put(route('users.update', $user), [
                'new_password' => $new_password,
                're_password' => $new_password
            ])
            ->assertStatus(403);

        $this->assertFalse(auth()->attempt([
            'username' => $user->username,
            'password' => $new_password
        ]));
        
        $this->actingAs($admin)
            ->from(route('users.edit', $user))
            ->put(route('users.update', $user), [
                'role' => \App\Models\Role::ROLE_ADMIN,
                'new_password' => $new_password
            ])
            ->assertSessionHasErrors();

        $this->assertFalse(auth()->attempt([
            'username' => $user->username,
            'password' => $new_password
        ]));

        $this->actingAs($admin)
            ->from(route('users.edit', $user))
            ->put(route('users.update', $user))
            ->assertStatus(403);

        $this->actingAs($admin)
            ->from(route('users.edit', $user))
            ->put(route('users.update', $user), [
                'new_password' => '1337',
                're_password' => $new_password
            ])
            ->assertSessionHasErrors(['new_password', 're_password']);

        $this->actingAs($admin)
            ->from(route('users.edit', $user))
            ->put(route('users.update', $user), [
                'role' => \App\Models\Role::all()
                    ->except(\App\Models\Role::ROLE_ADMIN)
                    ->random()->id,
                'new_password' => $new_password,
                're_password' => $new_password,
            ])
            ->assertSessionHasNoErrors();

        $this->assertFalse(auth()->attempt([
            'username' => $user->username,
            'password' => $new_password
        ]));

        $this->actingAs($admin)
            ->from(route('users.edit', $user))
            ->put(route('users.update', $user), [
                'new_password' => $new_password,
                're_password' => $new_password
            ])
            ->assertSessionHasNoErrors();
        
        $this->assertTrue(auth()->attempt([
            'username' => $user->username,
            'password' => $new_password
        ]));
    }

    /**
     * Test if non-admin user cannot delete user.
     * 
     * @return void
     */
    public function test_delete_user_with_non_admin() {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $staff = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF
        ]);
        $manager = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_MANAGER
        ]);
        $victim = \App\Models\User::factory()->create();
        $admin = \App\Models\User::factory()->create();

        $this->actingAs($staff)
            ->delete(route('users.destroy', $victim))
            ->assertStatus(403);
        
        $this->actingAs($manager)
            ->delete(route('users.destroy', $victim))
            ->assertStatus(403);

        $this->assertDatabaseHas('users', [
            'id' => $victim->id,
            'deleted_at' => NULL
        ]);

        $this->actingAs($admin)
            ->delete(route('users.destroy', $admin))
            ->assertStatus(403);

        $this->actingAs($admin)
            ->delete(route('users.destroy', $victim))
            ->assertStatus(403);
        
        $victim->update(['role' => \App\Models\Role::ROLE_STAFF]);
        
        $this->actingAs($admin)
            ->followingRedirects()
            ->from(route('users.edit', $victim))
            ->delete(route('users.destroy', $victim))
            ->assertDontSee($victim->username);

        $this->assertSoftDeleted($victim);
    }

    /**
     * Test if we can delete a user with an Admin account,
     * but data remains.
     * 
     * @return void
     */
    public function test_delete_user_with_admin() {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'role' => \App\Models\Role::ROLE_STAFF
        ]);
        $brand = \App\Models\Brand::factory()->create();
        $bike = \App\Models\Bike::factory()->create();
        $order = \App\Models\Order::factory()->create();

        $order->bikes()->attach($bike->id, [
            'order_value' => 1,
            'order_buy_price' => $bike->bike_buy_price,
            'order_sell_price' => $bike->bike_sell_price,
        ]);

        $admin = \App\Models\User::factory()->create();

        $this->actingAs($admin)
            ->from(route('users.edit', $user))
            ->delete(route('users.destroy', $user))
            ->assertSessionHasNoErrors();

        $this->assertSoftDeleted($user);

        $this->get(route('brands.show', $brand))
            ->assertStatus(200)
            ->assertSee($user->nameAndUsername());

        $this->get(route('bikes.show', $bike))
            ->assertStatus(200)
            ->assertSee($user->nameAndUsername());

        $this->get(route('orders.show', $order))
            ->assertStatus(200)
            ->assertSee($user->nameAndUsername());
    }
}
