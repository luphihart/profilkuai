<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\ConfidenceScore;
use App\Models\Rule;
use Illuminate\Support\Str;

class RuleEngine
{
    /**
     * Evaluasi aturan alur berdasarkan input murid dan status percakapan saat ini
     *
     * @param string $studentInput Jawaban mentah murid
     * @param Conversation $conversation
     * @return array Daftar instruksi sistem tambahan hasil evaluasi aturan
     */
    public function evaluateRules(string $studentInput, Conversation $conversation): array
    {
        $instructions = [];
        $studentId = $conversation->student_id;

        // 1. Aturan Keamanan (Safety - Prevent Psychological Diagnosis)
        // Jika tidak ada di DB (misal dalam unit test), default ke true
        $safetyActive = true;
        $safetyDb = Rule::where('category', 'Safety')
            ->where(function($q) {
                $q->where('name', 'Prevent Psychological Diagnosis')
                  ->orWhere('action', 'refuse_clinical_diagnosis');
            })
            ->first();
        if ($safetyDb) {
            $safetyActive = $safetyDb->is_active;
        }

        if ($safetyActive) {
            $instructions[] = "ATURAN UTAMA: Jangan pernah mendiagnosis gangguan mental klinis (seperti depresi klinis, OCD, bipolar, dll). Jika mendeteksi indikasi masalah serius (seperti perundungan berat atau tingkat kecemasan ekstrem), gunakan kalimat bermakna 'kemungkinan indikasi' atau 'perlu pembahasan lebih lanjut', lalu sarankan dengan ramah untuk menemui Guru BK (Ibu Susi).";
        }

        // 2. Evaluasi Panjang Jawaban (Short Answer - Response Too Short)
        $shortActive = true;
        $shortDb = Rule::where('category', 'Conversation')
            ->where(function($q) {
                $q->where('name', 'Response Too Short')
                  ->orWhere('name', 'Jawaban Terlalu Pendek')
                  ->orWhere('action', 'elaboration_prompt');
            })
            ->first();
        if ($shortDb) {
            $shortActive = $shortDb->is_active;
        }

        if ($shortActive) {
            $inputLength = strlen(trim($studentInput));
            if ($inputLength < 12) {
                $instructions[] = "ATURAN ALUR: Jawaban siswa sangat singkat. Jangan langsung melompat ke topik baru. Tanggapi dengan mengajak siswa menjabarkan pemikirannya secara lebih detail atau berikan contoh sederhana untuk memicu mereka bercerita.";
            }
        }

        // 3. Evaluasi Skor Keyakinan Rendah (Confidence - Confidence Below 70%)
        $confActive = true;
        $confDb = Rule::where('category', 'Confidence')
            ->where(function($q) {
                $q->where('name', 'Confidence Below 70%')
                  ->orWhere('name', 'Tingkat Keyakinan Rendah')
                  ->orWhere('action', 'deepen_exploration');
            })
            ->first();
        if ($confDb) {
            $confActive = $confDb->is_active;
        }

        if ($confActive) {
            $lowConfidenceDomains = ConfidenceScore::with('domain')
                ->where('student_id', $studentId)
                ->where('score', '<', 70)
                ->get();

            if ($lowConfidenceDomains->isNotEmpty()) {
                $domainNames = $lowConfidenceDomains->map(fn($sc) => $sc->domain->name)->implode(', ');
                $instructions[] = "ATURAN EKSPLORASI: Tingkat pemahaman kamu untuk domain [{$domainNames}] masih kurang dari 70%. Ajukan pertanyaan eksplorasi yang menggali aspek ini secara santai tanpa terkesan menginterogasi.";
            }
        }

        // 4. Evaluasi Konflik Emosi / Kontradiksi (Conversation - Contradictory Response)
        $contradictActive = true;
        $contradictDb = Rule::where('category', 'Conversation')
            ->where(function($q) {
                $q->where('name', 'Contradictory Response')
                  ->orWhere('action', 'highlight_contradiction');
            })
            ->first();
        if ($contradictDb) {
            $contradictActive = $contradictDb->is_active;
        }

        if ($contradictActive) {
            $lowerInput = Str::lower($studentInput);
            $hasConflictKeywords = (Str::contains($lowerInput, 'tapi') || Str::contains($lowerInput, 'namun') || Str::contains($lowerInput, 'meskipun')) 
                && (Str::contains($lowerInput, 'suka') && (Str::contains($lowerInput, 'males') || Str::contains($lowerInput, 'malas') || Str::contains($lowerInput, 'benci') || Str::contains($lowerInput, 'takut') || Str::contains($lowerInput, 'minder')));
            
            if ($hasConflictKeywords) {
                $instructions[] = "ATURAN KLARIFIKASI: Siswa menunjukkan pertentangan perasaan (misal: menyukai sesuatu tapi malas/takut). Coba validasi perasaannya, lalu klarifikasi apa yang sebenarnya membuat dirinya merasa terhambat.";
            }
        }

        // 5. Evaluasi Aturan Dinamis Lainnya dari Database
        $otherRules = Rule::where('is_active', true)
            ->whereNotIn('name', ['Response Too Short', 'Jawaban Terlalu Pendek', 'Prevent Psychological Diagnosis', 'Confidence Below 70%', 'Tingkat Keyakinan Rendah', 'Contradictory Response'])
            ->get();

        foreach ($otherRules as $rule) {
            $triggered = false;
            $cond = $rule->trigger_condition;

            switch ($cond) {
                case 'sensitive_content_detected':
                    $lowerInput = Str::lower($studentInput);
                    $sensitiveWords = ['bunuh diri', 'self harm', 'sayat', 'bully', 'perundungan', 'pukul', 'hajar', 'siksa', 'pelecehan', 'mati saja'];
                    foreach ($sensitiveWords as $word) {
                        if (Str::contains($lowerInput, $word)) {
                            $triggered = true;
                            break;
                        }
                    }
                    break;
                case 'ambiguous_keywords_detected':
                    $lowerInput = Str::lower($studentInput);
                    $ambiguousWords = ['mungkin', 'ragu', 'bingung', 'entahlah', 'kurang tahu', 'kurang tau', 'terserah'];
                    foreach ($ambiguousWords as $word) {
                        if (Str::contains($lowerInput, $word)) {
                            $triggered = true;
                            break;
                        }
                    }
                    break;
                case 'generic_fillers_detected':
                    $lowerInput = Str::lower($studentInput);
                    $fillers = ['ya gitu', 'gatau', 'biasa aja', 'oke', 'sip', 'ya', 'tidak', 'ga'];
                    foreach ($fillers as $word) {
                        if ($lowerInput === $word) {
                            $triggered = true;
                            break;
                        }
                    }
                    break;
                case 'career_goal_empty':
                    $hasGoal = \App\Models\StudentMemory::where('student_id', $studentId)->where('key', 'career_goal')->exists();
                    $triggered = !$hasGoal;
                    break;
                case 'career_goal_defined':
                    $hasGoal = \App\Models\StudentMemory::where('student_id', $studentId)->where('key', 'career_goal')->exists();
                    $triggered = $hasGoal;
                    break;
                case 'current_stage == 12':
                    $triggered = $conversation->current_stage == 12;
                    break;
            }

            if ($triggered) {
                $instructions[] = "ATURAN (" . strtoupper($rule->category) . " - " . strtoupper($rule->priority) . "): " . $rule->description . " -> Tindakan: " . $rule->action;
            }
        }

        return $instructions;
    }
}
