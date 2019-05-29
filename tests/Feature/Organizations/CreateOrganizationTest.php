<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateOrganizationTest extends TestCase
{
    /**
     * @test
     */
    public function smoke_test()
    {
        $response = $this->json('POST', 'api/organizations', ['org_name' => 'test'], []);
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function create_organization_request_should_be_valid()
    {
        $response = $this->json('POST', 'api/organizations', [], []);
        $response->assertStatus(422);

        $response = $this->json('POST', 'api/organizations', ['test'], []);
        $response->assertStatus(422);

        $response = $this->json('POST', 'api/organizations', ['org_name' => 'test', 'daughters' => 'test'], []);
        $response->assertStatus(422);
    }
}
