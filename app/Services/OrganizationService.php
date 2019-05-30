<?php
namespace App\Services;

use App\Organization;
use Illuminate\Support\Facades\DB;

class OrganizationService
{
    /**
     * @param $name
     * @return mixed
     */
    public function findRelationsByName($name)
    {
        $virtualRoot = Organization::leftJoin('organizations as parent', function ($join) {
                $join->on('organizations.root_id', '=', 'parent.root_id');
                $join->on('organizations.parent_id', '=', 'parent.id');
            })
            ->select('organizations.id as node_id', 'parent.id as parent_id', 'parent.parent_id as grandparent_id', 'organizations.root_id', 'organizations.level')
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
            ->selectRaw('organizations.name, IF(organizations.level > ?, "daughter", IF(organizations.level = ?, "sister", "parent")) as relation',
                [$virtualRoot->level, $virtualRoot->level])
            ->get();
        return $organizations;
    }

    /**
     * @param $organization
     */
    public function store($organization)
    {
        try{
            DB::beginTransaction();
            $this->buildTree([$organization]);
            DB::commit();
        } catch(\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param $organizations
     * @param null $parentId
     * @param null $rootId
     * @param int $level
     * @param int $number
     */
    protected function buildTree($organizations, $parentId = null, $rootId = null, $level = 0, &$number = 0)
    {
        foreach ($organizations as $organization) {
            $model = new Organization();
            $model->name = $organization['org_name'];
            $model->parent_id = $parentId;
            $model->level = $level;
            $model->left = $number++;
            $model->save();
            if ($rootId == null) {
                $rootId = $model->id;
            }
            if (isset($organization['daughters']) && is_array($organization['daughters']) ) {
                $this->buildTree($organization['daughters'], $model->id, $rootId, $level + 1, $number);
            }
            $model->root_id = $rootId;
            $model->right = $number++;
            $model->save();
        }
    }
}