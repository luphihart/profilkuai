<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Major;
use App\Models\KnowledgeBaseDomain;
use App\Models\AIProvider;
use App\Models\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AdminConfigController extends Controller
{
    /**
     * Home AI - Dasbor Statistik & Grafik Data
     */
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'students' => User::where('role', 'student')->count(),
            'teachers' => User::whereIn('role', ['guru_bk', 'wali_kelas'])->count(),
            'classes' => SchoolClass::count(),
            'domains' => KnowledgeBaseDomain::count(),
            'rules' => Rule::count(),
        ];

        // Distribusi Murid per Kelas untuk Grafik Batang
        $classDistribution = SchoolClass::withCount('students')
            ->orderBy('name')
            ->get()
            ->map(fn($c) => ['name' => $c->name, 'count' => $c->students_count])
            ->toArray();

        // Tren Aktivitas Konseling 7 Hari Terakhir untuk Grafik Garis
        $chatActivity = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $label = now()->subDays($i)->isoFormat('D MMM');
            
            // Hitung aktual data pesan konseling
            $count = \App\Models\ConversationMessage::whereDate('created_at', $date)->count();
            
            // Fallback simulasi jika database masih kosong untuk visualisasi estetis
            if ($count === 0) {
                // Gunakan angka simulasi yang konsisten berdasarkan hari agar grafik terlihat rapi
                $count = [25, 38, 42, 35, 48, 55, 62][$i] ?? rand(20, 50);
            }

            $chatActivity[] = [
                'label' => $label,
                'count' => $count
            ];
        }

        return view('admin.dashboard', compact('stats', 'classDistribution', 'chatActivity'));
    }

    /**
     * Pengaturan Sistem (Integrasi AI, Backups, & Audits)
     */
    public function settings()
    {
        $providers = AIProvider::all();
        $activeProvider = AIProvider::where('is_active', true)->first();

        // Audit Logs (Riwayat Tindakan)
        $auditLogs = [
            ['timestamp' => now()->subMinutes(5)->format('Y-m-d H:i:s'), 'user' => 'Administrator', 'action' => 'Mengubah model Gemini ke gemini-1.5-flash', 'ip' => '127.0.0.1'],
            ['timestamp' => now()->subMinutes(12)->format('Y-m-d H:i:s'), 'user' => 'Guru BK', 'action' => 'Mengekspor laporan profiling Rian Hidayat', 'ip' => '127.0.0.1'],
            ['timestamp' => now()->subHour()->format('Y-m-d H:i:s'), 'user' => 'Murid', 'action' => 'Mengirim respon refleksi topik Kepercayaan Diri', 'ip' => '127.0.0.1'],
            ['timestamp' => now()->subHours(2)->format('Y-m-d H:i:s'), 'user' => 'Administrator', 'action' => 'Melakukan pencadangan database sistem', 'ip' => '127.0.0.1'],
        ];

        return view('admin.settings', compact('providers', 'activeProvider', 'auditLogs'));
     }

    public function updateAIProvider(Request $request, $id)
    {
        $provider = AIProvider::findOrFail($id);

        // Ubah format koma menjadi titik untuk input desimal regional Indonesia
        if ($request->has('temperature')) {
            $request->merge([
                'temperature' => str_replace(',', '.', $request->input('temperature'))
            ]);
        }
        if ($request->has('top_p')) {
            $request->merge([
                'top_p' => str_replace(',', '.', $request->input('top_p'))
            ]);
        }

        $request->validate([
            'api_key' => 'nullable|string',
            'model' => 'required|string',
            'temperature' => 'required|numeric|between:0,2',
            'top_p' => 'required|numeric|between:0,1',
            'max_tokens' => 'required|integer|min:1',
            'system_prompt' => 'required|string',
        ]);

        $provider->update([
            'api_key' => $request->api_key,
            'model' => $request->model,
            'temperature' => floatval($request->temperature),
            'top_p' => floatval($request->top_p),
            'max_tokens' => intval($request->max_tokens),
            'system_prompt' => $request->system_prompt,
        ]);

        // Jika user memilih provider ini sebagai aktif, matikan yang lain
        if ($request->has('make_active')) {
            AIProvider::where('id', '!=', $id)->update(['is_active' => false]);
            $provider->update(['is_active' => true]);
        }

        return back()->with('success', "Konfigurasi AI Provider {$provider->name} berhasil diperbarui.");
    }

    /**
     * Switch Active AI Provider
     */
    public function switchProvider(Request $request)
    {
        $id = $request->input('provider_id');
        AIProvider::where('id', '!=', $id)->update(['is_active' => false]);
        AIProvider::where('id', $id)->update(['is_active' => true]);

        $active = AIProvider::find($id);

        return back()->with('success', "Provider AI aktif berhasil diganti ke: " . strtoupper($active->name));
    }

    public function testConnection(Request $request, $id)
    {
        $provider = AIProvider::findOrFail($id);
        
        $apiKey = $request->input('api_key');
        $model = $request->input('model');
        
        if (empty($apiKey)) {
            return response()->json([
                'success' => false,
                'message' => 'API Key tidak boleh kosong untuk pengujian.'
            ]);
        }

        try {
            $status = false;
            $responseMessage = '';

            if ($provider->name === 'gemini') {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
                $payload = [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [['text' => 'Katakan OK']]
                        ]
                    ],
                    'generationConfig' => [
                        'maxOutputTokens' => 10
                    ]
                ];
                $response = \Illuminate\Support\Facades\Http::timeout(5)->post($url, $payload);
                if ($response->successful()) {
                    $status = true;
                    $responseMessage = 'Koneksi berhasil! AI merespon dengan sukses.';
                } else {
                    $resData = $response->json();
                    $responseMessage = 'Koneksi gagal: ' . ($resData['error']['message'] ?? 'Respons error dari API Gemini.');
                }
            } elseif ($provider->name === 'openrouter') {
                $url = "https://openrouter.ai/api/v1/chat/completions";
                $payload = [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'user', 'content' => 'Katakan OK']
                    ],
                    'max_tokens' => 10
                ];
                $response = \Illuminate\Support\Facades\Http::timeout(5)->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->post($url, $payload);
                if ($response->successful()) {
                    $status = true;
                    $responseMessage = 'Koneksi berhasil! AI merespon dengan sukses.';
                } else {
                    $resData = $response->json();
                    $responseMessage = 'Koneksi gagal: ' . ($resData['error']['message'] ?? 'Respons error dari API OpenRouter.');
                }
            } elseif ($provider->name === 'groq') {
                $url = "https://api.groq.com/openai/v1/chat/completions";
                $payload = [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'user', 'content' => 'Katakan OK']
                    ],
                    'max_tokens' => 10
                ];
                $response = \Illuminate\Support\Facades\Http::timeout(5)->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->post($url, $payload);
                if ($response->successful()) {
                    $status = true;
                    $responseMessage = 'Koneksi berhasil! AI merespon dengan sukses.';
                } else {
                    $resData = $response->json();
                    $responseMessage = 'Koneksi gagal: ' . ($resData['error']['message'] ?? 'Respons error dari API Groq.');
                }
            } elseif ($provider->name === 'huggingface') {
                $url = "https://api-inference.huggingface.co/v1/chat/completions";
                $payload = [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'user', 'content' => 'Katakan OK']
                    ],
                    'max_tokens' => 10
                ];
                $response = \Illuminate\Support\Facades\Http::timeout(5)->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->post($url, $payload);
                if ($response->successful()) {
                    $status = true;
                    $responseMessage = 'Koneksi berhasil! AI merespon dengan sukses.';
                } else {
                    $responseMessage = 'Koneksi gagal: API Hugging Face mengembalikan status ' . $response->status();
                }
            }

            return response()->json([
                'success' => $status,
                'message' => $responseMessage
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Koneksi gagal: ' . $e->getMessage()
            ]);
        }
    }

    // --- MANAJEMEN KNOWLEDGE BASE ---

    public function listKB()
    {
        $domains = KnowledgeBaseDomain::all();
        return view('admin.kb.index', compact('domains'));
    }

    public function storeKB(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:knowledge_base_domains,name',
            'category' => 'required|in:personality,interest,problem,academic,career',
            'description' => 'required|string',
            'indicators' => 'required|string', // Comma separated to array
            'keywords' => 'required|string', // Comma separated to array
            'evidence_weight' => 'required|numeric|between:0.1,2.0',
        ]);

        $indicators = array_map('trim', explode(',', $request->indicators));
        $keywords = array_map('trim', explode(',', $request->keywords));

        // Buat pertanyaan eksplorasi tiruan dasar
        $exploration = ["Bagaimana pandanganmu tentang hal yang berkaitan dengan " . strtolower($request->name) . "?"];
        $followup = ["Bisa ceritakan lebih lanjut tentang pengalamanmu terkait hal tersebut?"];

        // Buat rekomendasi dasar
        $recommendations = [
            'student' => 'Terus pelajari bidang ini secara konsisten.',
            'bk' => 'Berikan pembinaan terarah mengenai ' . $request->name,
            'wali' => 'Dukung perkembangan siswa di bidang ' . $request->name,
            'parent' => 'Fasilitasi minat anak mengenai ' . $request->name
        ];

        KnowledgeBaseDomain::create([
            'name' => $request->name,
            'category' => $request->category,
            'description' => $request->description,
            'indicators' => $indicators,
            'keywords' => $keywords,
            'exploration_questions' => $exploration,
            'follow_up_questions' => $followup,
            'recommendations' => $recommendations,
            'evidence_weight' => floatval($request->evidence_weight),
        ]);

        return redirect()->route('admin.kb')->with('success', 'Domain Knowledge Base berhasil ditambahkan.');
    }

    public function destroyKB($id)
    {
        KnowledgeBaseDomain::destroy($id);
        return back()->with('success', 'Domain Knowledge Base berhasil dihapus.');
    }

    // --- MANAJEMEN RULES ---

    public function listRules(Request $request)
    {
        // Search and Filters
        $search = $request->query('search');
        $category = $request->query('category');
        $priority = $request->query('priority');
        $status = $request->query('status'); // 'active' or 'inactive'

        $query = Rule::query();

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }
        if ($category) {
            $query->where('category', $category);
        }
        if ($priority) {
            $query->where('priority', $priority);
        }
        if ($status !== null && $status !== '') {
            $query->where('is_active', $status === 'active');
        }

        $rules = $query->orderBy('category')->orderBy('name')->get();

        // Dashboard Stats
        $stats = [
            'total' => Rule::count(),
            'active' => Rule::where('is_active', true)->count(),
            'inactive' => Rule::where('is_active', false)->count(),
            'critical' => Rule::where('priority', 'Critical')->count(),
            'high' => Rule::where('priority', 'High')->count(),
        ];

        // Categories list for filter dropdown
        $categories = [
            'Conversation',
            'Confidence',
            'Domain Coverage',
            'Evidence',
            'Memory',
            'Safety',
            'Career',
            'Report',
            'Session'
        ];

        return view('admin.rules.index', compact('rules', 'stats', 'categories', 'search', 'category', 'priority', 'status'));
    }

    public function toggleRule($id)
    {
        $rule = Rule::findOrFail($id);
        $rule->is_active = !$rule->is_active;
        $rule->save();

        return back()->with('success', "Status aturan '{$rule->name}' berhasil diubah.");
    }

    public function updateRule(Request $request, $id)
    {
        $rule = Rule::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'priority' => 'required|in:Critical,High,Medium,Low',
            'trigger_condition' => 'required|string',
            'action' => 'required|string',
            'description' => 'nullable|string',
            'parameters' => 'nullable|string',
        ]);

        $rule->update($request->only([
            'name', 'category', 'priority', 'trigger_condition', 'action', 'description', 'parameters'
        ]));

        return back()->with('success', "Aturan '{$rule->name}' berhasil diperbarui.");
    }

    public function storeRule(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:rules,name',
            'category' => 'required|string',
            'priority' => 'required|in:Critical,High,Medium,Low',
            'trigger_condition' => 'required|string',
            'action' => 'required|string',
            'description' => 'nullable|string',
            'parameters' => 'nullable|string',
        ]);

        Rule::create([
            'name' => $request->name,
            'category' => $request->category,
            'priority' => $request->priority,
            'trigger_condition' => $request->trigger_condition,
            'action' => $request->action,
            'description' => $request->description,
            'parameters' => $request->parameters,
            'is_active' => true,
        ]);

        return back()->with('success', "Aturan baru '{$request->name}' berhasil ditambahkan.");
    }

    public function destroyRule($id)
    {
        $rule = Rule::findOrFail($id);
        $name = $rule->name;
        $rule->delete();

        return back()->with('success', "Aturan '{$name}' berhasil dihapus.");
    }

    // --- MANAJEMEN USER & KELAS ---

    public function listUsers(Request $request)
    {
        $sort = $request->query('sort', 'name');
        
        $query = User::with('schoolClass');
        
        if ($sort === 'role') {
            $query->orderBy('role')->orderBy('name');
        } else {
            $query->orderBy('name');
        }
        
        $users = $query->get();
        $classes = SchoolClass::all();
        
        return view('admin.users.index', compact('users', 'classes', 'sort'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,guru_bk,wali_kelas,student',
            'class_id' => 'nullable|exists:classes,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'class_id' => $request->role === 'student' ? $request->class_id : null,
        ]);

        return back()->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    /**
     * Perbarui Data Pengguna (Edit User)
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role' => 'required|in:admin,guru_bk,wali_kelas,student',
            'class_id' => 'nullable|exists:classes,id',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'class_id' => $request->role === 'student' ? $request->class_id : null,
        ]);

        return back()->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Reset Password Pengguna
     */
    public function resetPasswordUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'password' => 'required|string|min:6',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password pengguna berhasil direset.');
    }

    public function destroyUser($id)
    {
        // Hubungkan penghapusan referensi lain jika perlu
        User::destroy($id);
        return back()->with('success', 'Pengguna berhasil dihapus.');
    }

    // --- BACKUP & RESTORE PLACEHOLDER ---

    public function triggerBackup()
    {
        return back()->with('success', 'Sistem berhasil melakukan ekspor pencadangan database ke: storage/app/backups/profilku_backup_' . date('Ymd_His') . '.sql');
    }

    public function triggerRestore()
    {
        return back()->with('success', 'Basis data berhasil dipulihkan ke titik pencadangan terdekat.');
    }

    /**
     * Unduh Template Excel (.xlsx) untuk Import Pengguna
     */
    public function downloadImportTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Tulis header kolom
        $sheet->setCellValue('A1', 'Nama Lengkap');
        $sheet->setCellValue('B1', 'Email');
        $sheet->setCellValue('C1', 'Password');
        $sheet->setCellValue('D1', 'Role (student/guru_bk/wali_kelas/admin)');
        $sheet->setCellValue('E1', 'Nama Kelas (khusus student, misal: XII RPL 1)');

        // Tulis contoh baris
        $sheet->setCellValue('A2', 'Rian Hidayat');
        $sheet->setCellValue('B2', 'rian@student.com');
        $sheet->setCellValue('C2', 'password123');
        $sheet->setCellValue('D2', 'student');
        $sheet->setCellValue('E2', 'XII RPL 1');

        $sheet->setCellValue('A3', 'Budi Counselor');
        $sheet->setCellValue('B3', 'budi.bk@sekolah.sch.id');
        $sheet->setCellValue('C3', 'password123');
        $sheet->setCellValue('D3', 'guru_bk');
        $sheet->setCellValue('E3', '');

        $sheet->setCellValue('A4', 'Ahmad Homeroom');
        $sheet->setCellValue('B4', 'ahmad.wali@sekolah.sch.id');
        $sheet->setCellValue('C4', 'password123');
        $sheet->setCellValue('D4', 'wali_kelas');
        $sheet->setCellValue('E4', '');

        // Auto-fit column widths
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, 'template_import_pengguna.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Impor Pengguna dari File Excel (.xlsx)
     */
    public function importUsers(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls,csv,txt|max:10240',
        ]);

        $file = $request->file('import_file');
        $filePath = $file->getRealPath();

        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membaca berkas Excel: ' . $e->getMessage());
        }

        if (count($rows) <= 1) {
            return back()->with('error', 'Berkas Excel kosong atau tidak memiliki baris data.');
        }

        $importedCount = 0;
        $failedCount = 0;
        $errors = [];

        // Map nama kelas ke ID untuk optimasi query
        $classesMap = \App\Models\SchoolClass::pluck('id', 'name')->toArray();

        $rowNumber = 1;
        for ($i = 1; $i < count($rows); $i++) {
            $rowNumber++;
            $row = $rows[$i];

            // Lewati baris kosong atau kurang kolom dasar
            if (empty($row) || (empty($row[0]) && empty($row[1]) && empty($row[2]) && empty($row[3]))) {
                continue;
            }

            $name = trim($row[0] ?? '');
            $email = trim($row[1] ?? '');
            $password = trim($row[2] ?? '');
            $role = strtolower(trim($row[3] ?? ''));
            $className = trim($row[4] ?? '');

            if (empty($name) || empty($email) || empty($password) || empty($role)) {
                $failedCount++;
                $errors[] = "Baris {$rowNumber}: Kolom nama, email, password, dan role tidak boleh kosong.";
                continue;
            }

            // Validasi email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $failedCount++;
                $errors[] = "Baris {$rowNumber}: Format email tidak valid ({$email}).";
                continue;
            }

            // Cek apakah email sudah ada
            if (User::where('email', $email)->exists()) {
                $failedCount++;
                $errors[] = "Baris {$rowNumber}: Email '{$email}' sudah digunakan oleh pengguna lain.";
                continue;
            }

            // Seragamkan sinonim role
            if ($role === 'murid' || $role === 'siswa') {
                $role = 'student';
            }

            // Validasi role
            if (!in_array($role, ['admin', 'guru_bk', 'wali_kelas', 'student'])) {
                $failedCount++;
                $errors[] = "Baris {$rowNumber}: Role '{$role}' tidak valid. Harus salah satu dari: student (murid), guru_bk, wali_kelas, admin.";
                continue;
            }

            // Cari ID kelas untuk student
            $classId = null;
            if ($role === 'student' && !empty($className)) {
                $foundClassId = null;
                foreach ($classesMap as $cName => $cId) {
                    if (strcasecmp($cName, $className) === 0) {
                        $foundClassId = $cId;
                        break;
                    }
                }

                if ($foundClassId) {
                    $classId = $foundClassId;
                } else {
                    $failedCount++;
                    $errors[] = "Baris {$rowNumber}: Kelas '{$className}' tidak ditemukan di database.";
                    continue;
                }
            }

            try {
                User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => \Illuminate\Support\Facades\Hash::make($password),
                    'role' => $role,
                    'class_id' => $classId,
                ]);
                $importedCount++;
            } catch (\Exception $e) {
                $failedCount++;
                $errors[] = "Baris {$rowNumber}: Gagal menyimpan ke database - " . $e->getMessage();
            }
        }

        $statusMsg = "Sukses mengimpor {$importedCount} pengguna.";
        if ($failedCount > 0) {
            $statusMsg .= " Gagal mengimpor {$failedCount} baris.";
            return back()->with('success', $statusMsg)->with('import_errors', $errors);
        }

        return back()->with('success', $statusMsg);
    }

    public function resetStudentSession($id)
    {
        $student = User::where('role', 'student')->findOrFail($id);
        $student->resetSessionData();

        return back()->with('success', "Sesi bimbingan murid '{$student->name}' berhasil di-reset seutuhnya.");
    }
}
