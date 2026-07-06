<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherNote extends Model
{
    protected $table = 'teacher_notes';

    protected $fillable = ['teacher_id', 'student_id', 'note_text'];

    /**
     * Relasi ke Guru yang menulis catatan
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Relasi ke Siswa yang dicatat
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
