<?php

namespace App\DTOs\Requests\Company;

use App\DTOs\DTO;
use Schemantic\Attribute\Validate;
use OpenApi\Attributes as OA;

#[OA\Schema()]
class CreateCompanyRequest extends DTO
{
    /**
     * @param string   $id
     * @param string   $name
     * @param string[] $phones
     */
    public function __construct(
        #[Validate\Validate('validateUuid', 'Uuids must follow RFC 9562 format')]
        #[OA\Property('id', type: 'string')]
        public readonly string $id,

        #[Validate\Length(min: 2, max: 256)]
        #[OA\Property('name', type: 'string')]
        public readonly string $name,

        #[Validate\Validate('validateUuid', 'Uuids must follow RFC 9562 format')]
        #[OA\Property('buildingId', type: 'string')]
        public readonly string $buildingId,
    ) {
    }
}
