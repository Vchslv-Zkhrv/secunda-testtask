<?php

namespace App\DTOs\Requests\Company;

use App\DTOs\DTO;
use Schemantic\Attribute\Validate;
use OpenApi\Attributes as OA;

#[OA\Schema]
class UpdateCompanyRequest extends DTO
{
    public function __construct(
        #[Validate\Length(min: 2, max: 256)]
        #[OA\Property('name', type: 'string')]
        public readonly string $name,
    ) {
    }
}
