<?php

namespace App\Services;

use App\Models\Evidence;
use App\Models\KnowledgeBaseDomain;
use App\Models\ConversationMessage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\AIProvider;

class EvidenceEngine
{
    /**
     * Menganalisis pesan siswa untuk melihat apakah ada domain KB yang terpicu
     *
     * @param int $studentId ID Siswa
     * @param ConversationMessage $message Objek pesan
     * @return array Daftar evidence yang baru disimpan
     */
    public function analyzeEvidence(int $studentId, ConversationMessage $message): array
    {
        $provider = AIProvider::where('is_active', true)->first();
        if (!$provider || empty($provider->api_key)) {
            return [];
        }

        // Ambil semua domain dari knowledge base
        $domains = KnowledgeBaseDomain::all();
        if ($domains->isEmpty()) {
            return [];
        }

        $text = $message->message_text;

        // Siapkan ringkasan domain untuk dikirim ke LLM
        $domainContexts = [];
        foreach ($domains as $d) {
            $domainContexts[] = [
                'id' => $d->id,
                'name' => $d->name,
                'category' => $d->category,
                'description' => $d->description,
                'indicators' => $d->indicators,
                'keywords' => $d->keywords
            ];
        }

        $domainsJson = json_encode($domainContexts);

        $prompt = "Tugas Anda adalah memetakan jawaban siswa SMK ke satu atau lebih domain psikologis/karakteristik/masalah sekolah dari daftar yang disediakan.
Daftar Domain Tersedia (dalam format JSON):
{$domainsJson}

Pesan Siswa yang dianalisis: \"{$text}\"

Tugas:
Analisis apakah pesan siswa tersebut mengindikasikan adanya kecocokan (baik positif seperti ketertarikan, maupun negatif seperti masalah/kecemasan) dengan domain di atas.
Jika YA, berikan pemetaan detail. Jika TIDAK, jangan masukkan domain tersebut.

Format output HANYA berupa JSON array valid tanpa markdown dengan struktur objek berikut:
[
  {
    \"domain_id\": [ID Domain dari list],
    \"indicator\": \"[Sebutkan salah satu indikator terdekat atau deskripsi indikasi yang terdeteksi]\",
    \"excerpt\": \"[Kutipan langsung bagian kalimat siswa yang menjadi bukti]\",
    \"weight\": [Bobot bukti antara 0.1 sampai 1.0 berdasarkan seberapa jelas siswa mengungkapkannya],
    \"reasoning\": \"[Penjelasan rasional mengapa kutipan tersebut menjadi bukti untuk domain ini]\"
  }
]
Contoh jika siswa berkata \"Aku sering deg-degan kalau disuruh ngomong didepan kelas\":
[
  {
    \"domain_id\": 1,
    \"indicator\": \"Yakin tampil di depan umum\",
    \"excerpt\": \"deg-degan kalau disuruh ngomong didepan kelas\",
    \"weight\": 0.7,
    \"reasoning\": \"Siswa menunjukkan kecemasan saat berbicara di depan kelas yang berpotensi menurunkan tingkat kepercayaan diri.\"
  }
]
Jika tidak ada domain yang cocok, kembalikan array kosong: []";

        try {
            $responseJson = '';
            if ($provider->name === 'gemini') {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$provider->model}:generateContent?key={$provider->api_key}";
                $payload = [
                    'contents' => [
                        ['role' => 'user', 'parts' => [['text' => $prompt]]]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.1,
                        'maxOutputTokens' => 600,
                    ]
                ];
                $response = Http::post($url, $payload);
                if ($response->successful()) {
                    $responseJson = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '[]';
                }
            } else {
                $url = "https://openrouter.ai/api/v1/chat/completions";
                $payload = [
                    'model' => $provider->model,
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                    'temperature' => 0.1,
                    'max_tokens' => 600,
                ];
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $provider->api_key,
                    'Content-Type' => 'application/json'
                ])->post($url, $payload);
                if ($response->successful()) {
                    $responseJson = $response->json()['choices'][0]['message']['content'] ?? '[]';
                }
            }

            // Sanitasi output dari kemungkinan tag markdown
            $responseJson = trim($responseJson);
            if (strpos($responseJson, '```json') === 0) {
                $responseJson = substr($responseJson, 7);
                if (substr($responseJson, -3) === '```') {
                    $responseJson = substr($responseJson, 0, -3);
                }
            } elseif (strpos($responseJson, '```') === 0) {
                $responseJson = substr($responseJson, 3);
                if (substr($responseJson, -3) === '```') {
                    $responseJson = substr($responseJson, 0, -3);
                }
            }
            $responseJson = trim($responseJson);

            $results = json_decode($responseJson, true);
            if (!is_array($results)) {
                return [];
            }

            $savedEvidence = [];
            foreach ($results as $item) {
                if (empty($item['domain_id'])) continue;

                // Pastikan domain_id benar-benar ada di DB
                $domainExists = KnowledgeBaseDomain::where('id', $item['domain_id'])->exists();
                if (!$domainExists) continue;

                $evidence = Evidence::create([
                    'student_id' => $studentId,
                    'domain_id' => $item['domain_id'],
                    'indicator' => $item['indicator'] ?? 'Umum',
                    'excerpt' => $item['excerpt'] ?? $text,
                    'weight' => floatval($item['weight'] ?? 0.5),
                    'reasoning' => $item['reasoning'] ?? 'Terdeteksi dari analisis percakapan.',
                    'source_message_id' => $message->id
                ]);

                $savedEvidence[] = $evidence;
            }

            return $savedEvidence;

        } catch (\Exception $e) {
            Log::error('Gagal menganalisis bukti percakapan: ' . $e->getMessage());
            return [];
        }
    }
}
