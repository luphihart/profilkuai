<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\StudentMemory;
use App\Models\ConfidenceScore;
use App\Models\KnowledgeBaseDomain;

class ReflectionEngine
{
    /**
     * Nama-nama tahap percakapan (1-12)
     */
    public const STAGES = [
        1 => 'Ice Breaking',
        2 => 'Profil Pribadi',
        3 => 'Kehidupan Sekolah',
        4 => 'Hobi',
        5 => 'Minat & Bakat',
        6 => 'Hubungan Pertemanan',
        7 => 'Keluarga',
        8 => 'Kepribadian',
        9 => 'Hambatan & Masalah',
        10 => 'Motivasi Belajar',
        11 => 'Tujuan Karir & Cita-Cita',
        12 => 'Penutup'
    ];

    /**
     * Evaluasi apakah tahapan saat ini sudah selesai dan tentukan tahapan berikutnya
     *
     * @param Conversation $conversation
     * @return array Hasil evaluasi: ['current_stage' => int, 'completed' => array, 'missing' => array]
     */
    public function evaluateProgress(Conversation $conversation): array
    {
        $studentId = $conversation->student_id;
        $currentStage = $conversation->current_stage;

        // Ambil semua memori siswa
        $memories = StudentMemory::where('student_id', $studentId)->pluck('value', 'key')->toArray();

        // Hitung jumlah pesan dalam tahapan saat ini
        $messageCountInStage = $conversation->messages()
            ->where('created_at', '>=', $conversation->updated_at ?: now())
            ->count();

        // Cari tahu domain apa saja yang sudah terisi cukup tinggi (skor > 70%)
        $highConfidenceDomainIds = ConfidenceScore::where('student_id', $studentId)
            ->where('score', '>=', 70)
            ->pluck('domain_id')
            ->toArray();

        $completedStages = [];
        $missingStages = [];

        // Logika kelayakan per tahapan
        for ($i = 1; $i <= 12; $i++) {
            $isCompleted = false;

            switch ($i) {
                case 1: // Ice Breaking
                    // Selesai jika pesan > 1
                    $isCompleted = $messageCountInStage > 1 || $currentStage > 1;
                    break;
                case 2: // Profil Pribadi
                    // Selesai jika nama panggilan (nickname) terisi
                    $isCompleted = isset($memories['nickname']) || $currentStage > 2;
                    break;
                case 3: // Kehidupan Sekolah
                    // Selesai jika ada memori tentang sekolah, atau pesan > 2
                    $isCompleted = $messageCountInStage > 2 || $currentStage > 3;
                    break;
                case 4: // Hobi
                    $isCompleted = isset($memories['hobbies']) || $currentStage > 4;
                    break;
                case 5: // Minat & Bakat
                    // Cek apakah domain RPL atau Minat terpantau tinggi
                    $isCompleted = count($highConfidenceDomainIds) > 0 || $currentStage > 5;
                    break;
                case 6: // Teman
                    $isCompleted = isset($memories['friends']) || $currentStage > 6;
                    break;
                case 7: // Keluarga
                    $isCompleted = isset($memories['family']) || $currentStage > 7;
                    break;
                case 8: // Kepribadian
                    $isCompleted = $messageCountInStage > 2 || $currentStage > 8;
                    break;
                case 9: // Masalah
                    $isCompleted = isset($memories['problems']) || $currentStage > 9;
                    break;
                case 10: // Motivasi
                    $isCompleted = $messageCountInStage > 2 || $currentStage > 10;
                    break;
                case 11: // Karir
                    $isCompleted = isset($memories['career_goals']) || $currentStage > 11;
                    break;
                case 12: // Penutup
                    $isCompleted = $conversation->status === 'completed';
                    break;
            }

            if ($isCompleted) {
                $completedStages[] = self::STAGES[$i];
            } else {
                $missingStages[] = self::STAGES[$i];
            }
        }

        // Tentukan apakah kita harus naik tingkat (stage) dalam percakapan ini
        // Kita naik tingkat jika tingkat saat ini dianggap selesai, atau jika chat dalam tingkat ini sudah terlalu lama (e.g. > 3 turn)
        $shouldAdvance = false;
        if ($currentStage < 12) {
            $currentStageName = self::STAGES[$currentStage];
            $currentStageCompleted = in_array($currentStageName, $completedStages);
            
            // Aturan paksa maju agar percakapan tidak berputar-putar (max 4 pertukaran pesan per topik)
            if ($currentStageCompleted || $messageCountInStage >= 4) {
                $shouldAdvance = true;
            }
        }

        if ($shouldAdvance) {
            $conversation->current_stage += 1;
            $conversation->save();
            $currentStage = $conversation->current_stage;
        }

        return [
            'current_stage' => $currentStage,
            'completed' => $completedStages,
            'missing' => $missingStages
        ];
    }
}
