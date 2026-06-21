<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')
            ->assertRedirect('/login');
    }

    public function test_authenticated_user_can_open_dashboard_when_group_middleware_is_bypassed(): void
    {
        $user = User::factory()->create();

        $this->withoutMiddleware()
            ->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Client delivery control room');
    }

    public function test_demo_access_can_open_dashboard_when_enabled(): void
    {
        config([
            'services.demo_access.enabled' => true,
            'services.demo_access.admin' => true,
            'services.demo_access.name' => 'Demo User',
            'services.demo_access.email' => 'demo@example.test',
        ]);

        $this->post('/auth/demo')
            ->assertRedirect('/dashboard');

        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('Client delivery control room');
    }

    public function test_demo_access_is_hidden_when_disabled(): void
    {
        config(['services.demo_access.enabled' => false]);

        $this->post('/auth/demo')
            ->assertNotFound();
    }
}
