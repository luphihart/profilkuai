<?php

namespace App\Services;

use App\Models\User;
use App\Models\StudentMemory;
use App\Models\Evidence;
use App\Models\ConfidenceScore;
use App\Models\Report;
use App\Models\AIProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReportService
{
    /**
     * Hasilkan laporan profiling psikologis dan karir otomatis berbasis AI
     *
     * @param User $student Siswa subjek profiling
     * @return Report Laporan yang baru disimpan
     */
    public function generateReport(User $student): Report
    {
        $provider = AIProvider::where('is_active', true)->first();
        if (!$provider || empty($provider->api_key)) {
            throw new \Exception('AI Provider aktif belum dikonfigurasi.');
        }

        // 1. Kumpulkan Memori Siswa
        $memories = StudentMemory::where('student_id', $student->id)
            ->pluck('value', 'key')
            ->toArray();
        $memoriesText = json_encode($memories, JSON_PRETTY_PRINT);

        // 2. Kumpulkan Bukti Percakapan
        $evidence = Evidence::with('domain')
            ->where('student_id', $student->id)
            ->get()
            ->map(function ($ev) {
                return [
                    'domain' => $ev->domain->name,
                    'indicator' => $ev->indicator,
                    'excerpt' => $ev->excerpt,
                    'weight' => $ev->weight,
                    'reasoning' => $ev->reasoning
                ];
            })->toArray();
        $evidenceText = json_encode($evidence, JSON_PRETTY_PRINT);

        // 3. Kumpulkan Skor Keyakinan
        $scores = ConfidenceScore::with('domain')
            ->where('student_id', $student->id)
            ->get()
            ->mapWithKeys(function ($sc) {
                return [$sc->domain->name => $sc->score];
            })->toArray();
        $scoresText = json_encode($scores, JSON_PRETTY_PRINT);

        // 4. Susun prompt untuk pembuatan laporan komprehensif
        $prompt = "Tugas Anda adalah menyusun laporan profiling psikologis, potensi diri, dan karir komprehensif untuk siswa SMK bernama {$student->name} (Kelas: {$student->schoolClass->name}, Jurusan: {$student->schoolClass->major->name}).

Gunakan data mentah berikut untuk membuat analisis:

Data Memori Siswa:
{$memoriesText}

Bukti-bukti Percakapan Terdeteksi:
{$evidenceText}

Skor Keyakinan Domain Bimbingan:
{$scoresText}

Tugas Laporan:
Tulis analisis yang kaya, berwawasan mendalam, suportif, dan ramah (tidak klinis yang kaku, melainkan bersifat membimbing). Laporan ini harus memiliki bagian berikut dalam output JSON:
1. executive_summary: Rangkuman eksekutif gambaran umum siswa.
2. personality_analysis: Analisis kepribadian siswa (kekuatan dan karakternya).
3. strengths: Kelebihan atau poin positif siswa.
4. development_areas: Area pengembangan diri (hal-hal yang perlu ia perbaiki).
5. interests: Rangkuman minat belajar/jurusan.
6. talents: Analisis bakat/potensi.
7. problems: Masalah/kendala utama yang terdeteksi (seperti stres akademis, bullying, rasa minder, atau hambatan lainnya).
8. motivation: Gambaran dorongan motivasi belajarnya.
9. career_goals: Arah cita-cita karir dan kesesuaiannya dengan potensi diri.
10. student_recommendations: Rekomendasi konkret langkah mandiri yang bisa dicoba oleh siswa.
11. bk_recommendations: Rekomendasi bimbingan bagi Guru BK (Guru Bimbingan Konseling).
12. wali_recommendations: Rekomendasi praktis untuk Wali Kelas (Homeroom Teacher).
13. parent_recommendations: Rekomendasi kolaborasi untuk Orang Tua di rumah.
14. follow_up_plan: Rencana tindak lanjut bimbingan konseling ke depan.

Format output HANYA berupa JSON valid tanpa markdown, dengan key persis seperti di atas. JANGAN gunakan tag ```json di awal atau ``` di akhir, keluarkan plaintext JSON langsung.";

        try {
            $responseJson = '';
            if ($provider->name === 'gemini') {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$provider->model}:generateContent?key={$provider->api_key}";
                $payload = [
                    'contents' => [
                        ['role' => 'user', 'parts' => [['text' => $prompt]]]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.5,
                        'maxOutputTokens' => 4000,
                    ]
                ];
                $response = Http::post($url, $payload);
                if ($response->successful()) {
                    $responseJson = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
                }
            } else {
                $url = "https://openrouter.ai/api/v1/chat/completions";
                $payload = [
                    'model' => $provider->model,
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                    'temperature' => 0.5,
                    'max_tokens' => 4000,
                ];
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $provider->api_key,
                    'Content-Type' => 'application/json'
                ])->post($url, $payload);
                if ($response->successful()) {
                    $responseJson = $response->json()['choices'][0]['message']['content'] ?? '{}';
                }
            }

            // Ekstrak JSON murni dari kurung kurawal pembuka pertama hingga kurung penutup terakhir
            $responseJson = trim($responseJson);
            $firstBrace = strpos($responseJson, '{');
            $lastBrace = strrpos($responseJson, '}');
            if ($firstBrace !== false && $lastBrace !== false && $lastBrace > $firstBrace) {
                $responseJson = substr($responseJson, $firstBrace, $lastBrace - $firstBrace + 1);
            }

            $data = json_decode($responseJson, true);
            if (!is_array($data)) {
                throw new \Exception('Output laporan bukan format JSON valid. Teks mentah: ' . substr($responseJson, 0, 500));
            }

            // 5. Simpan Laporan ke Database
            $report = Report::create([
                'student_id' => $student->id,
                'executive_summary' => $this->sanitizeField($data['executive_summary'] ?? 'Laporan otomatis disusun.'),
                'personality_analysis' => $this->sanitizeField($data['personality_analysis'] ?? ''),
                'strengths' => $this->sanitizeField($data['strengths'] ?? ''),
                'development_areas' => $this->sanitizeField($data['development_areas'] ?? ''),
                'interests' => $this->sanitizeField($data['interests'] ?? ''),
                'talents' => $this->sanitizeField($data['talents'] ?? ''),
                'problems' => $this->sanitizeField($data['problems'] ?? ''),
                'motivation' => $this->sanitizeField($data['motivation'] ?? ''),
                'career_goals' => $this->sanitizeField($data['career_goals'] ?? ''),
                'confidence_scores_json' => $scores,
                'evidence_json' => $evidence,
                'student_recommendations' => $this->sanitizeField($data['student_recommendations'] ?? ''),
                'bk_recommendations' => $this->sanitizeField($data['bk_recommendations'] ?? ''),
                'wali_recommendations' => $this->sanitizeField($data['wali_recommendations'] ?? ''),
                'parent_recommendations' => $this->sanitizeField($data['parent_recommendations'] ?? ''),
                'follow_up_plan' => $this->sanitizeField($data['follow_up_plan'] ?? ''),
            ]);

            return $report;

        } catch (\Exception $e) {
            Log::error('Gagal menyusun laporan profiling AI: ' . $e->getMessage());
            
            // Return dummy report if failed
            return Report::create([
                'student_id' => $student->id,
                'executive_summary' => 'Gagal membuat analisis otomatis dari AI. Mohon periksa API Key Anda.',
                'personality_analysis' => 'Hubungi administrator sekolah.',
                'confidence_scores_json' => $scores,
                'evidence_json' => $evidence
            ]);
        }
    }

    /**
     * Sanitasi nilai field dari hasil AI yang mungkin berupa string atau array.
     */
    private function sanitizeField($value): string
    {
        if (is_array($value)) {
            return implode("\n", array_map(function($item) {
                if (is_array($item)) {
                    return json_encode($item);
                }
                $trimmed = trim($item);
                // Jika item tidak diawali bullet point atau nomor, tambahkan tanda hubung (-)
                if (strpos($trimmed, '-') !== 0 && strpos($trimmed, '*') !== 0 && !preg_match('/^\d+\./', $trimmed)) {
                    return '- ' . $trimmed;
                }
                return $trimmed;
            }, $value));
        }
        return trim((string)$value);
    }
}
