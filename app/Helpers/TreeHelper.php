<?php

namespace App\Helpers;

/**
 * Static class containing tree manipulation methods
 */
class TreeHelper
{
    /**
     * Make flat rows from tree
     *
     * @param array{id:mixed,childs:array[]}[] $tree
     * @param mixed[]                          $parents
     *
     * @return array{id:mixed,parents:mixed[]}[]
     */
    public static function flattenTree(
        array $tree,
        $parents = [],
    ): array {
        $plain = [];
        foreach ($tree as $item) {
            if (!empty($item['childs'])) {
                $childs = static::flattenTree($item['childs'], array_merge([$item['id']], $parents));
                unset($item['childs']);
                $plain = array_merge($plain, $childs);
            }
            $item['parents'] = $parents;
            $plain[] = $item;
        }
        return $plain;
    }

    /**
     * Make a tree from flat rows
     *
     * @param array{id:mixed,parentId:string}[] $rows
     * @param mixed                             $parentId
     *
     * @return array{id:mixed,childs:array[]}[]
     */
    public static function buildTree(
        array $rows,
        $parentId = null,
    ): array {
        $roots = [];
        foreach ($rows as $i => $row) {
            $rowParentId = $row['parentId'] ?? null;
            if ($rowParentId == $parentId) {
                unset($row['parentId']);
                $roots[] = $row;
            }
        }

        foreach ($roots as $i => &$root) {
            $subtrees = static::buildTree($rows, $root['id']);
            if ($subtrees) {
                $root['childs'] = $subtrees;
            }
        }

        return $roots;
    }

    /**
     * Get Nth layer of a tree
     *
     * @param array{id:mixed,childs:array[]}[] $tree
     * @param int                              $layer
     * @param int                              $currentLayer
     *
     * @return array{id:mixed}[]
     */
    public static function getTreeLayer(
        array $tree,
        int $layer,
        int $currentLayer = 0
    ): array {
        $result = [];

        foreach ($tree as $item) {
            if ($layer == $currentLayer) {
                unset($item['childs']);
                $result[] = $item;
            } else {
                $result = array_merge(
                    $result,
                    static::getTreeLayer($item['childs'], $currentLayer+1)
                );
            }
        }

        return $result;
    }
}
