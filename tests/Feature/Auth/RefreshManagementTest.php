<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RefreshManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate()
    {
        $user = User::factory()->create();

        return \JWTAuth::fromUser($user);
    }

    /** @test */
    function user_can_be_token_refres()
    {
        $token = $this->authenticate();

        $response = $this->withHeaders(['Authorization' => 'Bearer '. $token])
            ->json('GET', route('refresh.store'));

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ])
            ->assertHeader('Authorization');

        $this->assertAuthenticated();
    }

    /** @test */
    function only_authenticated_user_can_token_refres()
    {
        $this->json('GET', route('refresh.store'))
            ->assertStatus(401);
    }
}
