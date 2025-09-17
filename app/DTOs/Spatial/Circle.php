<?php

namespace App\DTOs\Spatial;

use App\DTOs\DTO;
use OpenApi\Attributes as OA;

#[OA\Schema()]
class Circle extends DTO
{
    public function __construct(
        #[OA\Property('center', ref: Point::class)]
        public readonly Point $center,

        #[OA\Property('radius', type: 'float')]
        public readonly float $radius,
    ) {
    }
}
