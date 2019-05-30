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
}
