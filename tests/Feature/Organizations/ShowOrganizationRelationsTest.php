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

    /**
     * @test
     */
    public function show_organization_with_relations()
    {
        $parent = factory(\App\Organization::class)->create(['right' => 3]);
        $parent->root_id = $parent->id;
        $parent->save();
        $organization = factory(\App\Organization::class)
            ->create(['parent_id' => $parent->id, 'left' => 1, 'right' => 2, 'level' => 1, 'root_id' => $parent->id]);
        $response = $this->get('api/organizations/' . $organization->name);
        $response
            ->assertStatus(200)
            ->assertJson([
                [
                    'org_name' => $parent->name,
                    'relationship_type' => 'parent'
                ]
            ]);

        $daughter = factory(\App\Organization::class)
            ->create(['parent_id' => $organization->id, 'left' => 2, 'right' => 3, 'level' => 2, 'root_id' => $parent->id]);
        $organization->right = 4;
        $organization->save();
        $parent->right = 5;
        $parent->save();
        $response = $this->get('api/organizations/' . $organization->name);
        $response
            ->assertStatus(200);
        $this->assertEqualsCanonicalizing([
            [
                'org_name' => $parent->name,
                'relationship_type' => 'parent'
            ],
            [
                'org_name' => $daughter->name,
                'relationship_type' => 'daughter'
            ]
        ], json_decode($response->getContent(), true));

        $sister = factory(\App\Organization::class)
            ->create(['parent_id' => $parent->id, 'left' => 5, 'right' => 6, 'level' => 1, 'root_id' => $parent->id]);
        $parent->right = 7;
        $parent->save();
        $response = $this->get('api/organizations/' . $organization->name);
        $response->assertStatus(200);
        $this->assertEqualsCanonicalizing([
                [
                    'org_name' => $parent->name,
                    'relationship_type' => 'parent'
                ],
                [
                    'org_name' => $daughter->name,
                    'relationship_type' => 'daughter'
                ],
                [
                    'org_name' => $sister->name,
                    'relationship_type' => 'sister'
                ]
            ], json_decode($response->getContent(), true));
    }
}
