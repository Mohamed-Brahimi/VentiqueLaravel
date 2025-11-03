<?php

namespace Tests\Feature;

use App\Models\Antique;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArticleControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_admin_can_create_antique()
    {
        $user = User::factory()->create([
        ]);

        $this->actingAs($user);

        $antique = Antique::create([
            'name' => 'Test Antique',
            'description' => 'This is a test antique description',
            'price' => 150.75,
            'user_id' => $user->id,
            'image' => 'antiques/fake-test-image.jpg' // Fake image path
        ]);

        $AntiqueCree = Antique::find($antique->id);
        $this->assertNotNull($AntiqueCree);
        $this->assertEquals('Test Antique', $AntiqueCree->name);

    }
    public function test_user_can_create_antique()
    {
        $user = User::factory()->create([
        ]);

        $this->actingAs($user);

        $antique = Antique::create([
            'name' => 'Test Antique2',
            'description' => 'This is a test antique description',
            'price' => 150.75,
            'user_id' => $user->id,
            'image' => 'antiques/fake-test-image.jpg' // Fake image path
        ]);

        $AntiqueCree = Antique::find($antique->id);
        $this->assertNotNull($AntiqueCree);
        $this->assertEquals('Test Antique2', $AntiqueCree->name);

    }
    public function test_invalide_creation()
    {
        $user = User::factory()->create([
        ]);

        $this->actingAs($user);

        $response = $this->post('/antiques', [
            'name' => 'Test Antique',
            'description' => 'This is a test antique description',
            'price' => 150.75,
            'user_id' => $user->id,
        ]);

        $response->assertSessionHasErrors();

    }
}