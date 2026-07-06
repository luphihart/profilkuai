<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evidence extends Model
{
    protected $table = 'evidence';

    protected $fillable = [
        'student_id',
        'domain_id',
        'indicator',
        'excerpt',
        'weight',
        'reasoning',
        'source_message_id',
    ];

    protected $casts = [
        'weight' => 'float',
    ];

    /**
     * Relasi ke Siswa
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Relasi ke Domain KB
     */
    public function domain(): BelongsTo
    {
        return $this->belongsTo(KnowledgeBaseDomain::class, 'domain_id');
    }

    /**
     * Relasi ke Pesan Sumber
     */
    public function sourceMessage(): BelongsTo
    {
        return $this->belongsTo(ConversationMessage::class, 'source_message_id');
    }
}
