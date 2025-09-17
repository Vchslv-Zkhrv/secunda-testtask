<?php

namespace App\Services;

use App\DTOs\Model\BuildingDTO;
use App\DTOs\Spatial\Circle;
use App\DTOs\Spatial\Point;
use App\DTOs\Spatial\Rect;
use Illuminate\Support\Facades\DB;

class BuildingService
{
    /**
     * @return BuildingDTO[]
     */
    public function findInRect(Rect $rect): array
    {
        $sql = <<<SQL
        SELECT
            id,
            address,
            ST_X(coordinates) AS "latitude",
            ST_Y(coordinates) AS "longitude"
        FROM
            "buildings"
        WHERE
            ST_Within(coordinates, {$rect->toSQL()})
        SQL;

        $rows = DB::select($sql);

        $buildings = [];
        foreach ($rows as $row) {
            $buildings[] = new BuildingDTO(
                id: $row->id,
                address: $row->address,
                coordinates: new Point(
                    $row->latitude,
                    $row->longitude,
                ),
            );
        }

        return $buildings;
    }

    /**
     * @return BuildingDTO[]
     */
    public function findInCircle(Circle $circle): array
    {
        $sql = <<<SQL
        SELECT
            id,
            address,
            ST_X(coordinates) AS "latitude",
            ST_Y(coordinates) AS "longitude"
        FROM
            "buildings"
        WHERE
            ST_DistanceSphere(coordinates, {$circle->center->toSQL()}) <= {$circle->radius}
        SQL;

        $rows = DB::select($sql);

        $buildings = [];
        foreach ($rows as $row) {
            $buildings[] = new BuildingDTO(
                id: $row->id,
                address: $row->address,
                coordinates: new Point(
                    $row->latitude,
                    $row->longitude,
                ),
            );
        }

        return $buildings;
    }
}
