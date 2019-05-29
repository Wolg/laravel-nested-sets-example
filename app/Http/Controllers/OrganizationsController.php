<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostOrganizationRequest;
use App\Services\OrganizationService;
use Illuminate\Http\Request;

class OrganizationsController extends Controller
{
    /**
     * @var OrganizationService
     */
    protected $service;

    /**
     * OrganizationsController constructor.
     * @param OrganizationService $service
     */
    public function __construct(OrganizationService $service)
    {
        $this->service = $service;
    }
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
        $this->service->store($request->all());
        return response()->json(['message' => 'Organization saved succesfully!']);
    }

}
