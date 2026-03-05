<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleFeature extends Model
{
    protected $table = 'role_feature';

    protected $fillable = [
        'role_id',
        'feature_id',
        'uuid',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class, 'feature_id', 'id');
    }
}
