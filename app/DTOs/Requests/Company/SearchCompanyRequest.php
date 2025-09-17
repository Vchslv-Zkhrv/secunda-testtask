<?php

namespace App\DTOs\Requests\Company;

use App\DTOs\DTO;
use OpenApi\Attributes as OA;

#[OA\Schema()]
class SearchCompanyRequest extends DTO
{
    public function __construct(
        #[OA\Property('search', type: 'string', nullable: true)]
        public readonly ?string $search,
    ) {
    }
}
