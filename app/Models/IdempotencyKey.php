<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Builder;

class IdempotencyKey extends Model
{
    use HasFactory, Prunable;

    protected $fillable = [
        'key',
        'user_id',
        'request_hash',
        'response_status',
        'response_body',
    ];

    protected $casts = [
        'response_body' => 'array',
    ];

    public function prunable(): Builder
    {
        return static::where('created_at', '<=', now()->subHours(24));
    }
}