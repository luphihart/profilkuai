<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\StudentMemory;
use App\Models\ConfidenceScore;
use App\Models\Evidence;
use App\Models\Report;
use App\Models\KnowledgeBaseDomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    /**
     * Tampilan AI Home (Dashboard Siswa)
     */
    public function index()
    {
        $student = Auth::user();

        // 1. Ambil percakapan aktif
        $conversation = Conversation::where('student_id', $student->id)
            ->where('status', 'active')
            ->first();

        $currentStage = $conversation ? $conversation->current_stage : 1;
        $progressPercentage = min(100, (int) round(($currentStage / 12) * 100));

        // 2. Ambil Memori Siswa
        $rawMemories = StudentMemory::where('student_id', $student->id)->get();
        $memories = $rawMemories->pluck('value', 'key')->toArray();

        // 3. Karir & Hobi dari memori
        $nickname = $memories['nickname'] ?? $student->name;
        $careerGoal = $memories['career_goals'] ?? 'Belum teridentifikasi';
        $mainHobby = $memories['hobbies'] ?? 'Belum diceritakan';

        // 4. Ambil Skor Keyakinan (Personality Snapshot)
        $scores = ConfidenceScore::with('domain')
            ->where('student_id', $student->id)
            ->get();

        // 5. Rekomendasi Dinamis
        // Ambil rekomendasi dari domain yang memiliki tingkat keyakinan tertinggi (>50%)
        $recommendations = [];
        $highScore = ConfidenceScore::where('student_id', $student->id)
            ->where('score', '>', 50)
            ->first();
        if ($highScore) {
            $domain = $highScore->domain;
            if ($domain && isset($domain->recommendations['student'])) {
                $recommendations[] = [
                    'title' => "Rekomendasi " . $domain->name,
                    'text' => $domain->recommendations['student'],
                    'category' => $domain->category
                ];
            }
        }
        
        if (empty($recommendations)) {
            $recommendations[] = [
                'title' => 'Langkah Awal',
                'text' => 'Yuk lanjutin ngobrol dengan AI-mu untuk membuka rekomendasi pengembangan diri yang dipersonalisasi!',
                'category' => 'general'
            ];
        }

        // 6. Evaluasi Unlocked Achievements
        $messageCount = $conversation ? $conversation->messages()->count() : 0;
        $hasReport = Report::where('student_id', $student->id)->exists();

        $achievements = [
            [
                'title' => 'Langkah Pertama',
                'desc' => 'Mendaftar akun di Profilku AI',
                'unlocked' => true,
                'icon' => '🚀'
            ],
            [
                'title' => 'Curhat Pertama',
                'desc' => 'Mulai mengobrol dengan mentor AI',
                'unlocked' => $messageCount > 0,
                'icon' => '💬'
            ],
            [
                'title' => 'Hobi Terungkap',
                'desc' => 'Bercerita hobi atau minat senggang ke AI',
                'unlocked' => isset($memories['hobbies']),
                'icon' => '🎨'
            ],
            [
                'title' => 'Visi Masa Depan',
                'desc' => 'Berbagi rencana atau cita-cita karir',
                'unlocked' => isset($memories['career_goals']),
                'icon' => '🔮'
            ],
            [
                'title' => 'Profil Selesai',
                'desc' => 'Laporan bimbingan berhasil disusun oleh AI',
                'unlocked' => $hasReport,
                'icon' => '🏆'
            ]
        ];

        // 7. Today's Mission & Quote of the Day
        $missions = [
            1 => 'Katakan "Halo" ke asisten AI untuk memulai perkenalan.',
            2 => 'Beri tahu nama panggilan favoritmu kepada AI.',
            3 => 'Ceritakan pelajaran SMK yang paling menantang bagimu.',
            4 => 'Ceritakan hobimu saat senggang atau akhir pekan.',
            5 => 'Bahas hal yang membuatmu penasaran di jurusaimu.',
            6 => 'Ceritakan bagaimana kamu biasanya bergaul dengan teman-teman.',
            7 => 'Bagikan kisah hangat tentang suasana rumah atau keluargamu.',
            8 => 'Gambarkan kepribadianmu dalam tiga kata ke AI.',
            9 => 'Utarakan keluh kesah atau masalah yang sedang kamu rasakan.',
            10 => 'Beri tahu AI apa yang menjadi penyemangat terbesarmu belajar.',
            11 => 'Diskusikan rencana karir impianmu setelah lulus nanti.',
            12 => 'Selesaikan sesi obrolan untuk menyusun laporan akhir.'
        ];
        $todayMission = $missions[$currentStage] ?? 'Lanjutkan percakapan dengan AI.';

        $quotes = [
            "Pendidikan adalah senjata paling mematikan di dunia, karena dengan itu kamu bisa mengubah dunia. - Nelson Mandela",
            "Masa depan adalah milik mereka yang percaya pada keindahan mimpi mereka. - Eleanor Roosevelt",
            "Jangan biarkan apa yang tidak bisa kamu lakukan mengganggu apa yang bisa kamu lakukan. - John Wooden",
            "Satu-satunya cara untuk melakukan pekerjaan besar adalah dengan mencintai apa yang kamu lakukan. - Steve Jobs",
            "Belajar bukanlah persiapan untuk hidup; belajar adalah hidup itu sendiri. - John Dewey"
        ];
        $quoteOfTheDay = $quotes[array_rand($quotes)];

        // 8. Rencana laporan terakhir
        $latestReport = Report::where('student_id', $student->id)->latest()->first();

        return view('student.dashboard', compact(
            'nickname',
            'progressPercentage',
            'currentStage',
            'careerGoal',
            'mainHobby',
            'scores',
            'rawMemories',
            'recommendations',
            'achievements',
            'todayMission',
            'quoteOfTheDay',
            'latestReport',
            'conversation'
        ));
    }
}
