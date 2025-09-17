<?php

namespace App\Services;

use App\DTOs\Model\CompanyDTO;
use App\Helpers\TreeHelper;
use App\Models\BusinessActivity;
use Illuminate\Support\Facades\DB;

class BusinessActivityService
{
    /**
     * @return array[]
     */
    public function getFullTree(): array
    {
        $sql = <<<SQL
        SELECT
            a.id AS "id",
            a.name AS "name",
            ap.parent_id AS "parentId"
        FROM
            "business_activities" AS a
            LEFT JOIN "business_activities_parents" AS ap ON (
                a.id = ap.child_id
                AND ap.is_direct = TRUE
            )
        SQL;

        $rows = DB::select($sql);

        if (empty($rows)) {
            return [];
        }

        foreach ($rows as &$row) {
            $row = (array)$row;
        }

        return TreeHelper::buildTree($rows);
    }

    /**
     * @return array{id:string,name:string,childs:array[]}
     */
    public function getSubTree(BusinessActivity $root): array
    {
        $sql = <<<SQL
        SELECT
            filtered_activities.id AS "id",
            filtered_activities.name AS "name",
            ap.parent_id AS "parentId"
        FROM
            (
                SELECT
                    _a.id AS "id",
                    _a.name AS "name",
                    _ap.parent_id AS "parent_id"
                FROM
                    "business_activities" AS _a
                    INNER JOIN "business_activities_parents" AS _ap ON (
                        _a.id = _ap.child_id
                    )
               WHERE
                    _ap.parent_id = :rootId
            ) AS filtered_activities

            LEFT JOIN "business_activities_parents" AS ap ON (
                filtered_activities.id = ap.child_id
                AND ap.is_direct = TRUE
            )
        SQL;

        $rows = DB::select($sql, [ 'rootId' => $root->id]);

        foreach ($rows as &$row) {
            $row = (array)$row;
        }

        $childs = TreeHelper::buildTree($rows, $root->id);

        if ($childs) {
            return [
                'id' => $root->id,
                'name' => $root->name,
                'childs' => $childs,
            ];
        } else {
            return [
                'id' => $root->id,
                'name' => $root->name,
            ];
        }
    }

    public function getSubTreeDepth(BusinessActivity $root): int
    {
        $sql = <<<SQL
        SELECT
            filtered_activities.child_id AS "child_id",
            ap.parent_id AS "parent_id"
        FROM
            (
                SELECT
                    _ap.child_id AS "child_id",
                    _ap.parent_id AS "parent_id"
                FROM
                    "business_activities_parents" AS _ap
               WHERE
                    _ap.parent_id = :rootId
            ) AS filtered_activities

            LEFT JOIN "business_activities_parents" AS ap ON (
                filtered_activities.child_id = ap.child_id
                AND ap.is_direct = TRUE
            )
        SQL;

        $rows = DB::select($sql, [ 'rootId' => $root->id]);

        foreach ($rows as &$row) {
            $row = (array)$row;
        }

        return $this->getDepth($rows, 'parent_id');
    }

    /**
     * @param array{child_id:string,parent_id:string}[] $rows
     * @param string                                    $key
     *
     * @return int
     */
    protected function getDepth(array $rows, string $key = 'child_id'): int
    {
        $depth = [];
        foreach ($rows as $row) {
            $id = $row[$key];
            if (!isset($depth[$id])) {
                $depth[$id] = 0;
            }
            $depth[$id]++;
        }

        if (empty($depth)) {
            return 0;
        }

        return max($depth);
    }

    /**
     * @param BusinessActivity $child
     *
     * @return ?BusinessActivity
     */
    public function getRoot(BusinessActivity $child): ?BusinessActivity
    {
        $sql = <<<SQL
        SELECT
            parents.parent_id AS "id"
        FROM
            (
                SELECT
                    _ap.child_id AS "child_id",
                    _ap.parent_id AS "parent_id"
                FROM
                    "business_activities_parents" AS _ap
                WHERE
                    _ap.child_id = :childId
            ) AS parents

            LEFT JOIN "business_activities_parents" AS super_parents ON (
                parents.parent_id = super_parents.child_id
            )
        WHERE
            super_parents.parent_id IS NULL
        SQL;

        $row = DB::selectOne($sql, ['childId' => $child->id]);

        if (empty($row)) {
            return null;
        }

        return BusinessActivity::query()->find($row->id);
    }

