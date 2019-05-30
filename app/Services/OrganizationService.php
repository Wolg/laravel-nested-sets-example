<?php
namespace App\Services;

use App\Organization;
use App\Queries\OrganizationQuery;
use Illuminate\Support\Facades\DB;

class OrganizationService
{
    /**
     * @var OrganizationQuery
     */
    protected $query;

    /**
     * OrganizationService constructor.
     * @param OrganizationQuery $query
     */
    public function __construct(OrganizationQuery $query)
    {
        $this->query = $query;
    }

    /**
     * @param string $name
     * @param int $limit
     * @return mixed
     */
    public function findRelationsByName(string $name, int $limit)
    {
        return $this->query->findRelationsByName($name, $limit);
    }
    /**
     * @param array $organization
     * @throws \Throwable
     */
    public function store(array $organization)
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
    protected function buildTree(array $organizations, int $parentId = null, int $rootId = null, int $level = 0, int &$number = 0)
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