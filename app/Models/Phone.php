<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string             $number
 * @property \DateTimeImmutable $created_at
 * @property Company            $company
 */
class Phone extends Model
{
    protected $table = 'phones';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    public $fillable = [
        'number',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<Company,Model>
     */
    public function company(): BelongsTo
    {
        return new BelongsTo(Company::class);
    }
}
