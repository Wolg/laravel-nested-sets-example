<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowOrganizationRelationsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function smoke_test()
    {
        $response = $this->get('api/organizations/black banana');
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function show_organization_without_relations()
    {
        $organization = factory(\App\Organization::class)->create();
        $response = $this->get('api/organizations/' . $organization->name);
        $response
            ->assertStatus(200)
            ->assertJson([]);
    }
}
