<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RouteTest extends TestCase
{
    use RefreshDatabase;

    public function testAccessCreationWithGuestRole()
    {
        $response = $this->get('/antiques/create');
        $response->assertRedirect('/login');
    }

    public function testAccessCreationWithUserRole()
    {
        $user = User::factory()->create([
            'role' => 'USER'
        ]);

        $this->actingAs($user);
        $response = $this->get('/antiques/create');
        $response->assertStatus(200);
    }

    public function testAccessCreationWithAdminRole()
    {
        // Create an admin user
        $admin = User::factory()->create([
            'role' => 'ADMIN'
        ]);

        $this->actingAs($admin);
        $response = $this->get('/antiques/create');
        $response->assertStatus(200);
    }

    public function testGuestCannotStoreAntique()
    {
        $antiqueData = [
            'name' => 'Test Antique',
            'description' => 'Test description',
            'price' => 100.50
        ];

        $response = $this->post('/antiques', $antiqueData);

        // Should redirect to login
        $response->assertRedirect('/login');
    }
}