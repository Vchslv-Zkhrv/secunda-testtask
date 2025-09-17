<?php

namespace App\DTOs\Requests\BusinessActivity;

use App\DTOs\DTO;
use Schemantic\Attribute\Validate;
use OpenApi\Attributes as OA;

#[OA\Schema()]
class MoveBusinessActivityRequest extends DTO
{
    public function __construct(
        #[Validate\Validate('validateUuidOrNull', 'Uuids must follow RFC 9562 format')]
        #[OA\Property('parentId', type: 'string', nullable: true)]
        public readonly ?string $parentId,
    ){
    }
}
