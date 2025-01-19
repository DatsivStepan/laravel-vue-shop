<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class AdvancedTaxesFieldControllerTest extends TestCase
{
    use WithoutMiddleware;

    protected $tenancy = true;
    protected $createUser = true;

    public function testUserCanCreateTaxField()
    {
        $params = [
            'key' => 'name',
            'name' => 'Name',
        ];

        $this->apiAs('POST', route('api.taxes.fields.store'), $params, $this->user)
            ->assertStatus(201);
    }
}
