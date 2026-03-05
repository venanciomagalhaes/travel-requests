<?php

namespace App\Models;

use App\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TravelRequest extends UserScope
{
    use HasFactory, SoftDeletes;

    protected $table = 'travel_requests';

    protected $fillable = [
        'uuid',
        'travelers_name',
        'destination',
        'departure_date',
        'return_date',
        'status',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'departure_date' => 'date',
            'return_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
