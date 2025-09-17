<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Company;
use App\Models\Phone;
use App\Services\BusinessActivityService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use MatanYadaev\EloquentSpatial\Objects\Point;

class DatabaseSeeder extends Seeder
{
    public function __construct(
        protected BusinessActivityService $activityService,
    ) {
    }

    public function run(): void
    {
        $this->activityService->fillFromTree([
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
        ]);

        Building::query()->insert([
            [
                'id' => '01993f17-ad61-771e-b3d1-ba111b7d1b99',
                'address' => 'Россия, г. Нижний Новгород, ул Большая Покровская, д 82, ТРЦ "НЕБО"',
                'coordinates' => new Point(43.987242, 56.309139),
            ],
            [
                'id' => '01993f18-e7da-7231-b5d9-0396a13d70f5',
                'address' => 'Россия, г. Нижний Новгород, ул Ульянова, д 13',
                'coordinates' => new Point(44.011510, 56.325588),
            ],
            [
                'id' => '01993f25-e8a4-7fb2-9a02-8ae4c0fe102a',
                'address' => 'Россия, г. Нижний Новгород, ул Белинского, д 63, ТЦ "ЭТАЖИ"',
                'coordinates' => new Point(44.020544, 56.316915),
            ],
        ]);

        Company::query()->insert([
            [
                'id' => '01993f27-3cc7-7cd5-a90b-a6bb1012d982',
                'name' => 'Лада деталь',
                'building_id' => '01993f17-ad61-771e-b3d1-ba111b7d1b99',
            ],
            [
                'id' => '01993f27-1ffb-7e27-aa3c-742c2ecb0f60',
                'name' => 'Аэрофлот',
                'building_id' => '01993f18-e7da-7231-b5d9-0396a13d70f5',
            ],
            [
                'id' => '01993f27-0340-7586-a862-81dd87b69342',
                'name' => 'РИК',
                'building_id' => '01993f17-ad61-771e-b3d1-ba111b7d1b99',
            ],
            [
                'id' => '01993f26-eb17-79d1-865f-4a8c254d1295',
                'name' => 'РЖД',
                'building_id' => '01993f25-e8a4-7fb2-9a02-8ae4c0fe102a',
            ],
            [
                'id' => '01993f26-d303-7fa1-af05-c440b2df57a4',
                'name' => 'CAT',
                'building_id' => '01993f17-ad61-771e-b3d1-ba111b7d1b99',
            ],
            [
                'id' => '01993f26-b6ba-7a60-a330-6c1b5af8b1d7',
                'name' => 'Volvo',
                'building_id' => '01993f18-e7da-7231-b5d9-0396a13d70f5',
            ],
        ]);

        DB::table('company_activity')->insert([
            [
                'company_id' => '01993f27-3cc7-7cd5-a90b-a6bb1012d982',
                'activity_id' => '01993009-9a55-73d7-9e37-287b52439584',
            ],
            [
                'company_id' => '01993f27-1ffb-7e27-aa3c-742c2ecb0f60',
                'activity_id' => '01993013-6f15-70bf-9ac8-b7a2b6a300d7',
            ],
            [
                'company_id' => '01993f27-0340-7586-a862-81dd87b69342',
                'activity_id' => '01993013-8c9f-7989-b21a-fe867371b43a',
            ],
            [
                'company_id' => '01993f27-0340-7586-a862-81dd87b69342',
                'activity_id' => '01993013-a73e-7fa7-8dd0-608b7afe490e',
            ],
            [
                'company_id' => '01993f27-0340-7586-a862-81dd87b69342',
                'activity_id' => '01993013-f4bf-7e0d-827f-e38a3bf9d019',
            ],
            [
                'company_id' => '01993f26-eb17-79d1-865f-4a8c254d1295',
                'activity_id' => '01993013-a73e-7fa7-8dd0-608b7afe490e',
            ],
            [
                'company_id' => '01993f26-d303-7fa1-af05-c440b2df57a4',
                'activity_id' => '0199300f-365c-70ac-be24-d6e2aed71e29',
            ],
            [
                'company_id' => '01993f26-d303-7fa1-af05-c440b2df57a4',
                'activity_id' => '0199300d-5e1d-7de8-bf2d-df7479bb296a',
            ],
            [
                'company_id' => '01993f26-b6ba-7a60-a330-6c1b5af8b1d7',
                'activity_id' => '0199300d-5e1d-7de8-bf2d-df7479bb296a',
            ],
            [
                'company_id' => '01993f26-b6ba-7a60-a330-6c1b5af8b1d7',
                'activity_id' => '0199300f-365c-70ac-be24-d6e2aed71e29',
            ],
            [
                'company_id' => '01993f26-b6ba-7a60-a330-6c1b5af8b1d7',
                'activity_id' => '01993009-9a55-73d7-9e37-287b52439584',
            ],
        ]);

        Phone::query()->insert([
            [
                'number' => '79111111111',
                'company_id' => '01993f27-3cc7-7cd5-a90b-a6bb1012d982',
            ],
            [
                'number' => '79222222222',
                'company_id' => '01993f27-3cc7-7cd5-a90b-a6bb1012d982',
            ],
            [
                'number' => '79333333333',
                'company_id' => '01993f27-1ffb-7e27-aa3c-742c2ecb0f60',
            ],
            [
                'number' => '79444444444',
                'company_id' => '01993f27-1ffb-7e27-aa3c-742c2ecb0f60',
            ],
            [
                'number' => '79555555555',
                'company_id' => '01993f27-1ffb-7e27-aa3c-742c2ecb0f60',
            ],
            [
                'number' => '79666666666',
                'company_id' => '01993f27-0340-7586-a862-81dd87b69342',
            ],
            [
                'number' => '79777777777',
                'company_id' => '01993f26-eb17-79d1-865f-4a8c254d1295',
            ],
            [
                'number' => '79888888888',
                'company_id' => '01993f26-eb17-79d1-865f-4a8c254d1295',
            ],
            [
                'number' => '79999999999',
                'company_id' => '01993f26-d303-7fa1-af05-c440b2df57a4',
            ],
            [
                'number' => '79000000000',
                'company_id' => '01993f26-b6ba-7a60-a330-6c1b5af8b1d7',
            ],
        ]);
    }
}
