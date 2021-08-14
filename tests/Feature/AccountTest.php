<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test login logout with already logged in account.
     * 
     * @return void
     */
    public function test_login_logout_with_logged_in_account() {
        $this->seed(\Database\Seeders\RoleSeeder::class);
        
        $user = \App\Models\User::factory()->create();

        \Illuminate\Support\Facades\Session::start();
        auth()->login($user);

        $this->get(route('auth.login.index'))
            ->assertRedirect(route('dashboard'));
        
        $this->post(route('auth.logout'), [
            '_token' => \Illuminate\Support\Facades\Session::token()
            ])
            ->assertRedirect(route('auth.login.index'));
    }

    /**
     * Test login.
     * 
     * @return void
     */
    public function test_login() {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'password' => \Illuminate\Support\Facades\Hash::make('1337')
        ]);

        $this->post(route('auth.login.handle'), [
            'username' => '123',
            'password' => '123'
        ])->assertSessionHasErrors();

        $this->post(route('auth.login.handle'), [
            'username' => '1337'
        ])->assertSessionHasErrors(['password']);

        $this->post(route('auth.login.handle'), [
            'password' => '1337'
        ])->assertSessionHasErrors(['username']);

        $this->post(route('auth.login.handle'))
            ->assertSessionHasErrors(['username', 'password']);

        $this->post(route('auth.login.handle'), [
            'username' => $user->username,
            'password' => '1337'
        ])
        ->assertRedirect(route('dashboard'));

        $this->assertEquals(
            auth()->user()->nameAndUsername(),
            $user->nameAndUsername()
        );
    }

    /**
     * Test if normal frogs cannot logout.
     * 
     * @return void
     */
    public function test_logout_with_no_account() {
        $this->seed(\Database\Seeders\RoleSeeder::class);
        
        \Illuminate\Support\Facades\Session::start();
        
        $this->post(route('auth.logout'), [
            '_token' => \Illuminate\Support\Facades\Session::token()
        ])->assertRedirect(route('auth.login.index'));
    }

    /**
     * Test if cannot using logout url with GET method.
     * 
     * @return void
     */
    public function test_accessing_logout_with_get_method() {
        $this->seed(\Database\Seeders\RoleSeeder::class);
        
        \Illuminate\Support\Facades\Session::start();

        $user = \App\Models\User::factory()->create();

        auth()->login($user);

        $this->get('/logout')
            ->assertRedirect(route('dashboard'));

        $this->assertTrue(auth()->check());

        auth()->logout($user);

        $this->assertFalse(auth()->check());

        $this->followingRedirects()
            ->get('/logout')
            ->assertSee('username')
            ->assertSee('password');
    }

    /**
     * Test if user can change password.
     * 
     * @return void
     */
    public function test_changing_password() {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = \App\Models\User::factory()->create([
            'password' => \Illuminate\Support\Facades\Hash::make('lorem')
        ]);

        $new_password = '13377331';

        $this->get(route('auth.changepassword.index'))
            ->assertRedirect(route('auth.login.index'));

        // Non logged in should be redirected back to login page.
        $this->from(route('auth.changepassword.index'))
            ->post(route('auth.changepassword.handle'), [
                'password' => 'lorem',
                'new_password' => $new_password,
                'confirm_password' => $new_password
            ])
            ->assertRedirect(route('auth.login.index'));

        // Password should remain unchanged
        $this->assertTrue(auth()->attempt([
            'username' => $user->username,
            'password' => 'lorem'
        ]));

        $this->actingAs($user)
            ->get(route('auth.changepassword.index'))
            ->assertStatus(200);

        $this->actingAs($user)
            ->from(route('auth.changepassword.index'))
            ->post(route('auth.changepassword.handle'), [
                'password' => 'lorem',
                'new_password' => $new_password,
                'confirm_password' => $new_password
            ])
            ->assertSessionHasNoErrors();

        $this->assertTrue(auth()->attempt([
            'username' => $user->username,
            'password' => $new_password
        ]));

        $this->assertFalse(auth()->attempt([
            'username' => $user->username,
            'password' => 'lorem'
        ]));
    }
}
