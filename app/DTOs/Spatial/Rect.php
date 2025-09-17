<?php

namespace App\DTOs\Spatial;

use App\DTOs\DTO;
use OpenApi\Attributes as OA;

#[OA\Schema()]
class Rect extends DTO
{
    public function __construct(
        #[OA\Property('topLeft', ref: Point::class)]
        public readonly Point $topLeft,

        #[OA\Property('bottomRight', ref: Point::class)]
        public readonly Point $bottomRight,
    ) {
    }

    public function toSQL(): string
    {
        return "ST_MakeEnvelope(".
            "{$this->topLeft->longitude}, {$this->topLeft->latitude}, " .
            "{$this->bottomRight->longitude}, {$this->bottomRight->latitude}" .
        ")";
    }
}
