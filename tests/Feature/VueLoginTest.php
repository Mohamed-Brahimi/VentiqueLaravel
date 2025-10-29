<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VueLoginTest extends TestCase
{
    //tester la vue à propos avec un message particulier

    public function testVueapropos()
    {
        $response = $this->get('/login');
        $response->assertSee('Email Address');
        $response->assertSee('Password');
        $response->assertSee('Remember Me');
        $response->assertSee('Login');
        $response->assertSee('Forgot Your Password?');


    }
}