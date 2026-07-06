<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\Evidence;
use App\Models\ConfidenceScore;
use App\Models\Report;
use App\Services\ConversationService;
use App\Services\ReflectionEngine;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    protected $conversationService;
    protected $reportService;

    public function __construct(ConversationService $conversationService, ReportService $reportService)
    {
        $this->conversationService = $conversationService;
        $this->reportService = $reportService;
    }

    /**
     * Tampilan Ruang Obrolan
     */
    public function index()
    {
        $student = Auth::user();

        // Cari percakapan aktif, jika tidak ada, buat baru
        $conversation = Conversation::firstOrCreate(
            [
                'student_id' => $student->id,
                'status' => 'active'
            ],
            [
                'current_stage' => 1
            ]
        );

        $messages = $conversation->messages()->orderBy('id', 'asc')->get();

        // Jika baru dimulai (messages = 0), kirim salam pembuka pertama via AI
        if ($messages->isEmpty()) {
            // Gunakan AI untuk menghasilkan sapaan pembuka sesuai system prompt yang dikonfigurasi admin
            try {
                $welcomeMsg = $this->conversationService->generateGreeting($student);
            } catch (\Exception $e) {
                // Fallback jika AI gagal dipanggil
                $welcomeMsg = "Halo! 👋 Aku Profilku AI, teman diskusimu di sini. Di sini kita akan mengobrol santai tentang dirimu, kegiatan sekolahmu, hobi, sampai cita-citamu nanti. Oh ya, kalau boleh tahu, siapa nama panggilan kesukaanmu? 😊";
            }
            
            ConversationMessage::create([
                'conversation_id' => $conversation->id,
                'sender' => 'ai',
                'message_text' => $welcomeMsg
            ]);

            $messages = $conversation->messages()->orderBy('id', 'asc')->get();
        }

        $currentStage = $conversation->current_stage;
        $stageName = ReflectionEngine::STAGES[$currentStage] ?? 'Profiling';
        $suggestionChips = $this->getSuggestionChips($currentStage);

        // Ambil data bukti untuk indikator UI
        $evidenceCount = Evidence::where('student_id', $student->id)->count();
        $averageConfidence = (int) ConfidenceScore::where('student_id', $student->id)->avg('score');

        return view('student.chat', compact(
            'conversation',
            'messages',
            'currentStage',
            'stageName',
            'suggestionChips',
            'evidenceCount',
            'averageConfidence'
        ));
    }

    /**
     * Kirim Pesan Baru
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $student = Auth::user();
        $messageText = $request->message;

        // Panggil orchestrator service untuk memproses input dan menghasilkan respons AI
        $aiResponse = $this->conversationService->handleStudentMessage($student, $messageText);

        // Muat data percakapan terbaru untuk feedback status UI
        $conversation = Conversation::where('student_id', $student->id)
            ->where('status', 'active')
            ->first();

        // Cek jika percakapan baru saja selesai (tahap 12 selesai), buat laporan secara langsung
        $stage = $conversation ? $conversation->current_stage : 12;
        $isCompleted = !$conversation;

        if ($isCompleted) {
            // Sesi obrolan selesai, buat laporan
            $this->reportService->generateReport($student);
        }

        $evidenceCount = Evidence::where('student_id', $student->id)->count();
        $averageConfidence = (int) ConfidenceScore::where('student_id', $student->id)->avg('score');
        $stageName = ReflectionEngine::STAGES[$stage] ?? 'Penutup';

        return response()->json([
            'status' => 'success',
            'ai_message' => $aiResponse,
            'current_stage' => $stage,
            'stage_name' => $stageName,
            'is_completed' => $isCompleted,
            'evidence_count' => $evidenceCount,
            'average_confidence' => $averageConfidence,
            'suggestion_chips' => $this->getSuggestionChips($stage)
        ]);
    }

    /**
     * Reset Percakapan untuk mengulang dari Tahap 1
     */
    public function reset()
    {
        $student = Auth::user();

        // Nonaktifkan percakapan aktif yang lama
        Conversation::where('student_id', $student->id)
            ->update(['status' => 'paused']);

        return redirect()->route('student.chat');
    }

    /**
     * Helper Chip Jawaban berdasarkan Tahap Obrolan
     */
    private function getSuggestionChips(int $stage): array
    {
        $chips = [
            1 => ['Halo!', 'Hai, apa kabar?', 'Halo, aku siap ngobrol'],
            2 => ['Panggil aku Rian saja', 'Bisa panggil aku Siti', 'Nama panggilanku Andi'],
            3 => ['Aku di jurusan RPL', 'Aku suka belajar coding praktis', 'Banyak tugas kelompok belakangan ini'],
            4 => ['Aku suka main sepak bola ⚽', 'Aku gemar bermain musik 🎸', 'Hobiku main game online 🎮'],
            5 => ['Aku senang belajar coding web', 'Aku suka menggambar desain vektor', 'Aku suka fotografi & editing video'],
            6 => ['Teman sekelas seru-seru', 'Aku punya sahabat dekat di kelas', 'Lebih suka belajar bareng temen'],
            7 => ['Keluargaku sangat suportif', 'Orang tuaku bekerja di wirausaha', 'Hubungan keluarga sangat hangat'],
            8 => ['Aku orangnya agak pendiam', 'Aku mandiri dan suka tantangan', 'Aku senang memimpin diskusi kelompok'],
            9 => ['Terkadang suka minder saat presentasi', 'Lumayan capek karena banyak tugas', 'Sempat ada salah paham dengan teman'],
            10 => ['Pengin banggain orang tua', 'Pengin punya keahlian buat masa depan', 'Pengin lulus dengan nilai terbaik'],
            11 => ['Aku pengin langsung kerja setelah lulus', 'Rencana mau merintis usaha sendiri', 'Kepingin kuliah di teknik informatika'],
            12 => ['Terima kasih sarannya!', 'Sangat bermanfaat mengobrol dengarmu', 'Sampai jumpa kembali! 👋']
        ];

        return $chips[$stage] ?? [];
    }
}
