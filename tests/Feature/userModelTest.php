<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UsermodelTest extends TestCase
{
    use RefreshDatabase;
    public function testValideRegistration()
    {

        $count = User::count();

        $response = $this->post('/register', [
            'email' => 'test@mail.com',
            'name' => 'test',
            'password' => 'motdepasse',
            'password_confirmation' => 'motdepasse'

        ]);
        $newCount = User::count();
        // dd($count, $newCount);
        $this->assertNotEquals($count, $newCount);


    }

    public function testIvalideRegistration()
    {

        $response = $this->post('/register', [
            'email' => '',
            'name' => 'test',
            'password' => 'motdepasse',
            'password_confirmation' => 'motdepasse'

        ]);
        // dd(session('errors'));
        $response->assertSessionHasErrors();
    }
}