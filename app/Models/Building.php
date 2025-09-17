<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MatanYadaev\EloquentSpatial\Objects\Point;

/**
 * @property string                  $id
 * @property ?string                 $address
 * @property Point                   $coordinates
 * @property Collection<int,Company> $companies
 */
class Building extends Model
{
    use HasUuids;

    protected $table = 'buildings';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    public $fillable = [
        'id',
        'address',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'coordinates' => Point::class,
    ];

    /**
     * @return HasMany<Company,Building>
     */
    public function companies(): HasMany
    {
        return $this->hasMany(Company::class, 'building_id');
    }
}
