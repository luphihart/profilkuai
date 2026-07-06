<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Major;
use App\Models\SchoolClass;
use App\Models\KnowledgeBaseDomain;
use App\Models\AIProvider;
use App\Models\Rule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Jurusan (Majors)
        $rpl = Major::create(['name' => 'Rekayasa Perangkat Lunak', 'code' => 'RPL']);
        $tkj = Major::create(['name' => 'Teknik Komputer & Jaringan', 'code' => 'TKJ']);
        $dkv = Major::create(['name' => 'Desain Komunikasi Visual', 'code' => 'DKV']);

        // 3. Seed Users untuk Guru/Admin terlebih dahulu (agar wali kelas bisa ditautkan ke kelas)
        $admin = User::create([
            'name' => 'Administrator Profilku',
            'email' => 'admin@profilku.ai',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $bk = User::create([
            'name' => 'Susi Susanti, S.Pd. (Guru BK)',
            'email' => 'gurubk@profilku.ai',
            'password' => Hash::make('password'),
            'role' => 'guru_bk',
        ]);

        $wali = User::create([
            'name' => 'Budi Hartono, S.T. (Wali Kelas)',
            'email' => 'walikelas@profilku.ai',
            'password' => Hash::make('password'),
            'role' => 'wali_kelas',
        ]);

        // 4. Seed Kelas (Classes)
        $classRpl = SchoolClass::create([
            'name' => 'XII RPL 1',
            'major_id' => $rpl->id,
            'homeroom_teacher_id' => $wali->id,
        ]);

        $classTkj = SchoolClass::create([
            'name' => 'XII TKJ 1',
            'major_id' => $tkj->id,
            'homeroom_teacher_id' => null,
        ]);

        $classDkv = SchoolClass::create([
            'name' => 'XII DKV 1',
            'major_id' => $dkv->id,
            'homeroom_teacher_id' => null,
        ]);

        // 5. Seed Siswa (Students)
        $student = User::create([
            'name' => 'Rian Hidayat (Siswa)',
            'email' => 'student@profilku.ai',
            'password' => Hash::make('password'),
            'role' => 'student',
            'class_id' => $classRpl->id,
        ]);

        // Tambah siswa tambahan untuk melengkapi dashboard BK
        User::create([
            'name' => 'Siti Aminah',
            'email' => 'siti@profilku.ai',
            'password' => Hash::make('password'),
            'role' => 'student',
            'class_id' => $classRpl->id,
        ]);

        User::create([
            'name' => 'Andi Wijaya',
            'email' => 'andi@profilku.ai',
            'password' => Hash::make('password'),
            'role' => 'student',
            'class_id' => $classDkv->id,
        ]);

        // 6. Seed AI Providers
        AIProvider::create([
            'name' => 'gemini',
            'api_key' => '', // Diisi melalui admin panel
            'model' => 'gemini-1.5-flash',
            'temperature' => 0.7,
            'top_p' => 0.95,
            'max_tokens' => 1200,
            'system_prompt' => "Anda adalah Pendamping & Mentor AI siswa SMK bernama Profilku AI. Gaya bahasa Anda harus hangat, ramah, seperti sahabat karib (menggunakan panggilan 'kamu' atau nama panggilan siswa), menyemangati, dan tidak pernah terdengar seperti sedang mengisi kuesioner formal ataupun psikiater klinis.
Tujuan percakapan ini adalah untuk mengenali minat, bakat, masalah, dan potensi siswa secara mengalir alami. JANGAN AJUKAN PERTANYAAN BERTUBI-TUBI. Ajukan SATU pertanyaan terbuka setiap kali merespons, dengarkan jawaban siswa, lakukan validasi emosi mereka terlebih dahulu, lalu gali lebih dalam jika ada informasi menarik.
Aturan penting: JANGAN PERNAH mendiagnosis penyakit mental (seperti depresi klinis, OCD, bipolar, dll). Jika mendeteksi masalah berat seperti perundungan fisik atau stres akademis yang berlebih, gunakan istilah 'kemungkinan indikasi', 'memerlukan obrolan lebih lanjut', atau sarankan secara halus untuk berkonsultasi dengan Guru BK sekolah (Ibu Susi).
Gunakan data memori siswa yang disediakan di dalam context untuk menghindari menanyakan hal yang sudah diketahui sebelumnya.",
            'is_active' => true,
        ]);

        AIProvider::create([
            'name' => 'openrouter',
            'api_key' => '', // Diisi melalui admin panel
            'model' => 'google/gemini-2.5-flash',
            'temperature' => 0.7,
            'top_p' => 0.9,
            'max_tokens' => 1200,
            'system_prompt' => "Anda adalah Pendamping & Mentor AI siswa SMK bernama Profilku AI. Gaya bahasa Anda harus hangat, ramah, seperti sahabat karib (menggunakan panggilan 'kamu' atau nama panggilan siswa), menyemangati, dan tidak pernah terdengar seperti sedang mengisi kuesioner formal ataupun psikiater klinis.
Tujuan percakapan ini adalah untuk mengenali minat, bakat, masalah, dan potensi siswa secara mengalir alami. JANGAN AJUKAN PERTANYAAN BERTUBI-TUBI. Ajukan SATU pertanyaan terbuka setiap kali merespons, dengarkan jawaban siswa, lakukan validasi emosi mereka terlebih dahulu, lalu gali lebih dalam jika ada informasi menarik.
Aturan penting: JANGAN PERNAH mendiagnosis penyakit mental (seperti depresi klinis, OCD, bipolar, dll). Jika mendeteksi masalah berat seperti perundungan fisik atau stres akademis yang berlebih, gunakan istilah 'kemungkinan indikasi', 'memerlukan obrolan lebih lanjut', atau sarankan secara halus untuk berkonsultasi dengan Guru BK sekolah (Ibu Susi).
Gunakan data memori siswa yang disediakan di dalam context untuk menghindari menanyakan hal yang sudah diketahui sebelumnya.",
            'is_active' => false,
        ]);

        AIProvider::create([
            'name' => 'groq',
            'api_key' => '', // Diisi melalui admin panel
            'model' => 'llama-3.3-70b-versatile',
            'temperature' => 0.7,
            'top_p' => 0.9,
            'max_tokens' => 1200,
            'system_prompt' => "Anda adalah Pendamping & Mentor AI siswa SMK bernama Profilku AI. Gaya bahasa Anda harus hangat, ramah, seperti sahabat karib (menggunakan panggilan 'kamu' atau nama panggilan siswa), menyemangati, dan tidak pernah terdengar seperti sedang mengisi kuesioner formal ataupun psikiater klinis.
Tujuan percakapan ini adalah untuk mengenali minat, bakat, masalah, dan potensi siswa secara mengalir alami. JANGAN AJUKAN PERTANYAAN BERTUBI-TUBI. Ajukan SATU pertanyaan terbuka setiap kali merespons, dengarkan jawaban siswa, lakukan validasi emosi mereka terlebih dahulu, lalu gali lebih dalam jika ada informasi menarik.
Aturan penting: JANGAN PERNAH mendiagnosis penyakit mental (seperti depresi klinis, OCD, bipolar, dll). Jika mendeteksi masalah berat seperti perundungan fisik atau stres akademis yang berlebih, gunakan istilah 'kemungkinan indikasi', 'memerlukan obrolan lebih lanjut', atau sarankan secara halus untuk berkonsultasi dengan Guru BK sekolah (Ibu Susi).
Gunakan data memori siswa yang disediakan di dalam context untuk menghindari menanyakan hal yang sudah diketahui sebelumnya.",
            'is_active' => false,
        ]);

        AIProvider::create([
            'name' => 'huggingface',
            'api_key' => '', // Diisi melalui admin panel
            'model' => 'meta-llama/Llama-3.2-3B-Instruct',
            'temperature' => 0.7,
            'top_p' => 0.9,
            'max_tokens' => 1200,
            'system_prompt' => "Anda adalah Pendamping & Mentor AI siswa SMK bernama Profilku AI. Gaya bahasa Anda harus hangat, ramah, seperti sahabat karib (menggunakan panggilan 'kamu' atau nama panggilan siswa), menyemangati, dan tidak pernah terdengar seperti sedang mengisi kuesioner formal ataupun psikiater klinis.
Tujuan percakapan ini adalah untuk mengenali minat, bakat, masalah, dan potensi siswa secara mengalir alami. JANGAN AJUKAN PERTANYAAN BERTUBI-TUBI. Ajukan SATU pertanyaan terbuka setiap kali merespons, dengarkan jawaban siswa, lakukan validasi emosi mereka terlebih dahulu, lalu gali lebih dalam jika ada informasi menarik.
Aturan penting: JANGAN PERNAH mendiagnosis penyakit mental (seperti depresi klinis, OCD, bipolar, dll). Jika mendeteksi masalah berat seperti perundungan fisik atau stres akademis yang berlebih, gunakan istilah 'kemungkinan indikasi', 'memerlukan obrolan lebih lanjut', atau sarankan secara halus untuk berkonsultasi dengan Guru BK sekolah (Ibu Susi).
Gunakan data memori siswa yang disediakan di dalam context untuk menghindari menanyakan hal yang sudah diketahui sebelumnya.",
            'is_active' => false,
        ]);

        // 7. Seed Rule Engine (Aturan Alur)
        $defaultRules = [
            // Conversation
            [
                'name' => 'Response Too Short',
                'category' => 'Conversation',
                'priority' => 'Medium',
                'trigger_condition' => 'length(student_response) < 15',
                'action' => 'elaboration_prompt',
                'description' => 'Memicu AI untuk meminta penjelasan tambahan jika jawaban murid terlalu pendek (misalnya hanya "ya", "tidak tahu", "biasa saja").',
                'is_active' => true,
            ],
            [
                'name' => 'Ambiguous Response',
                'category' => 'Conversation',
                'priority' => 'Medium',
                'trigger_condition' => 'ambiguous_keywords_detected',
                'action' => 'clarify_response',
                'description' => 'Klarifikasi jika jawaban murid mengandung kontradiksi atau keraguan.',
                'is_active' => true,
            ],
            [
                'name' => 'Generic Response',
                'category' => 'Conversation',
                'priority' => 'Low',
                'trigger_condition' => 'generic_fillers_detected',
                'action' => 'suggest_alternatives',
                'description' => 'Mendorong respon yang lebih spesifik jika murid menggunakan kata-kata pengisi umum.',
                'is_active' => true,
            ],
            [
                'name' => 'Contradictory Response',
                'category' => 'Conversation',
                'priority' => 'High',
                'trigger_condition' => 'contradiction_detected',
                'action' => 'highlight_contradiction',
                'description' => 'Mendeteksi dan mengonfrontasi secara halus jika respon murid bertentangan dengan fakta sebelumnya.',
                'is_active' => true,
            ],

            // Confidence
            [
                'name' => 'Confidence Below 70%',
                'category' => 'Confidence',
                'priority' => 'High',
                'trigger_condition' => 'confidence_score < 70',
                'action' => 'deepen_exploration',
                'description' => 'Jika skor pemahaman domain masih di bawah 70%, sistem menginstruksikan AI untuk terus mengeksplorasi domain terkait dengan pertanyaan alternatif.',
                'is_active' => true,
            ],
            [
                'name' => 'Confidence Above 90%',
                'category' => 'Confidence',
                'priority' => 'Medium',
                'trigger_condition' => 'confidence_score > 90',
                'action' => 'lock_domain',
                'description' => 'Mengunci domain jika tingkat keyakinan telah sangat tinggi dan berpindah ke eksplorasi berikutnya.',
                'is_active' => true,
            ],

            // Domain Coverage
            [
                'name' => 'Unexplored Domain',
                'category' => 'Domain Coverage',
                'priority' => 'High',
                'trigger_condition' => 'unexplored_domains_exist',
                'action' => 'switch_domain',
                'description' => 'Pindah ke domain kompetensi atau pemantauan lain yang belum sempat dibahas.',
                'is_active' => true,
            ],
            [
                'name' => 'Minimum Questions Per Domain',
                'category' => 'Domain Coverage',
                'priority' => 'Medium',
                'trigger_condition' => 'questions_asked_in_domain < 3',
                'action' => 'continue_domain_exploration',
                'description' => 'Memastikan minimal 3 pertanyaan diajukan sebelum menganggap satu domain telah dieksplorasi.',
                'is_active' => true,
            ],

            // Evidence
            [
                'name' => 'Insufficient Evidence',
                'category' => 'Evidence',
                'priority' => 'High',
                'trigger_condition' => 'evidence_count < 2',
                'action' => 'gather_more_evidence',
                'description' => 'Meminta bukti kutipan tambahan jika informasi yang mendukung kesimpulan belum kuat.',
                'is_active' => true,
            ],
            [
                'name' => 'Conflicting Evidence',
                'category' => 'Evidence',
                'priority' => 'Critical',
                'trigger_condition' => 'conflicting_evidence_detected',
                'action' => 'resolve_conflict',
                'description' => 'Mendeteksi pertentangan bukti antar domain untuk klarifikasi bimbingan.',
                'is_active' => true,
            ],

            // Memory
            [
                'name' => 'Prevent Duplicate Questions',
                'category' => 'Memory',
                'priority' => 'High',
                'trigger_condition' => 'question_exists_in_memory',
                'action' => 'rephrase_question',
                'description' => 'Mencegah AI menanyakan pertanyaan yang mirip dengan yang sudah pernah ditanyakan.',
                'is_active' => true,
            ],
            [
                'name' => 'Remember Student Facts',
                'category' => 'Memory',
                'priority' => 'Medium',
                'trigger_condition' => 'new_fact_extracted',
                'action' => 'save_to_memory',
                'description' => 'Secara aktif merekam fakta pribadi murid seperti nama panggilan, cita-cita, atau hobi.',
                'is_active' => true,
            ],

            // Safety
            [
                'name' => 'Sensitive Content Detection',
                'category' => 'Safety',
                'priority' => 'Critical',
                'trigger_condition' => 'sensitive_content_detected',
                'action' => 'alert_counselor',
                'description' => 'Memicu notifikasi darurat ke Guru BK jika murid membicarakan tindakan self-harm, perundungan fisik, atau kekerasan.',
                'is_active' => true,
            ],
            [
                'name' => 'Prevent Psychological Diagnosis',
                'category' => 'Safety',
                'priority' => 'Critical',
                'trigger_condition' => 'psychological_diagnosis_attempt',
                'action' => 'refuse_clinical_diagnosis',
                'description' => 'Melarang AI mendiagnosis kelainan mental klinis secara formal (depresi klinis, OCD, bipolar, dll).',
                'is_active' => true,
            ],

            // Career
            [
                'name' => 'Unknown Career Goal',
                'category' => 'Career',
                'priority' => 'Medium',
                'trigger_condition' => 'career_goal_empty',
                'action' => 'explore_career_options',
                'description' => 'Mengarahkan percakapan untuk menggali potensi karir jika murid belum menentukan cita-cita.',
                'is_active' => true,
            ],
            [
                'name' => 'Validate Career Choice',
                'category' => 'Career',
                'priority' => 'Medium',
                'trigger_condition' => 'career_goal_defined',
                'action' => 'validate_alignment',
                'description' => 'Memvalidasi keselarasan antara cita-cita karir dengan hobi dan minat bakat.',
                'is_active' => true,
            ],

            // Report
            [
                'name' => 'Hide Low Confidence Conclusions',
                'category' => 'Report',
                'priority' => 'High',
                'trigger_condition' => 'report_generation && confidence_score < 50',
                'action' => 'exclude_from_report',
                'description' => 'Menyembunyikan kesimpulan domain dengan tingkat keyakinan rendah dari laporan resmi.',
                'is_active' => true,
            ],
            [
                'name' => 'Require Minimum Evidence',
                'category' => 'Report',
                'priority' => 'High',
                'trigger_condition' => 'report_generation && evidence_count < 1',
                'action' => 'flag_as_unverified',
                'description' => 'Menandai domain sebagai \'belum terverifikasi\' jika tidak memiliki minimal satu bukti kutipan percakapan.',
                'is_active' => true,
            ],

            // Session
            [
                'name' => 'Auto Save Conversation',
                'category' => 'Session',
                'priority' => 'High',
                'trigger_condition' => 'message_received',
                'action' => 'save_state',
                'description' => 'Menyimpan histori pesan secara berkala untuk mencegah data hilang.',
                'is_active' => true,
            ],
            [
                'name' => 'Resume Previous Session',
                'category' => 'Session',
                'priority' => 'Medium',
                'trigger_condition' => 'session_reopened',
                'action' => 'load_previous_state',
                'description' => 'Memulihkan sesi percakapan bimbingan terakhir saat murid kembali masuk.',
                'is_active' => true,
            ],
            [
                'name' => 'End Session When Complete',
                'category' => 'Session',
                'priority' => 'Critical',
                'trigger_condition' => 'current_stage == 12',
                'action' => 'compile_final_report',
                'description' => 'Menghentikan sesi konseling AI dan menyusun laporan profiling akhir saat tahap 12 tercapai.',
                'is_active' => true,
            ],
        ];

        foreach ($defaultRules as $r) {
            Rule::create($r);
        }

        // 8. Seed Domain Knowledge Base
        $domains = [
            [
                'name' => 'Kepercayaan Diri',
                'category' => 'personality',
                'description' => 'Kemampuan seseorang untuk meyakini kapasitas, nilai, dan keputusan diri sendiri dalam menghadapi tugas maupun pergaulan sosial.',
                'indicators' => ['Yakin tampil di depan umum', 'Tidak takut salah mencoba hal baru', 'Menerima kritik dengan positif', 'Mampu berpendapat'],
                'keywords' => ['percaya diri', 'minder', 'takut salah', 'tampil', 'presentasi', 'gugup', 'pede', 'malu'],
                'synonyms' => ['self confidence', 'rendah diri', 'demam panggung', 'ragu-ragu'],
                'example_behaviors' => ['Siswa bersedia menjadi ketua kelompok', 'Siswa berani mempresentasikan karya tanpa gemetar berlebihan', 'Siswa tidak langsung menyerah ketika idenya dikritik'],
                'exploration_questions' => [
                    'Gimana perasaan kamu kalau diminta memimpin presentasi di depan kelas?',
                    'Pernah nggak kamu ngerasa minder saat berkumpul atau kerja kelompok dengan teman-teman?'
                ],
                'follow_up_questions' => [
                    'Apa sih yang biasanya bikin kamu paling deg-degan saat harus berbicara di depan orang banyak?',
                    'Bagaimana cara kamu menenangkan diri saat merasa kurang percaya diri?'
                ],
                'recommendations' => [
                    'student' => 'Cobalah untuk aktif berpendapat di kelas, mulai dari mengajukan pertanyaan kecil kepada guru.',
                    'bk' => 'Berikan siswa pelatihan public speaking sederhana atau libatkan dalam kegiatan organisasi untuk meningkatkan asertivitas.',
                    'wali' => 'Tunjuk siswa sebagai perwakilan kelas dalam forum diskusi kecil agar terbiasa tampil.',
                    'parent' => 'Berikan apresiasi pada usaha mandiri anak di rumah, hindari membandingkan performanya dengan anak lain.'
                ],
                'evidence_weight' => 1.0,
            ],
            [
                'name' => 'Stres Akademis',
                'category' => 'problem',
                'description' => 'Tekanan mental atau emosional akibat beban tugas, ujian, atau ekspektasi prestasi di lingkungan sekolah SMK.',
                'indicators' => ['Kelelahan fisik akibat tugas', 'Kesulitan tidur karena memikirkan nilai', 'Merasa cemas berlebih sebelum ujian', 'Kehilangan fokus belajar'],
                'keywords' => ['stres', 'pusing', 'tugas banyak', 'capek', 'begadang', 'lelah', 'cemas', 'nilai turun', 'beban'],
                'synonyms' => ['burnout', 'tekanan belajar', 'depresi tugas', 'frustrasi'],
                'example_behaviors' => ['Siswa sering terlambat mengumpulkan tugas praktikum', 'Siswa tampak lesu saat jam pelajaran pertama', 'Siswa mengeluhkan pusing saat menjelang ujian tengah semester'],
                'exploration_questions' => [
                    'Belakangan ini, tugas-tugas sekolah kerasa sangat menumpuk nggak buat kamu?',
                    'Ada waktu nggak buat kamu istirahat atau bersenang-senang di luar jam sekolah?'
                ],
                'follow_up_questions' => [
                    'Kira-kira mata pelajaran apa yang paling sering bikin kamu kepikiran sampai susah tidur?',
                    'Kalau lagi ngerasa jenuh banget sama tugas, apa yang biasanya kamu lakukan?'
                ],
                'recommendations' => [
                    'student' => 'Atur jadwal belajar dengan metode pomodoro dan luangkan waktu 15 menit untuk relaksasi atau hobi harian.',
                    'bk' => 'Bantu siswa menyusun skala prioritas tugas dan berikan konseling manajemen waktu serta teknik mindfulness.',
                    'wali' => 'Koordinasikan dengan guru mata pelajaran agar pembagian tenggat waktu tugas praktikum tidak menumpuk di hari yang sama.',
                    'parent' => 'Ciptakan suasana rumah yang tenang dan hindari menuntut nilai akademik yang tidak realistis.'
                ],
                'evidence_weight' => 1.0,
            ],
            [
                'name' => 'Minat Rekayasa Perangkat Lunak',
                'category' => 'interest',
                'description' => 'Ketertarikan dan antusiasme siswa SMK terhadap aktivitas pemrograman, pembuatan aplikasi, logika algoritma, dan teknologi perangkat lunak.',
                'indicators' => ['Senang coding berjam-jam', 'Suka mencari tahu cara kerja suatu aplikasi', 'Tertarik membuat website atau game sendiri', 'Senang belajar logika matematika/algoritma'],
                'keywords' => ['coding', 'pemrograman', 'website', 'aplikasi', 'laravel', 'javascript', 'html', 'python', 'github', 'bikin game'],
                'synonyms' => ['software engineering', 'bikin program', 'ngoding', 'developer'],
                'example_behaviors' => ['Siswa mengutak-atik kode di luar jam sekolah', 'Siswa bersemangat menceritakan proyek aplikasi buatan sendiri', 'Siswa aktif berdiskusi di forum programming'],
                'exploration_questions' => [
                    'Hal apa yang paling bikin kamu penasaran waktu pertama kali belajar coding di kelas?',
                    'Pernah nggak kamu nyoba bikin website atau aplikasi kecil di luar tugas sekolah?'
                ],
                'follow_up_questions' => [
                    'Bahasa pemrograman atau framework apa yang paling kamu sukai saat ini dan kenapa?',
                    'Apa proyek impian yang kepengin banget kamu wujudkan di masa depan?'
                ],
                'recommendations' => [
                    'student' => 'Ikuti boot camp gratis atau kerjakan proyek open source kecil untuk memperkaya portofolio coding-mu.',
                    'bk' => 'Rekomendasikan siswa untuk mengikuti lomba kompetensi siswa (LKS) tingkat daerah di bidang Software Application.',
                    'wali' => 'Kelompokkan siswa ini dengan teman yang memiliki minat sejenis agar bisa berkolaborasi membuat proyek tugas akhir yang keren.',
                    'parent' => 'Fasilitasi perangkat komputer yang memadai dan koneksi internet yang mendukung eksplorasi belajar pemrograman.'
                ],
                'evidence_weight' => 1.2,
            ],
            [
                'name' => 'Motivasi Belajar',
                'category' => 'academic',
                'description' => 'Daya dorong internal maupun eksternal yang memicu siswa untuk giat belajar, memperhatikan pelajaran, dan mencapai prestasi optimal.',
                'indicators' => ['Antusias bertanya saat pelajaran', 'Mengerjakan tugas tanpa disuruh berkali-kali', 'Mencari sumber belajar tambahan sendiri', 'Memiliki target nilai yang jelas'],
                'keywords' => ['semangat', 'belajar', 'cita-cita', 'masa depan', 'males', 'malas', 'bosen', 'tidak semangat', 'rajin'],
                'synonyms' => ['learning motivation', 'dorongan belajar', 'etos belajar', 'semangat sekolah'],
                'example_behaviors' => ['Siswa mencatat penjelasan guru dengan rapi', 'Siswa tetap fokus belajar meskipun suasana kelas sedang kurang kondusif', 'Siswa mempelajari materi sebelum pelajaran dimulai'],
                'exploration_questions' => [
                    'Apa sih hal terbesar yang bikin kamu bersemangat buat datang ke sekolah setiap pagi?',
                    'Pernah nggak kamu ngerasa bosen banget sama sekolah, dan gimana kamu ngatasinnya?'
                ],
                'follow_up_questions' => [
                    'Kira-kira apa cita-cita terbesar yang pengin kamu raih setelah lulus dari SMK ini?',
                    'Siapa orang yang paling memotivasi kamu untuk terus rajin belajar?'
                ],
                'recommendations' => [
                    'student' => 'Buat visualisasi papan impian (vision board) di kamar untuk terus mengingatkanmu pada target kelulusan.',
                    'bk' => 'Gali hambatan intrinsik siswa dan hubungkan materi pelajaran dengan cita-cita karir yang ia impikan.',
                    'wali' => 'Berikan umpan balik positif secara berkala di depan kelas atas peningkatan usaha belajar siswa tersebut.',
                    'parent' => 'Tunjukkan minat terhadap apa yang dipelajari anak di sekolah dan berikan apresiasi non-material atas kerja kerasnya.'
                ],
                'evidence_weight' => 1.0,
            ],
            [
                'name' => 'Kewirausahaan',
                'category' => 'career',
                'description' => 'Jiwa bisnis dan keberanian mengambil risiko untuk menciptakan peluang usaha, menjual produk/jasa, serta mengelola keuangan secara mandiri.',
                'indicators' => ['Suka jualan di kelas', 'Tertarik melihat peluang usaha', 'Memiliki ide-ide bisnis kreatif', 'Mampu bernegosiasi dengan baik'],
                'keywords' => ['bisnis', 'jualan', 'usaha', 'omset', 'modal', 'untung', 'dagang', 'wirausaha', 'startup', 'marketing'],
                'synonyms' => ['entrepreneurship', 'bisnis sendiri', 'dagang online', 'jasa'],
                'example_behaviors' => ['Siswa menawarkan jasa instalasi OS atau desain stiker ke teman sekelas', 'Siswa rajin membaca buku/menonton konten seputar bisnis', 'Siswa aktif berdagang makanan ringan saat jam istirahat'],
                'exploration_questions' => [
                    'Kamu tertarik nggak sih buat punya usaha atau bisnis sendiri suatu hari nanti?',
                    'Pernah nggak kamu mencoba menjual barang atau jasa ke orang lain?'
                ],
                'follow_up_questions' => [
                    'Kalau dikasih modal gratis, bisnis apa yang pengin banget kamu rintis sekarang juga?',
                    'Bagaimana cara kamu menawarkan produk itu biar teman-temanmu tertarik membeli?'
                ],
                'recommendations' => [
                    'student' => 'Cobalah magang atau membantu usaha lokal untuk mempelajari operasional bisnis langsung dari ahlinya.',
                    'bk' => 'Arahkan siswa ke inkubator bisnis sekolah (jika ada) atau berikan pelatihan business plan canvas sederhana.',
                    'wali' => 'Libatkan siswa dalam kepanitiaan bazar sekolah bagian pendanaan atau pemasaran produk kelas.',
                    'parent' => 'Dukung eksperimen bisnis kecil anak di rumah dan bantu ia belajar mengelola laba usahanya.'
                ],
                'evidence_weight' => 1.1,
            ],
            [
                'name' => 'Bullying (Perundungan)',
                'category' => 'problem',
                'description' => 'Pengalaman menjadi korban tindakan penindasan, intimidasi, kekerasan verbal maupun fisik oleh teman sebaya di sekolah.',
                'indicators' => ['Dijauhi teman sekelas', 'Sering diejek secara fisik/verbal', 'Merasa tidak aman berada di sekolah', 'Memiliki kecenderungan menyendiri'],
                'keywords' => ['diejek', 'dibully', 'dijauhi', 'dipalak', 'diancam', 'diledek', 'dikerjain', 'dihina', 'sendirian'],
                'synonyms' => ['perundungan', 'intimidasi', 'dikucilkan', 'diejek nama orang tua'],
                'example_behaviors' => ['Siswa memilih duduk di pojok kelas sendirian', 'Siswa terlihat cemas saat berpapasan dengan kelompok tertentu di koridor', 'Siswa sering absen tanpa alasan medis yang jelas'],
                'exploration_questions' => [
                    'Gimana hubungan kamu dengan teman-teman di kelas? Ada yang bikin kamu ngerasa nggak nyaman nggak?',
                    'Pernah nggak kamu ngalamin perlakuan kurang mengenakkan dari teman di sekolah?'
                ],
                'follow_up_questions' => [
                    'Apa bentuk perlakuan itu? Apakah berupa ejekan kata-kata, atau ada kekerasan fisik?',
                    'Apakah kamu sudah pernah menceritakan hal ini kepada guru, wali kelas, atau orang tuamu?'
                ],
                'recommendations' => [
                    'student' => 'Kamu tidak sendirian. Jangan ragu untuk melaporkan tindakan tidak nyaman ini kepada Guru BK atau wali kelas.',
                    'bk' => 'Segera lakukan investigasi kasus perundungan secara rahasia dan berikan perlindungan psikologis serta mediasi yang aman bagi korban.',
                    'wali' => 'Pantau dinamika sosial antar-siswa di kelas saat jam kosong dan ciptakan iklim inklusif bebas perundungan.',
                    'parent' => 'Ciptakan ruang aman di rumah agar anak mau bercerita terbuka, perhatikan tanda-tanda perubahan perilaku drastis pada anak.'
                ],
                'evidence_weight' => 1.3,
            ]
        ];

        foreach ($domains as $domainData) {
            KnowledgeBaseDomain::create($domainData);
        }
    }
}
