<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FormControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testLoginFailed(): void
    {
        $response = $this->post('/form/login', [
            'username' => '',
            'password' => ''
        ]);
        $response->assertStatus(400);
    }
    public function testLoginSuccess(): void
    {
        $response = $this->post('/form/login', [
            'username' => 'admin',
            'password' => 'rahasia'
        ]);
        $response->assertStatus(200);
    }
    public function testFormFailed(): void
    {
        $response = $this->post('/form', [
            'username' => '',
            'password' => ''
        ]);
        $response->assertStatus(302);
    }
    public function testFormSuccess(): void
    {
        $response = $this->post('/form', [
            'username' => 'admin',
            'password' => 'rahasia'
        ]);
        $response->assertStatus(200);
    }
}
