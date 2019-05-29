<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostOrganizationRequest;
use Illuminate\Http\Request;

class OrganizationsController extends Controller
{
    /**
     * @param $name
     * @return array
     */
    public function show($name)
    {
        return [];
    }

    /**
     * @param PostOrganizationRequest $request
     * @return array
     */
    public function store(PostOrganizationRequest $request)
    {
        return [];
    }

}
