<?php

namespace App\DTO\Model;

use App\DTO\DTO;
use Schemantic\Attribute\DateTimeFormat;

#[DateTimeFormat('Y-m-d H:i:s')]
abstract class ModelDTO extends DTO
{
}
