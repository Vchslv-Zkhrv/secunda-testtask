<?php

namespace App\DTOs\Requests\Building;

use App\DTOs\DTO;
use App\DTOs\Spatial\Point;
use Schemantic\Attribute\Validate;
use OpenApi\Attributes as OA;

#[OA\Schema()]
class CreateBuildingRequest extends DTO
{
    public function __construct(
        #[Validate\Validate('validateUuid', 'Uuids must follow RFC 9562 format')]
        #[OA\Property('id', type: 'string')]
        public readonly string $id,

        #[Validate\Length(2, 256)]
        #[OA\Property('address', type: 'address')]
        public readonly string $address,

        #[OA\Property('coordinates', ref: Point::class)]
        public readonly Point $coordinates,
    ) {
    }
}
