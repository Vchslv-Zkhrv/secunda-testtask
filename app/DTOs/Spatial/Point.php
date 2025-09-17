<?php

namespace App\DTOs\Spatial;

use App\DTOs\DTO;
use OpenApi\Attributes as OA;

#[OA\Schema()]
class Point extends DTO
{
    public function __construct(
        #[OA\Property('latitude', type: 'float')]
        public readonly float $latitude,

        #[OA\Property('longitude', type: 'float')]
        public readonly float $longitude,
    ) {
    }

    public function toSQL(): string
    {
        return "ST_Point($this->longitude, $this->latitude)";
    }
}
