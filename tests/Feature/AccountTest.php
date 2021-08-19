<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Resources used in this test.
     * 
     * @var \App\Models\User
     */
    protected $user;

    /**
     * Default password.
     * 
     * @var string
     */
    protected $current_password = '1337';
    
    /**
     * Setting up test resources.
     * 
     * @return void
     */
    public function setUp() : void {
        parent::setUp();

        $this->seed(\Database\Seeders\RoleSeeder::class);

        $this->user = \App\Models\User::factory()->create([
            'password' => Hash::make($this->current_password)
        ]);
    }

    /**
     * Test login logout with already logged in account.
     * 
     * @return void
     */
    public function test_login_logout_with_logged_in_account() {
        auth()->login($this->user);

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
            'username' => $this->user->username,
            'password' => $this->current_password
        ])
        ->assertRedirect(route('dashboard'));

        $this->assertEquals(
            auth()->user()->nameAndUsername(),
            $this->user->nameAndUsername()
        );
    }

    /**
     * Test if normal frogs cannot logout.
     * 
     * @return void
     */
    public function test_logout_with_no_account() {
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
        \Illuminate\Support\Facades\Session::start();

        auth()->login($this->user);

        $this->get('/logout')
            ->assertRedirect(route('dashboard'));

        $this->assertTrue(auth()->check());

        auth()->logout($this->user);

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

        $new_password = '13377331';

        $this->get(route('auth.changepassword.index'))
            ->assertRedirect(route('auth.login.index'));

        // Non logged in should be redirected back to login page.
        $this->from(route('auth.changepassword.index'))
            ->post(route('auth.changepassword.handle'), [
                'password' => $this->current_password,
                'new_password' => $new_password,
                'confirm_password' => $new_password
            ])
            ->assertRedirect(route('auth.login.index'));

        // Password should remain unchanged
        $this->assertTrue(auth()->attempt([
            'username' => $this->user->username,
            'password' => $this->current_password
        ]));

        $this->actingAs($this->user)
            ->get(route('auth.changepassword.index'))
            ->assertStatus(200);

        $this->actingAs($this->user)
            ->from(route('auth.changepassword.index'))
            ->post(route('auth.changepassword.handle'), [
                'password' => $this->current_password,
                'new_password' => $new_password,
                'confirm_password' => $new_password
            ])
            ->assertSessionHasNoErrors();

        $this->assertTrue(auth()->attempt([
            'username' => $this->user->username,
            'password' => $new_password
        ]));

        $this->assertFalse(auth()->attempt([
            'username' => $this->user->username,
            'password' => 'lorem'
        ]));
    }
}
