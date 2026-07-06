<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    protected $fillable = [
        'name',
        'category',
        'priority',
        'trigger_condition',
        'action',
        'description',
        'parameters',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
