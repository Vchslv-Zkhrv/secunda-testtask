<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property string             $id
 * @property string             $name
 * @property \DateTimeImmutable $created_at
 * @property \DateTimeImmutable $updated_at
 */
class Company extends Model
{
    protected $table = 'companies';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = true;

    public $fillable = [
        'id',
        'name',
    ];

    /**
     * @return BelongsTo<Building,Company>
     */
    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    /**
     * @return HasOne<Phone,Company>
     */
    public function phones(): HasMany
    {
        return $this->hasOne(Phone::class);
    }

    /**
     * @return BelongsToMany<BusinessActivity,Company,Pivot>
     */
    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(BusinessActivity::class, 'company_activity', 'company_id', 'activity_id');
    }
}
