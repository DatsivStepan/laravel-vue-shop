<?php

namespace Tests\Feature;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use WithoutMiddleware;

    protected $tenancy = true;
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        factory(UserData::class)->create(['user_id' => $this->user->id]);

        $this->mock(UserRepository::class,
            function ($mock) {
                $mock->shouldReceive('getLmsUserByToken')->once()->andReturn($this->user);
            });
    }

    public function testSuccessLogin()
    {
        $this->be($this->user);

        $params = [
            'auth_token' => $this->user->userData->temp_token,
            'connection_id' => $this->currentTenant->id,
        ];

        $this->json('POST', route('login'), $params)
            ->assertStatus(200);
    }
}
