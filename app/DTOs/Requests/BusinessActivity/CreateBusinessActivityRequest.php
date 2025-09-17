<?php

namespace App\DTOs\Requests\BusinessActivity;

use App\DTOs\DTO;
use Schemantic\Attribute\Validate;
use OpenApi\Attributes as OA;

#[OA\Schema()]
class CreateBusinessActivityRequest extends DTO
{
    public function __construct(
        #[Validate\Validate('validateUuid', 'Uuids must follow RFC 9562 format')]
        #[OA\Property('id', type: 'string')]
        public readonly string $id,

        #[Validate\Length(min: 1, max: 256)]
        #[OA\Property('name', type: 'string')]
        public readonly string $name,

        #[Validate\Validate('validateUuidOrNull', 'Uuids must follow RFC 9562 format')]
        #[OA\Property('parentId', type: 'string', nullable: true)]
        public readonly ?string $parentId = null,
    ) {
    }
}
