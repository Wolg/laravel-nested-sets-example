<?php
namespace App\Queries;

use App\Organization;
use Illuminate\Pagination\LengthAwarePaginator;

class OrganizationQuery
{
    /**
     * @param string $name
     * @param $limit
     * @return mixed
     */
    public function findRelationsByName(string $name, int $limit)
    {
        // Lets find a starting root
        $virtualRoot = Organization::leftJoin('organizations as parent', function ($join) {
                $join->on('organizations.root_id', '=', 'parent.root_id');
                $join->on('organizations.parent_id', '=', 'parent.id');
            })
            ->select(
                'organizations.id as node_id',
                'parent.id as parent_id',
                'parent.parent_id as grandparent_id',
                'organizations.root_id',
                'organizations.level',
                'parent.level as parent_level'
            )
            ->where('organizations.name', $name)
            ->first();
        if (!$virtualRoot) {
            return null;
        }
        $virtualRootId = $virtualRoot->grandparent_id ?: ($virtualRoot->parent_id ?: $virtualRoot->node_id);
        $organizations = Organization::leftJoin('organizations as parent', function ($join) {
                $join->on('organizations.root_id', '=', 'parent.root_id');
            })
            ->whereRaw('organizations.left BETWEEN parent.left AND parent.right')
            ->where('parent.id', $virtualRootId)
            ->where('organizations.root_id', $virtualRoot->root_id)
            ->where('organizations.name', '!=', $name)
            ->groupBy('organizations.name', 'organizations.level')
            ->orderBy('organizations.name')
            ->selectRaw(
                'organizations.name, IF(organizations.level > ?, "daughter", IF(organizations.level = ?, "sister", "parent")) as relation',
                [$virtualRoot->level, $virtualRoot->level]
            );
        if ($virtualRoot['parent_level'] != null) {
            $organizations->having('organizations.level', '>=', $virtualRoot->parent_level);
        }
        $organizations = $organizations->get();
        // Pagination
        $page = LengthAwarePaginator::resolveCurrentPage();
        $path = LengthAwarePaginator::resolveCurrentPath();
        $perPage = $limit ?: config('app.organizations.pagination.limit');
        $organizationToDisplay = $organizations->forPage($page, $perPage);

        return new LengthAwarePaginator(
            $organizationToDisplay,
            $organizations->count(),
            $perPage,
            $page,
            ['path' => $path]
        );
    }
}