<?php

namespace App\DTOs\Requests\Building;

use App\DTOs\DTO;
use App\DTOs\Spatial\Point;
use Schemantic\Attribute\Validate;
use OpenApi\Attributes as OA;

#[OA\Schema()]
class UpdateBuildingRequest extends DTO
{
    public function __construct(
        #[Validate\NotEmpty()]
        #[OA\Property('address', type: 'string')]
        public readonly string $address,

        #[OA\Property('coordinates', ref: Point::class)]
        public readonly Point $coordinates,
    ) {
    }
}
