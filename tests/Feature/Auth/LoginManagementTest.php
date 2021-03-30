<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginManagementTest extends TestCase
{
    use RefreshDatabase;

    private function attributes($overrides = [])
    {
        return array_merge([
            'email' => 'test@test.com',
            'password' => 'password',
        ], $overrides);
    }

    protected function authenticate()
    {
        $user = User::factory()->create();

        return \JWTAuth::fromUser($user);
    }

    /** @test */
    function user_can_be_login()
    {
        $user = User::factory()->create($this->attributes());

        // id/pw 입력 후 로그인 한다.
        $payload = ['email' => $user->email, 'password' => 'password'];
        $response = $this->json('POST', route('login.store'), $payload);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ])
            ->assertHeader('Authorization');

        $this->assertAuthenticated();
    }

    /** @test */
    function must_be_a_register_user()
    {
        User::factory()->create($this->attributes());

        $payload = ['email' => 'not@register', 'password' => 'what'];
        $this->json('POST', route('login.store'), $payload)->assertStatus(401);

        $this->assertGuest();
    }

    /** @test */
    function user_can_be_logout()
    {
        $token = $this->authenticate();

        $this->withHeaders(['Authorization' => 'Bearer '. $token])
            ->json('delete', route('login.destroy'))
            ->assertStatus(200);

        $this->assertGuest();
    }

    /** @test */
    function only_authenticated_user_can_logout()
    {
        $this->deleteJson(route('login.destroy'))
            ->assertStatus(401);
    }

    /** @test */
    function user_can_inquire_login_info()
    {
        $token = $this->authenticate();

        $response = $this->withHeaders(['Authorization' => 'Bearer '. $token])
            ->getJson(route('login.show'));

        $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'data' => true,
        ]);
    }

    /** @test */
    function only_authenticated_user_can_inquire_login_info()
    {
        $this->getJson(route('login.show'))
            ->assertStatus(401);
    }
}
