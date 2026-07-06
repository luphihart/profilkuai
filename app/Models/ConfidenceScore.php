<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConfidenceScore extends Model
{
    protected $table = 'confidence_scores';

    protected $fillable = ['student_id', 'domain_id', 'score'];

    protected $casts = [
        'score' => 'integer',
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
}
