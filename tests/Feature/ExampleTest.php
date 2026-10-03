<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(302);
        $response->assertRedirect('/dashboard');
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        config()->set('inertia.testing.ensure_pages_exist', false);
        $user = \App\Models\User::first();
        $response = $this->actingAs($user)->withSession(['success' => 'Selamat datang kembali!'])->get('/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard/Index')
            ->has('flash.success')
            ->where('flash.success', 'Selamat datang kembali!')
        );
    }
}
