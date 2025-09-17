<?php

namespace App\DTOs\Requests\BusinessActivity;

use App\DTOs\DTO;
use Schemantic\Attribute\Validate;
use OpenApi\Attributes as OA;

#[OA\Schema()]
class UpdateBusinessActivityRequest extends DTO
{
    public function __construct(
        #[Validate\Length(min: 1, max: 256)]
        #[OA\Property('name', type: 'string')]
        public readonly string $name,
    ) {
    }
}
