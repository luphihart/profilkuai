<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Major extends Model
{
    protected $fillable = ['name', 'code'];

    /**
     * Relasi ke Kelas
     */
    public function schoolClasses(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'major_id');
    }
}
