<?php

namespace App\Services;

use App\Models\StudentMemory;
use App\Models\ConversationMessage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\AIProvider;

class MemoryEngine
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Analisis pesan siswa untuk mengekstrak informasi memori baru
     *
     * @param int $studentId ID Siswa
     * @param ConversationMessage $message Objek pesan dari siswa
     * @return array Memori baru yang berhasil diekstrak
     */
    public function extractMemories(int $studentId, ConversationMessage $message): array
    {
        $provider = AIProvider::where('is_active', true)->first();
        if (!$provider || empty($provider->api_key)) {
            return [];
        }

        $text = $message->message_text;

        $prompt = "Tugas Anda adalah menganalisis pesan dari siswa SMK dan mengekstrak informasi faktual tentang dirinya.
Kategori informasi yang dicari:
1. nickname (nama panggilan)
2. hobbies (hobi atau kegemaran)
3. career_goals (cita-cita atau tujuan karir)
4. interests (minat belajar/topik yang disukai)
5. problems (masalah, keluhan, hambatan yang sedang dihadapi)
6. achievements (prestasi atau hal membanggakan yang pernah diraih)
7. family (informasi tentang keluarga, pekerjaan ortu, hubungan keluarga)
8. friends (hubungan pertemanan, sahabat karib, kelompok sosial)

Pesan siswa: \"{$text}\"

Berikan respon HANYA dalam format JSON valid tanpa markdown, dengan key yang sesuai dari kategori di atas jika ditemukan informasi baru. Jika kategori tidak ditemukan, jangan sertakan key-nya di JSON.
Contoh output jika siswa berkata \"Aku suka ngoding Laravel dan hobi main bola, tapi sering pusing kalau tugas matematika menumpuk\":
{
  \"interests\": \"ngoding Laravel\",
  \"hobbies\": \"main bola\",
  \"problems\": \"pusing kalau tugas matematika menumpuk\"
}
Jika tidak ada informasi baru, kembalikan JSON kosong: {}";

        try {
            // Kita panggil API langsung untuk ekstraksi terstruktur
            $responseJson = '';
            if ($provider->name === 'gemini') {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$provider->model}:generateContent?key={$provider->api_key}";
                $payload = [
                    'contents' => [
                        ['role' => 'user', 'parts' => [['text' => $prompt]]]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.1,
                        'maxOutputTokens' => 300,
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
                    'temperature' => 0.1,
                    'max_tokens' => 300,
                ];
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $provider->api_key,
                    'Content-Type' => 'application/json'
                ])->post($url, $payload);
                if ($response->successful()) {
                    $responseJson = $response->json()['choices'][0]['message']['content'] ?? '{}';
                }
            }

            // Sanitasi output dari kemungkinan tag markdown ```json
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

            $extracted = json_decode($responseJson, true);
            if (!is_array($extracted)) {
                return [];
            }

            $savedMemories = [];
            foreach ($extracted as $key => $value) {
                if (empty($value)) continue;

                // Gabungkan jika berupa array untuk mencegah error 'Array to string conversion'
                $valStr = is_array($value) ? implode(', ', $value) : (string)$value;

                // Cek apakah data memori dengan key ini sudah ada
                $memory = StudentMemory::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'key' => $key
                    ],
                    [
                        'value' => $valStr,
                        'confidence' => 0.9,
                        'source_message_id' => $message->id
                    ]
                );

                $savedMemories[$key] = $valStr;
            }

            return $savedMemories;

        } catch (\Exception $e) {
            Log::error('Gagal mengekstrak memori siswa: ' . $e->getMessage());
            return [];
        }
    }
}
