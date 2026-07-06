<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Evidence;
use App\Models\ConfidenceScore;
use App\Models\StudentMemory;
use App\Models\Conversation;
use App\Models\TeacherNote;
use App\Models\Report;
use App\Models\KnowledgeBaseDomain;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CounselorDashboardController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Dashboard Guru BK
     */
    public function index(Request $request)
    {
        // 1. Ambil daftar siswa beserta kelasnya
        $classFilter = $request->input('class_id');
        $query = User::where('role', 'student')->with('schoolClass.major');

        if ($classFilter) {
            $query->where('class_id', $classFilter);
        }

        $students = $query->get();

        // 2. Siswa Prioritas (Stres akademis/Bullying terdeteksi dengan bobot >= 0.7, atau minder)
        // Kita cari siswa yang memiliki evidence di domain problem (ID 2 untuk Stres, ID 6 untuk Bullying) dengan weight >= 0.7
        $priorityEvidence = Evidence::where('weight', '>=', 0.7)
            ->whereIn('domain_id', function ($q) {
                $q->select('id')->from('knowledge_base_domains')->where('category', 'problem');
            })
            ->pluck('student_id')
            ->toArray();

        // Tambah siswa dengan motivasi sangat rendah (skor < 40)
        $lowMotivationStudents = ConfidenceScore::whereIn('domain_id', function ($q) {
                $q->select('id')->from('knowledge_base_domains')->where('name', 'Motivasi Belajar');
            })
            ->where('score', '<', 50)
            ->pluck('student_id')
            ->toArray();

        $priorityIds = array_unique(array_merge($priorityEvidence, $lowMotivationStudents));
        
        $priorityStudents = User::whereIn('id', $priorityIds)
            ->with(['schoolClass.major', 'confidenceScores.domain'])
            ->get();

        // 3. Hitung Distribusi Masalah untuk Heatmap (Masalah Terpopuler)
        $problemDomains = KnowledgeBaseDomain::where('category', 'problem')->get();
        $problemDistribution = [];
        foreach ($problemDomains as $d) {
            $count = Evidence::where('domain_id', $d->id)->distinct('student_id')->count();
            $problemDistribution[] = [
                'name' => $d->name,
                'count' => $count
            ];
        }

        // 4. Hitung Distribusi Minat Karir (berdasarkan data memori)
        $careerMemories = StudentMemory::where('key', 'career_goals')->get();
        $careerCounts = [
            'Kerja' => 0,
            'Kuliah' => 0,
            'Wirausaha' => 0,
            'Belum Tahu' => 0
        ];
        foreach ($careerMemories as $cm) {
            $val = strtolower($cm->value);
            if (strpos($val, 'kerja') !== false || strpos($val, 'karyawan') !== false) {
                $careerCounts['Kerja']++;
            } elseif (strpos($val, 'kuliah') !== false || strpos($val, 'universitas') !== false || strpos($val, 'lanjut') !== false) {
                $careerCounts['Kuliah']++;
            } elseif (strpos($val, 'usaha') !== false || strpos($val, 'bisnis') !== false || strpos($val, 'dagang') !== false || strpos($val, 'wirausaha') !== false) {
                $careerCounts['Wirausaha']++;
            } else {
                $careerCounts['Belum Tahu']++;
            }
        }

        // 5. Total Statistik
        $totalStudents = User::where('role', 'student')->count();
        $totalConversations = Conversation::count();
        $totalProblemsLogged = Evidence::whereIn('domain_id', function ($q) {
            $q->select('id')->from('knowledge_base_domains')->where('category', 'problem');
        })->count();

        $classes = SchoolClass::all();

        return view('bk.dashboard', compact(
            'students',
            'priorityStudents',
            'problemDistribution',
            'careerCounts',
            'totalStudents',
            'totalConversations',
            'totalProblemsLogged',
            'classes',
            'classFilter'
        ));
    }

    /**
     * Detail Siswa & Explainable AI
     */
    public function showStudentDetail($id)
    {
        $student = User::where('role', 'student')
            ->with(['schoolClass.major.schoolClasses'])
            ->findOrFail($id);

        // 1. Riwayat Percakapan
        $conversation = Conversation::where('student_id', $student->id)
            ->latest()
            ->first();
        $messages = $conversation ? $conversation->messages()->orderBy('id', 'asc')->get() : collect();

        // 2. Memori yang Tereksplorasi
        $memories = StudentMemory::where('student_id', $student->id)->get();

        // 3. Bukti Analisis (Evidence Engine) - Tracing ke kalimat percakapan
        $evidence = Evidence::with(['domain', 'sourceMessage'])
            ->where('student_id', $student->id)
            ->orderBy('weight', 'desc')
            ->get();

        // 4. Skor Keyakinan (Confidence Engine)
        $confidenceScores = ConfidenceScore::with('domain')
            ->where('student_id', $student->id)
            ->get();

        // 5. Catatan Konseling Internal
        $notes = TeacherNote::with('teacher')
            ->where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // 6. Laporan Profiling Akhir
        $latestReport = Report::where('student_id', $student->id)->latest()->first();

        return view('bk.student-detail', compact(
            'student',
            'conversation',
            'messages',
            'memories',
            'evidence',
            'confidenceScores',
            'notes',
            'latestReport'
        ));
    }

    /**
     * Tambah Catatan Guru BK untuk Siswa
     */
    public function addNote(Request $request, $studentId)
    {
        $request->validate([
            'note_text' => 'required|string|max:10000',
        ]);

        TeacherNote::create([
            'teacher_id' => Auth::id(),
            'student_id' => $studentId,
            'note_text' => $request->note_text
        ]);

        return back()->with('success', 'Catatan konseling berhasil ditambahkan.');
    }

    /**
     * Memicu pembuatan laporan profiling manual oleh BK
     */
    public function triggerReport($studentId)
    {
        $student = User::findOrFail($studentId);
        $this->reportService->generateReport($student);
        return back()->with('success', 'Laporan profiling baru berhasil disusun oleh AI.');
    }

    /**
     * Hasilkan Rekomendasi BK via AI secara real-time
     */
    public function generateAIRecommendation(Request $request, $id, \App\Services\AIService $aiService)
    {
        $student = User::where('role', 'student')->with('schoolClass.major')->findOrFail($id);

        $provider = \App\Models\AIProvider::where('is_active', true)->first();
        if (!$provider || empty($provider->api_key)) {
            return response()->json([
                'recommendation' => "⚠️ Gagal membuat rekomendasi: Kunci API AI (API Key) belum dikonfigurasi di Panel Admin sekolah. Hubungi Administrator."
            ]);
        }

        // Kumpulkan data profil siswa
        $memories = \App\Models\StudentMemory::where('student_id', $student->id)->pluck('value', 'key')->toArray();
        $memoriesText = json_encode($memories, JSON_PRETTY_PRINT);

        $evidence = \App\Models\Evidence::with('domain')
            ->where('student_id', $student->id)
            ->get()
            ->map(fn($ev) => "- Domain [{$ev->domain->name}] Indikator: {$ev->indicator} (Bukti: \"{$ev->excerpt}\")")
            ->implode("\n");

        $scores = \App\Models\ConfidenceScore::with('domain')
            ->where('student_id', $student->id)
            ->get()
            ->map(fn($sc) => "- {$sc->domain->name}: {$sc->score}%")
            ->implode("\n");

        // Susun prompt rekomendasi BK
        $prompt = "Tugas: Buat draf rekomendasi bimbingan konseling taktis untuk Guru BK sekolah SMK berdasarkan profil siswa berikut:
Nama: {$student->name}
Kelas/Jurusan: {$student->schoolClass->name} - {$student->schoolClass->major->name}

Memori Faktual:
{$memoriesText}

Daftar Skor Keyakinan Domain:
{$scores}

Bukti Hambatan Percakapan:
{$evidence}

Tugas:
Tulis draf rekomendasi dalam gaya bahasa profesional, solutif, dan berorientasi pada aksi konkret sekolah. Susun draf dengan format plaintext rapi (tidak usah markdown tabel/header tebal):
1. Ringkasan Fokus Siswa (singkat, 1-2 kalimat)
2. Rekomendasi Aksi Guru BK (tindakan di sekolah)
3. Arahan untuk Wali Kelas & Orang Tua

Draf ini harus siap dibaca dan diedit kembali oleh Guru BK. Batasi draf dalam 2-3 paragraf agar ringkas, padat, dan taktis. JANGAN sertakan markdown bold berlebih, tulis plaintext terformat rapi.";

        try {
            $recommendationText = $aiService->generateResponse([], $prompt);
            return response()->json([
                'recommendation' => $recommendationText
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'recommendation' => "Terjadi kesalahan saat memproses rekomendasi AI: " . $e->getMessage()
            ]);
        }
    }

    public function resetSession($id)
    {
        $student = User::where('role', 'student')->findOrFail($id);
        $student->resetSessionData();

        return back()->with('success', "Sesi bimbingan murid '{$student->name}' berhasil di-reset seutuhnya.");
    }
}
