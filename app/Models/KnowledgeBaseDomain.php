<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KnowledgeBaseDomain extends Model
{
    protected $table = 'knowledge_base_domains';

    protected $fillable = [
        'name',
        'category',
        'description',
        'indicators',
        'keywords',
        'synonyms',
        'example_behaviors',
        'exploration_questions',
        'follow_up_questions',
        'recommendations',
        'evidence_weight',
    ];

    /**
     * Konversi tipe data otomatis saat diakses di Eloquent
     */
    protected $casts = [
        'indicators' => 'array',
        'keywords' => 'array',
        'synonyms' => 'array',
        'example_behaviors' => 'array',
        'exploration_questions' => 'array',
        'follow_up_questions' => 'array',
        'recommendations' => 'array',
        'evidence_weight' => 'float',
    ];

    /**
     * Relasi ke Bukti (Evidence)
     */
    public function evidence(): HasMany
    {
        return $this->hasMany(Evidence::class, 'domain_id');
    }

    /**
     * Relasi ke Skor Keyakinan (Confidence Scores)
     */
    public function confidenceScores(): HasMany
    {
        return $this->hasMany(ConfidenceScore::class, 'domain_id');
    }
}
