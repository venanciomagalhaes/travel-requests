<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Model;

class UserScope extends Model
{
    protected static function booted(): void
    {
        static::addGlobalScope('user_scope', function ($builder) {
            if (auth()->check()) {
                $builder->where('user_id', auth()->id());
            }
        });
    }
}
