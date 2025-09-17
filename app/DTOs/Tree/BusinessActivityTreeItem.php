<?php

namespace App\DTOs\Tree;

use App\DTOs\DTO;
use OpenApi\Attributes as OA;

#[OA\Schema()]
class BusinessActivityTreeItem extends DTO
{
    /**
     * @param string $id
     * @param string $name
     * @param array[] $childs
     */
    public function __construct(
        #[OA\Property('id', type: 'string')]
        public readonly string $id,

        #[OA\Property('name', type: 'string')]
        public readonly string $name,

        #[OA\Property(
            property: 'childs',
            type: 'array',
            items: new OA\Items(ref: BusinessActivityTreeItem::class)
        )]
        public readonly array $childs,
    ) {
    }
}
