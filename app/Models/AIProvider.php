<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AIProvider extends Model
{
    protected $table = 'ai_providers';

    protected $fillable = [
        'name',
        'api_key',
        'model',
        'temperature',
        'top_p',
        'max_tokens',
        'system_prompt',
        'is_active',
    ];

    protected $casts = [
        'temperature' => 'float',
        'top_p' => 'float',
        'max_tokens' => 'integer',
        'is_active' => 'boolean',
    ];
}
