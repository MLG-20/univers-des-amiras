<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\User;
use Filament\Pages\Auth\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_away_from_the_admin_dashboard(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_login_with_correct_credentials(): void
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@amiras.test',
            'password' => bcrypt('correct-password'),
        ]);

        Livewire::test(Login::class)
            ->set('data.email', $admin->email)
            ->set('data.password', 'correct-password')
            ->call('authenticate')
            ->assertHasNoErrors();

        $this->assertAuthenticatedAs($admin, 'admin');
    }

    public function test_admin_cannot_login_with_incorrect_password(): void
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@amiras.test',
            'password' => bcrypt('correct-password'),
        ]);

        Livewire::test(Login::class)
            ->set('data.email', $admin->email)
            ->set('data.password', 'wrong-password')
            ->call('authenticate')
            ->assertHasErrors(['data.email']);

        $this->assertGuest('admin');
    }

    public function test_inactive_admin_cannot_login(): void
    {
        $admin = Admin::factory()->inactive()->create([
            'password' => bcrypt('correct-password'),
        ]);

        Livewire::test(Login::class)
            ->set('data.email', $admin->email)
            ->set('data.password', 'correct-password')
            ->call('authenticate')
            ->assertHasErrors(['data.email']);

        $this->assertGuest('admin');
    }

    public function test_a_client_account_cannot_authenticate_on_the_admin_guard(): void
    {
        // A client registers with the same email/password an admin might use;
        // this must never grant access to the admin panel (guard isolation).
        User::factory()->create([
            'email' => 'shared@amiras.test',
            'password' => bcrypt('shared-password'),
        ]);

        Livewire::test(Login::class)
            ->set('data.email', 'shared@amiras.test')
            ->set('data.password', 'shared-password')
            ->call('authenticate')
            ->assertHasErrors(['data.email']);

        $this->assertGuest('admin');
    }

    public function test_a_client_authenticated_on_the_web_guard_cannot_access_admin_routes(): void
    {
        $client = User::factory()->create();

        $response = $this->actingAs($client, 'web')->get('/admin');

        $response->assertRedirect('/admin/login');
    }

    public function test_authenticated_admin_can_log_out(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->post('/admin/logout');

        $response->assertRedirect();
        $this->assertGuest('admin');
    }
}
