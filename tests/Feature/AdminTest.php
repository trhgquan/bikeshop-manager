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
                'username' => explode('@', $email)[0],
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
        $username = explode('@', $email)[0];
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
        $username = explode('@', $email)[0];
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
    public function test_update_user_with_non_admin() {
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
}
