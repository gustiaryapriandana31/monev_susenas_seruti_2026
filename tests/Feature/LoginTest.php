<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that guests are redirected to the login page.
     */
    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    /**
     * Test that guest can view the login page.
     */
    public function test_guests_can_view_login_page(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200)
            ->assertSee('Monev Susenas Seruti')
            ->assertSee('Username')
            ->assertSee('Password');
    }

    /**
     * Test login with invalid credentials.
     */
    public function test_cannot_login_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'username' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    /**
     * Test login with valid credentials.
     */
    public function test_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'username' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'password',
        ]);

        $response->assertRedirect('/')
            ->assertSessionHas('success');
        
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test authenticated user cannot access login page.
     */
    public function test_authenticated_user_cannot_access_login_page(): void
    {
        $user = User::factory()->create([
            'username' => 'admin',
        ]);

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect('/');
    }

    /**
     * Test logout.
     */
    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create([
            'username' => 'admin',
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login')
            ->assertSessionHas('success');
        
        $this->assertGuest();
    }
}
