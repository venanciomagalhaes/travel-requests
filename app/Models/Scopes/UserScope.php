<?php

namespace App\Models\Scopes;

use App\Enums\V1\Role\RolesNamesEnum;
use Illuminate\Database\Eloquent\Model;

class UserScope extends Model
{
    protected static function booted(): void
    {
        static::addGlobalScope('user_scope', function ($builder) {
            if (auth()->check() && ! auth()->user()->hasRole(RolesNamesEnum::ADMINISTRATOR)) {
                $builder->where('user_id', auth()->id());
            }
        });
    }
}
