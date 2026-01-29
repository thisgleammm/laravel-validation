<?php

namespace Tests\Feature;

use App\Rules\RegistrationRule;
use App\Rules\UpperCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\In;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use function PHPUnit\Framework\assertNotNull;

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

        self::assertTrue($validator->passes());
        self::assertFalse($validator->fails());
    }
    public function testValidatorInvalid(): void
    {
        $data = [
            "username" => '',
            "password" => ''
        ];

        $rules = [
            "username" => 'required',
            "password" => 'required'
        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);

        self::assertFalse($validator->passes());
        self::assertTrue($validator->fails());

        $message = $validator->getMessageBag();
        $message->get("username");
        $message->get("password");
        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }
    public function testValidationException(): void
    {
        $data = [
            "username" => '',
            "password" => ''
        ];

        $rules = [
            "username" => 'required',
            "password" => 'required'
        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);

        try {
            $validator->validate();
            self::fail("ValidationException Not Trown");
        } catch (ValidationException $exception) {
            self::assertNotNull($exception->validator);
            $message = $exception->validator->errors();
            Log::error($message->toJson());
        }
    }

    public function testValidatorMultipleRules(): void
    {

        App::setLocale("id");
        $data = [
            "username" => 'eko',
            "password" => 'eko'
        ];

        $rules = [
            "username" => 'required|email|max:100',
            "password" => ['required', 'min:6', 'max:20']
        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);

        self::assertFalse($validator->passes());
        self::assertTrue($validator->fails());

        $message = $validator->getMessageBag();
        $message->get("username");
        $message->get("password");
        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }

    public function testValidatorValidData(): void
    {
        $data = [
            "username" => 'thisgleam@gmail.com',
            "password" => '12345678',
            "admin" => true
        ];

        $rules = [
            "username" => 'required|email|max:100',
            "password" => 'required|min:6|max:20'
        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);

        try {
            $valid = $validator->validate();
            Log::info(json_encode($valid, JSON_PRETTY_PRINT));
        } catch (ValidationException $exception) {
            self::assertNotNull($exception->validator);
            $message = $exception->validator->errors();
            Log::error($message->toJson());
        }
    }

    public function testValidationAdditionalMessage(): void
    {
        $data = [
            "username" => 'thisgleam@gmail.com',
            "password" => 'thisgleam@gmail.com'
        ];

        $rules = [
            "username" => 'required|email|max:100',
            "password" => ['required', 'min:6', 'max:20']
        ];

        $validator = Validator::make($data, $rules);
        $validator->after(function (\Illuminate\Validation\Validator $validator){
            $data = $validator->getData();
            if($data['username'] == $data['password']){
                $validator->errors()->add("password", "Password tidak boleh sama dengan username");
            }
        });
        self::assertNotNull($validator);

        self::assertFalse($validator->passes());
        self::assertTrue($validator->fails());

        $message = $validator->getMessageBag();
        
        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }
    public function testValidationCustomRule(): void
    {
        $data = [
            "username" => 'thisgleam@gmail.com',
            "password" => 'thisgleam@gmail.com'
        ];

        $rules = [
            "username" => ['required', 'email', 'max:100', new UpperCase()],
            "password" => ['required', 'min:6', 'max:20', new RegistrationRule()]
        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);

        self::assertFalse($validator->passes());
        self::assertTrue($validator->fails());

        $message = $validator->getMessageBag();
        
        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }

    public function testValidationCustomFunctionRule(): void
    {
        $data = [
            "username" => 'thisgleam@gmail.com',
            "password" => 'thisgleam@gmail.com'
        ];

        $rules = [
            "username" => ['required', 'email', 'max:100', function(string $attribute, string $value, \Closure $fail){
                if(strtoupper($value) != $value){
                    $fail("The $attribute field must be UPPERCASE");
                }
            }],
            "password" => ['required', 'min:6', 'max:20', new RegistrationRule()]
        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);

        self::assertFalse($validator->passes());
        self::assertTrue($validator->fails());

        $message = $validator->getMessageBag();
        
        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }

    public function testValidationRuleClasses(): void
    {
        $data = [
            "username" => 'Thisgleam',
            "password" => 'thisgleam12@gmail.com'
        ];

        $rules = [
            "username" => ['required', new In(["Gleam", "Budi", "Koko"])],
            "password" => ['required', Password::min(6)->letters()->numbers()->symbols()]
        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);

        self::assertTrue($validator->passes());
    }

    public function testNestedArray()
    {
        $data = [
            "name" => [
                "first" => "Gleam",
                "last" => "Budi"
            ],
            "address" => [
                "street" => "Jalan Durian",
                "city" => "Jakarta",
                "country" => "Indonesia"
            ]
        ];

        $rules = [
            "name.first" => ["required", "max:100"],
            "name.last" => ["max:100"],
            "address.street" => ["max:200"],
            "address.city" => ["required", "max:100"],
            "address.county" => ["required", "max:100"],
        ];

        $validator = Validator::make($data, $rules);
        self::assertTrue($validator->passes());
    }
    
    public function testNestedIndexedArray()
    {
        $data = [
            "name" => [
                "first" => "Gleam",
                "last" => "Budi"
            ],
            "address" => [
                [
                    "street" => "Jalan Durian",
                    "city" => "Jakarta",
                    "country" => "Indonesia"
                ],
                [
                    "street" => "Jalan Manggis",
                    "city" => "Jakarta",
                    "country" => "Indonesia"
                ]
            ]
        ];

        $rules = [
            "name.first" => ["required", "max:100"],
            "name.last" => ["max:100"],
            "address.*.street" => ["max:200"],
            "address.*.city" => ["required", "max:100"],
            "address.*.county" => ["required", "max:100"],
        ];

        $validator = Validator::make($data, $rules);
        self::assertTrue($validator->passes());
    }
}
