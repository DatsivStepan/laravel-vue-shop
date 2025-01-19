<?php

namespace Tests;

use App\Models\User;
use App\Tenancy\Tenant;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Auth;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $tenancy = false;
    protected $createUser = false;
    /** @var User $user */
    protected $user;

    protected $currentTenant;

    public function setUp(): void
    {
        parent::setUp();

        if ($this->tenancy) {
            $this->initializeTenancy();
        }

        if ($this->createUser) {
            $this->user = factory(User::class)->create();
            $this->user->roles()->create(['name' => 'admin', 'guard_name' => 'api']);
        }
    }

    public function tearDown(): void
    {
        config(
            [
                'tenancy.queue_database_deletion' => false,
                'tenancy.delete_database_after_tenant_deletion' => true,
            ]
        );
        tenancy()->query()->get()->each->delete();

        parent::tearDown();
    }

    public function initializeTenancy()
    {
        $this->currentTenant = Tenant::create();
        tenancy()->initialize($this->currentTenant);
    }

    /**
     * @param $method
     * @param $uri
     * @param array $data
     * @param User|null $user
     * @param array $headers
     *
     * @return \Illuminate\Testing\TestResponse
     */
    protected function apiAs($method, $uri, array $data = [], User $user = null, array $headers = [])
    {
        if ($user) {
            $token = Auth::login($user);
        } else {
            if (isset($this->user)) {
                $token = Auth::login($this->user);
            } else {
                $token = '';
            }
        }

        $headers = array_merge([
            'Authorization' => 'Bearer ' . $token,
        ], $headers);


        return $this->api($method, $uri, $data, $headers);
    }

    protected function api($method, $uri, array $data = [], array $headers = [])
    {
        return $this->json($method, $uri, $data, $headers);
    }
}
