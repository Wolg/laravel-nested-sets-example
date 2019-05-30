<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostOrganizationRequest;
use App\Http\Resources\OrganizationRelations;
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
     * @param Request $request
     * @param $name
     * @return OrganizationRelations
     */
    public function show(Request $request, $name)
    {
        return new OrganizationRelations($this->service->findRelationsByName($name, (int) $request->get('limit')));
    }

    /**
     * @param PostOrganizationRequest $request
     * @return array
     */
    public function store(PostOrganizationRequest $request)
    {
        $this->service->store($request->all());
        return response()->json(['message' => trans('organization.created.success')]);
    }

}
