<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterManagementTest extends TestCase
{
    use RefreshDatabase;

    private function attributes($overrides = [])
    {
        return array_merge([
            'name' => '테스트',
            'email' => 'test@test.com',
            'password' => 'password',
        ], $overrides);
    }

    private function assertValidationError($response, $field)
    {
        $response->assertStatus(422)->assertJsonStructure(['errors' => [$field]]);
    }

    /** @test */
    function user_can_be_register()
    {
        $response = $this->json('POST', route('register.store'), $this->attributes());

        $response->assertStatus(201);
        $response->assertJson([
            'status' => 'success',
            'data' => [
                'name' => '테스트',
                'email' => 'test@test.com',
            ],
        ]);

        tap(User::first(), function ($user) {
            $this->assertCount(1, User::all());
            $this->assertEquals('테스트', $user->name);
            $this->assertEquals('test@test.com', $user->email);
            $this->assertTrue(\Hash::check('password', $user->password));
        });
    }

    /** @test */
    function name_is_required()
    {
        $response = $this->json('POST', route('register.store'), $this->attributes([
            'name' => '',
        ]));

        $this->assertValidationError($response, 'name');
    }

    /** @test */
    function email_is_required()
    {
        $response = $this->json('POST', route('register.store'), $this->attributes([
            'email' => '',
        ]));

        $this->assertValidationError($response, 'email');
    }

    /** @test */
    function password_is_required()
    {
        $response = $this->json('POST', route('register.store'), $this->attributes([
            'password' => '',
        ]));

        $this->assertValidationError($response, 'password');
    }

    /** @test */
    function email_must_be_email_format()
    {
        $response = $this->json('POST', route('register.store'), $this->attributes([
            'email' => 'test',
        ]));

        $this->assertValidationError($response, 'email');
        $this->assertCount(0, User::all());
    }

    /** @test */
    function email_is_unique()
    {
        $this->json('POST', route('register.store'), $this->attributes([
            'email' => 'test@test.com',
        ]));
        $response = $this->json('POST', route('register.store'), $this->attributes([
            'email' => 'test@test.com',
        ]));

        $this->assertValidationError($response, 'email');
        $this->assertCount(1, User::all());
    }

    /** @test */
    function must_enter_at_least_8_password()
    {
        $response = $this->json('POST', route('register.store'), $this->attributes([
            'password' => '1234567',
        ]));

        $this->assertValidationError($response, 'password');
    }
}
