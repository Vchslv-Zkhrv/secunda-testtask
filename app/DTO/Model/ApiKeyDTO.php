<?php

namespace App\DTO\Model;

use App\DTO\Model\ModelDTO;
use Ramsey\Uuid\Uuid;
use Schemantic\Attribute\Alias;

class ApiKeyDTO extends ModelDTO
{
    public function __construct(
        public string|Uuid $id,

        #[Alias('created_at')]
        public \DateTimeImmutable $createdAt,

        #[Alias('valid_till')]
        public ?\DateTimeImmutable $validTill = null,

        #[Alias('deleted_at')]
        public ?\DateTimeImmutable $deletedAt = null,
    ) {
    }
}
