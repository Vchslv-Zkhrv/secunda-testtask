<?php

namespace Tests\Unit;

use App\Helpers\TreeHelper;
use Tests\TestCase;

class TreeHelperTest extends TestCase
{
    public function testFlattenAndBuildTree(): void
    {
        // из дерева в плоский массив и обратно

        $inputTree = [
            [
                'id' => '01993002-5b81-798d-adf1-2d37a6fb87b4',
                'name' => 'Автомобильный бизнес',
                'childs' => [
                    [
                        'id' => '01993006-d9bc-73cf-8721-c37535479f81',
                        'name' => 'Аренда транспорта / техники',
                        'childs' => [
                            [
                                'id' => '0199300b-4866-75ea-a2c5-733607e5e7ab',
                                'name' => 'Аренда авто',
                            ],
                            [
                                'id' => '0199300b-4866-75eb-a2c5-733607e5e7ab',
                                'name' => 'Аренда мотоциклов / квадроциклов / снегоходов',
                            ],
                            [
                                'id' => '0199300d-5e1d-7de8-bf2d-df7479bb296a',
                                'name' => 'Аренда спецтехиники',
                            ],
                        ],
                    ],
                    [
                        'id' => '01993009-09e3-7bd2-937e-582671862d6d',
                        'name' => 'Услуги',
                        'childs' => [
                            [
                                'id' => '0199300f-1d43-7c83-a1f7-14981ae44905',
                                'name' => 'Авто и мото подбор',
                            ],
                            [
                                'id' => '0199300f-365c-70ac-be24-d6e2aed71e29',
                                'name' => 'Ремонт',
                            ],
                            [
                                'id' => '0199300f-6915-79e7-99d7-24ac00023dce',
                                'name' => 'Тюнинг',
                            ],
                        ],
                    ],
                    [
                        'id' => '01993009-9a55-73d7-9e37-287b52439584',
                        'name' => 'Продажа автомобилей, запчастей, аксуссуары',
                    ],
                ],
            ],
            [
                'id' => '01993012-ba9e-781b-99d9-d4c222b34971',
                'name' => 'Логистика',
                'childs' => [
                    [
                        'id' => '01993013-6f15-70bf-9ac8-b7a2b6a300d7',
                        'name' => 'Авиа перевозки',
                    ],
                    [
                        'id' => '01993013-8c9f-7989-b21a-fe867371b43a',
                        'name' => 'Авто перевозки',
                    ],
                    [
                        'id' => '01993013-a73e-7fa7-8dd0-608b7afe490e',
                        'name' => 'ЖД перевозки',
                    ],
                    [
                        'id' => '01993013-f4bf-7e0d-827f-e38a3bf9d019',
                        'name' => 'Морские перевозки',
                    ],
                ],
            ],
        ];

        $rows = TreeHelper::flattenTree($inputTree);

        foreach ($rows as &$row) {
            if (empty($row['parents'])) {
                $row['parentId'] = null;
            } else {
                $row['parentId'] = reset($row['parents']);
            }
            unset($row['parents']);
        }

        $tree = TreeHelper::buildTree($rows);

        $this->assertEquals($inputTree, $tree);
    }
}
