<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * API token model
 *
 * @property Uuid                $id
 * @property \DateTimeImmutable  $created_at
 * @property ?\DateTimeImmutable $valid_till
 * @property ?\DateTimeImmutable $deleted_at
 */
class ApiKey extends Model
{
    use HasUuids;

    protected $table = 'api_key';

    public $timestamps = false;

    public $fillable = [
        'id',
        'created_at',
        'valid_till',
        'deleted_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'valid_till' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public static function extractIdFromString(string $string): UuidInterface
    {
        $length = strlen($string);
        if ($length < 36) {
            throw new \ValueError("String is too short: '$string'");
        } elseif ($length == 36) {
            return Uuid::fromString($string);
        } else {
            $string = base64_decode($string);
            if (empty($string)) {
                throw new \ValueError("String isn't base64-encoded: '$string'");
            }
            $string = substr($string, 0, 36);
            return Uuid::fromString($string);
        }
    }

    public function toTokenValue(): string
    {
        $id = (string)$this->id;
        return base64_encode($id . md5($id . env('APP_KEY')));
    }
}
