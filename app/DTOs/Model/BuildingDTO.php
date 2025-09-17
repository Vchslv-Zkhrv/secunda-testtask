<?php

namespace App\DTOs\Model;

use App\DTOs\DTO;
use App\DTOs\Spatial\Point;
use App\Models\Building;
use Schemantic\Attribute\Validate;
use OpenApi\Attributes as OA;

#[OA\Schema()]
class BuildingDTO extends DTO
{
    public function __construct(
        #[Validate\Validate('validateUuid', 'Uuids must follow RFC 9562 format')]
        #[OA\Property('id', type: 'string')]
        public readonly string $id,

        #[Validate\Length(2, 256)]
        #[OA\Property('address', type: 'string')]
        public readonly string $address,

        #[OA\Property('coordinates', ref: Point::class)]
        public readonly Point $coordinates,
    ) {
    }

    public static function fromModel(Building $building):  BuildingDTO
    {
        return new BuildingDTO(
            id: $building->id,
            address: $building->address,
            coordinates: new Point(
                $building->coordinates->latitude,
                $building->coordinates->longitude,
            )
        );
    }
}
