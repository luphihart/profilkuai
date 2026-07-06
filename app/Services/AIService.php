<?php

namespace App\Services;

use App\Models\AIProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    /**
     * Kirim percakapan ke model AI aktif (Gemini, OpenRouter, Groq, HuggingFace)
     *
     * @param array $messages Riwayat pesan, format: [['sender' => 'student'|'ai', 'message_text' => string]]
     * @param string|null $systemPromptOverride Jika ingin menimpa prompt bawaan
     * @param array $memories Memori siswa yang disisipkan ke sistem prompt
     * @return string
     */
    public function generateResponse(array $messages, ?string $systemPromptOverride = null, array $memories = []): string
    {
        $provider = AIProvider::where('is_active', true)->first();

        if (!$provider || empty($provider->api_key)) {
            Log::warning('AI Provider aktif belum dikonfigurasi atau API Key kosong.');
            return "Hai! Maaf ya, saat ini aku belum bisa berpikir dengan jernih karena kunci akses AI (API Key) belum disiapkan oleh Administrator. Coba beritahu guru BK atau admin sekolah ya agar mereka mengisinya di panel pengaturan. 😉";
        }

        // Susun System Prompt: SELALU gunakan prompt bawaan dari database sebagai fondasi,
        // lalu tambahkan instruksi tambahan (override) jika ada
        $systemPrompt = $provider->system_prompt;
        if ($systemPromptOverride) {
            $systemPrompt .= "\n\n--- INSTRUKSI TAMBAHAN ---\n" . $systemPromptOverride;
        }
        if (!empty($memories)) {
            $systemPrompt .= "\n\nInformasi penting tentang siswa yang sudah kamu ketahui (JANGAN TANYAKAN LAGI):\n";
            foreach ($memories as $key => $val) {
                $systemPrompt .= "- " . ucfirst(str_replace('_', ' ', $key)) . ": " . $val . "\n";
            }
        }

        try {
            if ($provider->name === 'gemini') {
                return $this->callGemini($provider, $messages, $systemPrompt);
            } elseif ($provider->name === 'openrouter') {
                return $this->callOpenRouter($provider, $messages, $systemPrompt);
            } elseif ($provider->name === 'groq') {
                return $this->callGroq($provider, $messages, $systemPrompt);
            } elseif ($provider->name === 'huggingface') {
                return $this->callHuggingFace($provider, $messages, $systemPrompt);
            }
        } catch (\Exception $e) {
            Log::error('Kesalahan panggilan API AI: ' . $e->getMessage());
            return "Aduh, sepertinya ada sedikit kendala koneksi ke otak AI-ku nih. Coba kirim pesan lagi dalam beberapa saat ya! 🥺";
        }

        return "Format provider AI tidak dikenali.";
    }

    private function callGemini(AIProvider $provider, array $messages, string $systemPrompt): string
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$provider->model}:generateContent?key={$provider->api_key}";

        // Format history ke format Gemini
        // Gemini mengharapkan role: 'user' atau 'model'
        $contents = [];
        foreach ($messages as $msg) {
            $role = ($msg['sender'] === 'student') ? 'user' : 'model';
            $contents[] = [
                'role' => $role,
                'parts' => [
                    ['text' => $msg['message_text']]
                ]
            ];
        }

        $payload = [
            'contents' => $contents,
            'systemInstruction' => [
                'parts' => [
                    ['text' => $systemPrompt]
                ]
            ],
            'generationConfig' => [
                'temperature' => $provider->temperature,
                'topP' => $provider->top_p,
                'maxOutputTokens' => $provider->max_tokens,
            ]
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post($url, $payload);

        if ($response->failed()) {
            Log::error('Gemini API Error: ' . $response->body());
            throw new \Exception('Gemini API returned error: ' . $response->status());
        }

        $resData = $response->json();
        return $resData['candidates'][0]['content']['parts'][0]['text'] ?? 'Maaf, saya tidak menerima respons yang valid.';
    }

    private function callOpenRouter(AIProvider $provider, array $messages, string $systemPrompt): string
    {
        $url = "https://openrouter.ai/api/v1/chat/completions";

        $apiMessages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        foreach ($messages as $msg) {
            $role = ($msg['sender'] === 'student') ? 'user' : 'assistant';
            $apiMessages[] = [
                'role' => $role,
                'content' => $msg['message_text']
            ];
        }

        $payload = [
            'model' => $provider->model,
            'messages' => $apiMessages,
            'temperature' => $provider->temperature,
            'top_p' => $provider->top_p,
            'max_tokens' => $provider->max_tokens,
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $provider->api_key,
            'Content-Type' => 'application/json',
            'HTTP-Referer' => 'http://profilku.ai',
            'X-Title' => 'Profilku AI'
        ])->post($url, $payload);

        if ($response->failed()) {
            Log::error('OpenRouter API Error: ' . $response->body());
            throw new \Exception('OpenRouter API returned error: ' . $response->status());
        }

        $resData = $response->json();
        return $resData['choices'][0]['message']['content'] ?? 'Maaf, saya tidak menerima respons yang valid.';
    }

    private function callGroq(AIProvider $provider, array $messages, string $systemPrompt): string
    {
        $url = "https://api.groq.com/openai/v1/chat/completions";

        $apiMessages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        foreach ($messages as $msg) {
            $role = ($msg['sender'] === 'student') ? 'user' : 'assistant';
            $apiMessages[] = [
                'role' => $role,
                'content' => $msg['message_text']
            ];
        }

        $payload = [
            'model' => $provider->model,
            'messages' => $apiMessages,
            'temperature' => $provider->temperature,
            'top_p' => $provider->top_p,
            'max_tokens' => $provider->max_tokens,
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $provider->api_key,
            'Content-Type' => 'application/json',
        ])->post($url, $payload);

        if ($response->failed()) {
            Log::error('Groq API Error: ' . $response->body());
            throw new \Exception('Groq API returned error: ' . $response->status());
        }

        $resData = $response->json();
        return $resData['choices'][0]['message']['content'] ?? 'Maaf, saya tidak menerima respons yang valid.';
    }

    private function callHuggingFace(AIProvider $provider, array $messages, string $systemPrompt): string
    {
        $url = "https://api-inference.huggingface.co/v1/chat/completions";

        $apiMessages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        foreach ($messages as $msg) {
            $role = ($msg['sender'] === 'student') ? 'user' : 'assistant';
            $apiMessages[] = [
                'role' => $role,
                'content' => $msg['message_text']
            ];
        }

        $payload = [
            'model' => $provider->model,
            'messages' => $apiMessages,
            'temperature' => $provider->temperature,
            'max_tokens' => $provider->max_tokens,
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $provider->api_key,
            'Content-Type' => 'application/json',
        ])->post($url, $payload);

        if ($response->failed()) {
            Log::error('Hugging Face API Error: ' . $response->body());
            throw new \Exception('Hugging Face API returned error: ' . $response->status());
        }

        $resData = $response->json();
        return $resData['choices'][0]['message']['content'] ?? 'Maaf, saya tidak menerima respons yang valid.';
    }
}
