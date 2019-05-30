<?php
namespace App\Services;

use App\Organization;
use Illuminate\Support\Facades\DB;

class OrganizationService
{
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
     * @param null $root
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