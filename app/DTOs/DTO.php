<?php

namespace App\DTOs;

use Schemantic\Schema;

abstract class DTO extends Schema
{
    const UUID_REGEX = "[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}";

    public function validateUuid(string $uuid): bool
    {
        $matches = [];
        return (bool)preg_match('/' . self::UUID_REGEX . '/', $uuid, $matches);
    }

    public function validateUuidOrNUll(?string $uuid): bool
    {
        return $uuid === null || $this->validateUuid($uuid);
    }

    public function validatePhone(string $phone): bool
    {
        return is_numeric($phone) && strlen($phone) == 11;
    }
}
