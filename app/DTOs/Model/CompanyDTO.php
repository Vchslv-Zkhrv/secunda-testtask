<?php

namespace App\DTOs\Model;

use App\DTOs\DTO;
use Schemantic\Attribute\Alias;
use Schemantic\Attribute\DateTimeFormat;
use OpenApi\Attributes as OA;

#[OA\Schema()]
#[DateTimeFormat('Y-m-d\TH:i:s.up')]
class CompanyDTO extends DTO
{
    public function __construct(
        #[OA\Property('id', type: 'string')]
        public readonly string $id,

        #[OA\Property('name', type: 'string')]
        public readonly string $name,

        #[Alias('buildingId')]
        #[OA\Property('buildingId', type: 'string')]
        public readonly string $building_id,

        #[Alias('createdAt')]
        #[OA\Property('createdAt', type: 'datetime')]
        public readonly \DateTimeImmutable $created_at,

        #[Alias('updatedAt')]
        #[OA\Property('updatedAt', type: 'datetime')]
        public readonly \DateTimeImmutable $updated_at,
    ) {
    }
}
