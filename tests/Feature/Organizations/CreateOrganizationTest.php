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
}