    /**
     * @param array{child_id:string,parent_id:string,is_direct:bool}[] $structure
     *
     * @return void
     */
    public function applyStructure(array $structure): void
    {
        $maxDepth = $this->getDepth($structure);

        if ($maxDepth >= BusinessActivity::MAX_DEPTH) {
            throw new \ValueError(
                "Cannot apply new structure: tree depth cannot be greater than "
                . BusinessActivity::MAX_DEPTH . ", got $maxDepth"
            );
        }

        DB::table('business_activities_parents')->insert($structure);
    }

    /**
     * @param array{id:string,name:string,parentId:string|null,childs:array[]}[] $tree
     */
    public function fillFromTree(array $tree): void
    {
        $rows = TreeHelper::flattenTree($tree);
        $activities = array_map(
            fn (array $row) => ['id' => $row['id'], 'name' => $row['name']],
            $rows
        );
        BusinessActivity::query()->insert($activities);

        $parents = [];
        foreach ($rows as $row) {
            $id = $row['id'];
            foreach (array_values($row['parents']) as $i => $ap) {
                $parents[] = [
                    'child_id' => $id,
                    'parent_id' => $ap,
                    'is_direct' => $i === 0,
                ];
            }
        }

        $this->applyStructure($parents);
    }

    /**
     * Returns list of all activity parents
     *
     * @param string $childId
     *
     * @return array{parentId:string,childId:string,isDirect:bool}[]
     */
    public function getParents(string $childId): array
    {
        $sql = <<<SQL
            SELECT
                parent_id AS "parentId",
                child_id AS "childId",
                is_direct AS "isDirect"
            FROM
                "business_activities_parents"
            WHERE
                child_id = :childId
        SQL;

        $rows = DB::select($sql, ['childId' => $childId]);
        foreach ($rows as &$row) {
            $row = (array)$row;
        }

        return $rows;
    }

    /**
     * Returns list of all activity parents
     *
     * @param string $childId
     *
     * @return array{parentId:string,childId:string,isDirect:bool}[]
     */
    public function getChilds(string $parentId): array
    {
        $sql = <<<SQL
            SELECT
                parent_id AS "parentId",
                child_id AS "childId",
                is_direct AS "isDirect"
            FROM
                "business_activities_parents"
            WHERE
                parent_id = :parentId
        SQL;

        $rows = DB::select($sql, ['parentId' => $parentId]);
        foreach ($rows as &$row) {
            $row = (array)$row;
        }

        return $rows;
    }

    /**
     * Adds one BusinessActivity to structure
     *
     * @param BusinessActivity  $activity
     * @param ?BusinessActivity $directParent
     * @param bool              $returnTree
     *
     * @return ($returnTree is true ? array[] : null)
     */
    public function addOne(
        BusinessActivity $activity,
        ?BusinessActivity $directParent,
        bool $returnTree = true,
    ): null|array {
        // проверяем, можем ли мы добавить этот элемент в нашу структуру
        $rootParent = null;

        if ($directParent !== null) {
            $directParentDepth = $this->getSubTreeDepth($directParent); // глубина вложенности дерева directParent

            $rootParent = $this->getRoot($directParent); // корень дерева
            if ($rootParent !== null) {
                $rootParentDepth = $this->getSubTreeDepth($rootParent); // глубина вложенности всей ветки дерева
                if ($rootParentDepth + $directParentDepth >= BusinessActivity::MAX_DEPTH) { // можем ли мы добавить еще уровень
                    throw new \LogicException("Cannot add activity: max depth (" . BusinessActivity::MAX_DEPTH . ") exceeded");
                }
            }
        }

        $activity->save();

        if ($directParent) {
            $parents = $this->getParents($directParent->id);
            $parentsIds = array_merge([$directParent->id], array_column($parents, 'parentId'));

            DB::table("business_activities_parents")->insert(
                array_map(
                    fn (string $parentId) => [
                        'parent_id' => $parentId,
                        'child_id' => $activity->id,
                        'is_direct' => $parentId == $directParent?->id,
                    ],
                    $parentsIds
                )
            );
        }

        if ($returnTree) {
            $treeRoot = $rootParent ?? $directParent ?? $activity;
            return $this->getSubTree($treeRoot);
        } else {
            return null;
        }
    }

