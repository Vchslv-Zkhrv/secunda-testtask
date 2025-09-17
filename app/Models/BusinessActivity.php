<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property string $id
 * @property string $name
 */
class BusinessActivity extends Model
{
    const MAX_DEPTH = 3;

    use HasUuids;

    protected $table = 'business_activities';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    public $fillable = [
        'id',
        'name',
    ];

    /**
     * @return BelongsToMany<Company,BusinessActivity,Pivot>
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_activity', 'activity_id', 'company_id');
    }
}
