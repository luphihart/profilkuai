<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    // Laporan hanya memiliki created_at, tidak ada updated_at bawaan karena bersifat arsip historis
    const UPDATED_AT = null;

    protected $fillable = [
        'student_id',
        'executive_summary',
        'personality_analysis',
        'strengths',
        'development_areas',
        'interests',
        'talents',
        'problems',
        'motivation',
        'career_goals',
        'confidence_scores_json',
        'evidence_json',
        'student_recommendations',
        'bk_recommendations',
        'wali_recommendations',
        'parent_recommendations',
        'follow_up_plan',
        'created_at',
    ];

    protected $casts = [
        'confidence_scores_json' => 'array',
        'evidence_json' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Relasi ke Siswa
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