    public function deleteSubTree(BusinessActivity $root): void
    {
        $childs = $this->getChilds($root->id);
        foreach ($childs as &$child) {
            $child['id'] = $child['childId'];
        }

        $depth = $this->getDepth($childs, 'childId');
        $tree = TreeHelper::buildTree($childs, $root->id);

        // удаляем дерево от веток к стволу
        for ($layerDepth=$depth-1; $layerDepth>=0; $layerDepth--) {
            $layer = TreeHelper::getTreeLayer($tree, $layerDepth);
            $ids = array_column($layer, 'id');
            BusinessActivity::query()->whereIn('id', $ids)->delete();
        }
        $root->delete();
    }

    /**
     * Moves an element within the tree without recreating it
     *
     * @param BusinessActivity  $activity
     * @param ?BusinessActivity $newDirectParent
     * @param bool              $returnTree
     *
     * @return ($returnTree is true ? array[] : null)
     */
    public function changeParent(
        BusinessActivity $activity,
        ?BusinessActivity $newDirectParent,
        bool $returnTree = true,
    ): null|array {
        $childs = $this->getChilds($activity->id);
        $childsIds = array_column($childs, 'childId');
        $oldParents = $this->getParents($activity->id);
        $oldParentsIds = array_column($oldParents, 'parentId');
        $newRoot = null;

        if ($newDirectParent === null) {
            // удаляем старые связи
            DB::table("business_activities_parents")
                ->whereIn('child_id', array_merge($childsIds, [$activity->id]))
                ->whereIn('parent_id', $oldParentsIds)
                ->delete()
            ;
        } else {
            $newRoot = $this->getRoot($newDirectParent) ?? $newDirectParent;
            $newParentDepth = $this->getSubTreeDepth($newRoot);
            $activityDepth = $this->getSubTreeDepth($activity);
            if ($newParentDepth + $activityDepth >= BusinessActivity::MAX_DEPTH) {
                throw new \LogicException(
                    "Cannot change item parent: max depth (" . BusinessActivity::MAX_DEPTH . ") exceeded"
                );
            }
            $newParents = $this->getParents($newDirectParent->id);
            $newParentsIds = array_column($newParents, 'parentId');
            $rows = collect(
                array_merge($childsIds, [$activity->id])
            )->crossJoin(
                array_merge($newParentsIds, [$newDirectParent->id])
            )->toArray();
            // удаляем старые связи
            DB::table("business_activities_parents")
                ->whereIn('child_id', array_merge($childsIds, [$activity->id]))
                ->whereIn('parent_id', $oldParentsIds)
                ->delete()
            ;
            // создаем новые
            DB::table("business_activities_parents")->insert(
                array_map(
                    fn (array $row) => [
                        'parent_id' => $row[1],
                        'child_id' => $row[0],
                        'is_direct' => $row[1] == $newDirectParent->id && $row[0] == $activity->id
                    ],
                    $rows
                )
            );
        }

        if ($returnTree) {
            $root = $newRoot ?? $activity;
            return $this->getSubTree($root);
        } else {
            return null;
        }
    }

    /**
     * @return CompanyDTO[]
     */
    public function getChildCompanies(BusinessActivity $activity): array
    {
        $childs = $this->getChilds($activity->id);
        $ids = array_column($childs, 'childId');
        $ids[] = $activity->id;
        $ids = "('" . implode("','", $ids) . "')";

        $sql = <<<SQL
        SELECT DISTINCT
            c.id AS "id",
            c.name AS "name",
            c.created_at AS "created_at",
            c.updated_at AS "updated_at",
            c.building_id AS "building_id"
        FROM
            "company_activity" AS "ca"
            INNER JOIN "companies" AS "c" ON (
                ca.company_id = c.id
            )
        WHERE
            ca.activity_id IN $ids
        SQL;

        $rows = DB::select($sql);
        $companies = [];
        foreach ($rows as $row) {
            $companies[] = new CompanyDTO(
                id: $row->id,
                name: $row->name,
                building_id: $row->building_id,
                created_at: new \DateTimeImmutable($row->created_at),
                updated_at: new \DateTimeImmutable($row->updated_at),
            );
        }

        return $companies;
    }
}
