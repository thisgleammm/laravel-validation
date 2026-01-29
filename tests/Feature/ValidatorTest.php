<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ValidatorTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testValidator(): void
    {
        $data = [
            "username" => 'admin',
            "password" => "12345678"
        ];

        $rules = [
            "username" => 'required',
            "password" => 'required'
        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);
    }
}
