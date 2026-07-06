<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolClass extends Model
{
    // Menggunakan tabel 'classes' karena 'class' adalah reserved keyword di PHP
    protected $table = 'classes';

    protected $fillable = ['name', 'major_id', 'homeroom_teacher_id'];

    /**
     * Relasi ke Jurusan
     */
    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class, 'major_id');
    }

    /**
     * Relasi ke Wali Kelas (User)
     */
    public function homeroomTeacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'homeroom_teacher_id');
    }

    /**
     * Relasi ke Siswa-Siswa di kelas ini
     */
    public function students(): HasMany
    {
        return $this->hasMany(User::class, 'class_id');
    }
}
