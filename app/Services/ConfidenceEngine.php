<?php

namespace App\Services;

use App\Models\Evidence;
use App\Models\KnowledgeBaseDomain;
use App\Models\ConfidenceScore;
use Illuminate\Support\Facades\DB;

class ConfidenceEngine
{
    /**
     * Hitung ulang skor keyakinan untuk semua domain siswa berdasarkan bukti yang terkumpul
     *
     * @param int $studentId ID Siswa
     * @return array Array berisi [domain_id => score] yang diperbarui
     */
    public function recalculateScores(int $studentId): array
    {
        $domains = KnowledgeBaseDomain::all();
        $updatedScores = [];

        foreach ($domains as $domain) {
            // Ambil total bobot bukti untuk domain ini
            $totalWeight = Evidence::where('student_id', $studentId)
                ->where('domain_id', $domain->id)
                ->sum('weight');

            // Hitung target bobot berdasarkan ketetapan domain (misal target dasar = 1.5)
            // Semakin tinggi evidence_weight di KB, semakin sulit/banyak bukti yang dibutuhkan untuk mencapai 100%
            $targetWeight = 1.5 * ($domain->evidence_weight ?: 1.0);

            // Hitung persentase skor
            $score = 0;
            if ($totalWeight > 0) {
                $score = min(100, (int) round(($totalWeight / $targetWeight) * 100));
            }

            // Simpan atau perbarui skor
            ConfidenceScore::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'domain_id' => $domain->id
                ],
                [
                    'score' => $score
                ]
            );

            $updatedScores[$domain->id] = $score;
        }

        return $updatedScores;
    }

    /**
     * Dapatkan status kelayakan pemahaman domain
     *
     * @param int $studentId ID Siswa
     * @return array [domain_name => ['score' => int, 'need_more' => bool]]
     */
    public function getSummary(int $studentId): array
    {
        $scores = ConfidenceScore::with('domain')
            ->where('student_id', $studentId)
            ->get();

        $summary = [];
        foreach ($scores as $s) {
            $summary[$s->domain->name] = [
                'score' => $s->score,
                'need_more' => $s->score < 70 // Aturan: di bawah 70% butuh eksplorasi tambahan
            ];
        }

        return $summary;
    }
}
