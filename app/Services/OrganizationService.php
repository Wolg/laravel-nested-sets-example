<?php
namespace App\Services;

use App\Organization;

class OrganizationService
{
    /**
     * @param $organization
     */
    public function store($organization)
    {
        $this->buildTree([$organization]);
    }

    /**
     * @param $organizations
     * @param null $parentId
     * @param null $root
     * @param int $level
     * @param int $number
     */
    protected function buildTree($organizations, $parentId = null, $root = null, $level = 0, &$number = 0)
    {
        foreach ($organizations as $organization) {
            $model = new Organization();
            $model->name = $organization['org_name'];
            $model->parent_id = $parentId;
            $model->level = $level;
            $model->left = $number++;
            $model->save();
            if ($root == null) {
                $root = $model->id;
            }
            if (isset($organization['daughters']) && is_array($organization['daughters']) ) {
                $this->buildTree($organization['daughters'], $model->id, $root, $level + 1, $number);
            }
            $model->root = $root;
            $model->right = $number++;
            $model->save();
        }
    }
}