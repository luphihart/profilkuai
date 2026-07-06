<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentMemory extends Model
{
    protected $table = 'student_memories';

    protected $fillable = ['student_id', 'key', 'value', 'confidence', 'source_message_id'];

    protected $casts = [
        'confidence' => 'float',
    ];

    /**
     * Relasi ke Siswa
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Relasi ke Pesan Sumber (jika ada)
     */
    public function sourceMessage(): BelongsTo
    {
        return $this->belongsTo(ConversationMessage::class, 'source_message_id');
    }
}
