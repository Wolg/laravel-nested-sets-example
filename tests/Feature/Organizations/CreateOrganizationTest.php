<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateOrganizationTest extends TestCase
{
    use RefreshDatabase;

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

        // Test duplicates
        $response = $this->json('POST', 'api/organizations', ['org_name' => 'test'], []);
        $response->assertStatus(200);
        $response = $this->json('POST', 'api/organizations', ['org_name' => 'test'], []);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function create_organization_successfully()
    {
        $json = '{
            "org_name": "Paradise island",
            "daughters": [
                {
                    "org_name": "Banana Tree",
                    "daughters": [
                        {"org_name": "Yellow Banana"},
                        {"org_name": "Brown Banana"},
                        {"org_name": "Black Banana"}
                    ]
                },
                {
                    "org_name": "Big Banana Tree",
                    "daughters": [
                        {"org_name": "Yellow Banana"},
                        {"org_name": "Brown Banana"},
                        {"org_name": "Green Banana"},
                        {
                            "org_name": "Black banana",
                            "daughters": [{
                                "org_name": "Phoneutria Spider"
                            }]
                        }
                    ]	
                }
            ]
        }';
        $response = $this->json('POST', 'api/organizations', json_decode($json, true), []);
        $response
            ->assertStatus(200)
            ->assertJson([
            'message' => trans('organization.created.success'),
        ]);
    }
}
