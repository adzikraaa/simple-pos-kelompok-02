<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdempotencyKey extends Model
{
    protected $fillable = ['key', 'request_path', 'request_body', 'response_status', 'response_body'];

    protected $casts = [
        'request_body' => 'array',
        'response_body' => 'array',
    ];
}