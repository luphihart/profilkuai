<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\User;
use App\Models\StudentMemory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConversationService
{
    protected $aiService;
    protected $memoryEngine;
    protected $evidenceEngine;
    protected $confidenceEngine;
    protected $reflectionEngine;
    protected $ruleEngine;

    public function __construct(
        AIService $aiService,
        MemoryEngine $memoryEngine,
        EvidenceEngine $evidenceEngine,
        ConfidenceEngine $confidenceEngine,
        ReflectionEngine $reflectionEngine,
        RuleEngine $ruleEngine
    ) {
        $this->aiService = $aiService;
        $this->memoryEngine = $memoryEngine;
        $this->evidenceEngine = $evidenceEngine;
        $this->confidenceEngine = $confidenceEngine;
        $this->reflectionEngine = $reflectionEngine;
        $this->ruleEngine = $ruleEngine;
    }

    /**
     * Menghasilkan sapaan pembuka dari AI menggunakan system prompt yang dikonfigurasi admin
     *
     * @param User $student
     * @return string Sapaan pembuka AI
     */
    public function generateGreeting(User $student): string
    {
        $greetingInstruction = "Ini adalah pesan pembuka pertamamu. Belum ada pesan sebelumnya dari siswa.\n"
            . "Tugas: Perkenalkan dirimu sesuai kepribadianmu, jelaskan tujuan percakapan secara singkat, "
            . "lalu ajukan satu pertanyaan pembuka ringan (misalnya tanyakan nama panggilan siswa). "
            . "Maksimal 3 kalimat. Gunakan 1-2 emoji agar akrab.";

        return $this->aiService->generateResponse([], $greetingInstruction);
    }

    /**
     * Memproses pesan masuk dari siswa dan mengembalikan tanggapan AI
     *
     * @param User $student
     * @param string $messageText
     * @return string Tanggapan AI
     */
    public function handleStudentMessage(User $student, string $messageText): string
    {
        return DB::transaction(function () use ($student, $messageText) {
            // 1. Cari atau buat percakapan aktif siswa
            $conversation = Conversation::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'status' => 'active'
                ],
                [
                    'current_stage' => 1
                ]
            );

            // 2. Simpan pesan siswa ke database
            $studentMessage = ConversationMessage::create([
                'conversation_id' => $conversation->id,
                'sender' => 'student',
                'message_text' => $messageText
            ]);

            // 3. Panggil Memory Engine untuk ekstraksi fakta siswa
            $this->memoryEngine->extractMemories($student->id, $studentMessage);

            // 4. Panggil Evidence Engine untuk mencatat bukti kecocokan domain
            $this->evidenceEngine->analyzeEvidence($student->id, $studentMessage);

            // 5. Panggil Confidence Engine untuk memperbarui skor keyakinan domain
            $this->confidenceEngine->recalculateScores($student->id);

            // 6. Panggil Reflection Engine untuk mengevaluasi kelayakan & transisi tahapan
            $reflection = $this->reflectionEngine->evaluateProgress($conversation);
            $stageText = ReflectionEngine::STAGES[$reflection['current_stage']];

            // 7. Panggil Rule Engine untuk mendapatkan instruksi alur dinamis
            $ruleInstructions = $this->ruleEngine->evaluateRules($messageText, $conversation);

            // 8. Kumpulkan seluruh riwayat percakapan saat ini
            $history = $conversation->messages()->orderBy('id', 'asc')->get()->map(function ($msg) {
                return [
                    'sender' => $msg->sender,
                    'message_text' => $msg->message_text
                ];
            })->toArray();

            // 9. Ambil semua memori terkumpul untuk disuntikkan ke prompt
            $memories = StudentMemory::where('student_id', $student->id)
                ->pluck('value', 'key')
                ->toArray();

            // 10. Susun instruksi khusus untuk tahapan saat ini
            $stageInstruction = "TAHAP PERCAKAPAN SAAT INI: Kamu sedang di Tahap {$reflection['current_stage']} dari 12 Tahap: '{$stageText}'. ";
            switch ($reflection['current_stage']) {
                case 1:
                    $stageInstruction .= "Lakukan ice breaking, sambut siswa dengan hangat, tanyakan kabarnya atau nama panggilannya.";
                    break;
                case 2:
                    $stageInstruction .= "Gali profil pribadinya (siapa namanya, asalnya, atau deskripsi singkat dirinya secara ramah).";
                    break;
                case 3:
                    $stageInstruction .= "Tanyakan tentang kehidupan sekolahnya di SMK ini (jurusan apa yang diambil, bagaimana belajarnya).";
                    break;
                case 4:
                    $stageInstruction .= "Gali apa hobi atau aktivitas favorit yang paling sering ia lakukan di waktu senggang.";
                    break;
                case 5:
                    $stageInstruction .= "Cari tahu apa minat terbesarnya (mata pelajaran atau keahlian praktis seperti coding, desain, dll).";
                    break;
                case 6:
                    $stageInstruction .= "Tanyakan secara santai tentang lingkungan pertemanannya di sekolah (apakah punya banyak teman, bagaimana kerja kelompok).";
                    break;
                case 7:
                    $stageInstruction .= "Tanyakan tentang keluarganya (hubungan dengan orang tua, saudara, atau suasana di rumah secara hangat).";
                    break;
                case 8:
                    $stageInstruction .= "Gali bagaimana ia memandang kepribadian dirinya sendiri (apakah pemalu, asertif, mandiri, dll).";
                    break;
                case 9:
                    $stageInstruction .= "Cari tahu apakah ada hambatan, masalah, ketakutan, atau stres akademis yang sedang ia alami akhir-akhir ini.";
                    break;
                case 10:
                    $stageInstruction .= "Gali dorongan motivasi terbesarnya untuk terus sekolah dan belajar (apa mimpi yang menggerakkannya).";
                    break;
                case 11:
                    $stageInstruction .= "Tanyakan apa rencana atau cita-cita karirnya setelah lulus nanti (kuliah, langsung kerja, atau berwirausaha).";
                    break;
                case 12:
                    $stageInstruction .= "Ini adalah tahap penutup. Berikan pesan penyemangat (quotes atau dorongan positif) berdasarkan apa saja yang sudah kamu bicarakan, lalu akhiri percakapan dengan hangat.";
                    break;
            }

            // Gabungkan instruksi dasar dengan instruksi aturan & tahapan
            $combinedInstruction = $stageInstruction . "\n\n" . implode("\n", $ruleInstructions);

            // 11. Dapatkan respons AI dari AIService
            $aiResponseText = $this->aiService->generateResponse($history, $combinedInstruction, $memories);

            // 12. Simpan pesan AI ke database
            ConversationMessage::create([
                'conversation_id' => $conversation->id,
                'sender' => 'ai',
                'message_text' => $aiResponseText
            ]);

            // Jika masuk ke tahap 12 (Penutup) dan pesan AI selesai, tandai percakapan selesai
            if ($reflection['current_stage'] === 12 && ConversationMessage::where('conversation_id', $conversation->id)->where('sender', 'ai')->count() > 1) {
                // Tandai selesai agar tidak melanjutkan percakapan lagi, melainkan memicu report generation
                $conversation->status = 'completed';
                $conversation->save();
            }

            return $aiResponseText;
        });
    }
}
