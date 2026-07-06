-- Profilku AI Database Dump
-- MySQL Compatible
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------
-- Table structure for table `majors`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `majors`;
CREATE TABLE `majors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `majors`
INSERT INTO `majors` (`id`, `name`, `code`, `created_at`, `updated_at`) VALUES (1, 'Rekayasa Perangkat Lunak', 'RPL', '2026-07-06 07:10:35', '2026-07-06 07:10:35');
INSERT INTO `majors` (`id`, `name`, `code`, `created_at`, `updated_at`) VALUES (2, 'Teknik Komputer & Jaringan', 'TKJ', '2026-07-06 07:10:35', '2026-07-06 07:10:35');
INSERT INTO `majors` (`id`, `name`, `code`, `created_at`, `updated_at`) VALUES (3, 'Desain Komunikasi Visual', 'DKV', '2026-07-06 07:10:35', '2026-07-06 07:10:35');

-- -----------------------------------------------------
-- Table structure for table `classes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `classes`;
CREATE TABLE `classes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `major_id` bigint(20) unsigned NOT NULL,
  `homeroom_teacher_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `classes`
INSERT INTO `classes` (`id`, `name`, `major_id`, `homeroom_teacher_id`, `created_at`, `updated_at`) VALUES (1, 'XII RPL 1', 1, 3, '2026-07-06 07:10:36', '2026-07-06 07:10:36');
INSERT INTO `classes` (`id`, `name`, `major_id`, `homeroom_teacher_id`, `created_at`, `updated_at`) VALUES (2, 'XII TKJ 1', 2, NULL, '2026-07-06 07:10:36', '2026-07-06 07:10:36');
INSERT INTO `classes` (`id`, `name`, `major_id`, `homeroom_teacher_id`, `created_at`, `updated_at`) VALUES (3, 'XII DKV 1', 3, NULL, '2026-07-06 07:10:36', '2026-07-06 07:10:36');

-- -----------------------------------------------------
-- Table structure for table `users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `class_id` bigint(20) unsigned DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `users`
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `class_id`, `remember_token`, `created_at`, `updated_at`) VALUES (1, 'Administrator Profilku', 'admin@profilku.ai', NULL, '$2y$12$lJKN9Pv5HTy9IYgYQN4euOtnaM3D12YfUgwpC0355F./eAkclUVzy', 'admin', NULL, NULL, '2026-07-06 07:10:35', '2026-07-06 07:10:35');
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `class_id`, `remember_token`, `created_at`, `updated_at`) VALUES (2, 'Susi Susanti, S.Pd. (Guru BK)', 'gurubk@profilku.ai', NULL, '$2y$12$I21fg8W4YxwG.yoBHH/MjuYxEBtSpCIx5hwEftBL9bCtBljSOiYPK', 'guru_bk', NULL, NULL, '2026-07-06 07:10:35', '2026-07-06 07:10:35');
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `class_id`, `remember_token`, `created_at`, `updated_at`) VALUES (3, 'Budi Hartono, S.T. (Wali Kelas)', 'walikelas@profilku.ai', NULL, '$2y$12$kWvM0RrsQkl12ZI5PtedNOWyJyF27iebCsoivUP0e3q91IG9V4yKe', 'wali_kelas', NULL, NULL, '2026-07-06 07:10:36', '2026-07-06 07:10:36');
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `class_id`, `remember_token`, `created_at`, `updated_at`) VALUES (4, 'Rian Hidayat (Siswa)', 'student@profilku.ai', NULL, '$2y$12$qsYASaL1ZL9KYKl9tTEC7.JfxWb8i1f4yGjsL76n2ijI3ThAybL8u', 'student', 1, NULL, '2026-07-06 07:10:36', '2026-07-06 07:10:36');
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `class_id`, `remember_token`, `created_at`, `updated_at`) VALUES (5, 'Siti Aminah', 'siti@profilku.ai', NULL, '$2y$12$BNMHo.WZPTm4.hKEmKMbROgcHfIJ7QqgWqdEHkYoyovVYNZF3Wrjm', 'student', 1, NULL, '2026-07-06 07:10:37', '2026-07-06 07:10:37');
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `class_id`, `remember_token`, `created_at`, `updated_at`) VALUES (6, 'Andi Wijaya', 'andi@profilku.ai', NULL, '$2y$12$aNArTPISGLTXMsizz2YnpO0IAQNU3CKQOsza48bJhf72JO9FpIJOG', 'student', 3, NULL, '2026-07-06 07:10:37', '2026-07-06 07:10:37');

-- -----------------------------------------------------
-- Table structure for table `password_reset_tokens`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `password_reset_tokens`
-- No data found for `password_reset_tokens`

-- -----------------------------------------------------
-- Table structure for table `sessions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `sessions`
-- No data found for `sessions`

-- -----------------------------------------------------
-- Table structure for table `cache`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `cache`
-- No data found for `cache`

-- -----------------------------------------------------
-- Table structure for table `cache_locks`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `cache_locks`
-- No data found for `cache_locks`

-- -----------------------------------------------------
-- Table structure for table `jobs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `jobs`
-- No data found for `jobs`

-- -----------------------------------------------------
-- Table structure for table `job_batches`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` longtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `job_batches`
-- No data found for `job_batches`

-- -----------------------------------------------------
-- Table structure for table `failed_jobs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `failed_jobs`
-- No data found for `failed_jobs`

-- -----------------------------------------------------
-- Table structure for table `knowledge_base_domains`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `knowledge_base_domains`;
CREATE TABLE `knowledge_base_domains` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `category` enum('personality','interest','problem','academic','career') NOT NULL,
  `description` text NOT NULL,
  `indicators` json DEFAULT NULL,
  `keywords` json DEFAULT NULL,
  `synonyms` json DEFAULT NULL,
  `example_behaviors` json DEFAULT NULL,
  `exploration_questions` json DEFAULT NULL,
  `follow_up_questions` json DEFAULT NULL,
  `recommendations` json DEFAULT NULL,
  `evidence_weight` decimal(3,2) NOT NULL DEFAULT '1.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `knowledge_base_domains_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `knowledge_base_domains`
INSERT INTO `knowledge_base_domains` (`id`, `name`, `category`, `description`, `indicators`, `keywords`, `synonyms`, `example_behaviors`, `exploration_questions`, `follow_up_questions`, `recommendations`, `evidence_weight`, `created_at`, `updated_at`) VALUES (1, 'Kepercayaan Diri', 'personality', 'Kemampuan seseorang untuk meyakini kapasitas, nilai, dan keputusan diri sendiri dalam menghadapi tugas maupun pergaulan sosial.', '[\"Yakin tampil di depan umum\",\"Tidak takut salah mencoba hal baru\",\"Menerima kritik dengan positif\",\"Mampu berpendapat\"]', '[\"percaya diri\",\"minder\",\"takut salah\",\"tampil\",\"presentasi\",\"gugup\",\"pede\",\"malu\"]', '[\"self confidence\",\"rendah diri\",\"demam panggung\",\"ragu-ragu\"]', '[\"Siswa bersedia menjadi ketua kelompok\",\"Siswa berani mempresentasikan karya tanpa gemetar berlebihan\",\"Siswa tidak langsung menyerah ketika idenya dikritik\"]', '[\"Gimana perasaan kamu kalau diminta memimpin presentasi di depan kelas?\",\"Pernah nggak kamu ngerasa minder saat berkumpul atau kerja kelompok dengan teman-teman?\"]', '[\"Apa sih yang biasanya bikin kamu paling deg-degan saat harus berbicara di depan orang banyak?\",\"Bagaimana cara kamu menenangkan diri saat merasa kurang percaya diri?\"]', '{\"student\":\"Cobalah untuk aktif berpendapat di kelas, mulai dari mengajukan pertanyaan kecil kepada guru.\",\"bk\":\"Berikan siswa pelatihan public speaking sederhana atau libatkan dalam kegiatan organisasi untuk meningkatkan asertivitas.\",\"wali\":\"Tunjuk siswa sebagai perwakilan kelas dalam forum diskusi kecil agar terbiasa tampil.\",\"parent\":\"Berikan apresiasi pada usaha mandiri anak di rumah, hindari membandingkan performanya dengan anak lain.\"}', 1, '2026-07-06 07:10:39', '2026-07-06 07:10:39');
INSERT INTO `knowledge_base_domains` (`id`, `name`, `category`, `description`, `indicators`, `keywords`, `synonyms`, `example_behaviors`, `exploration_questions`, `follow_up_questions`, `recommendations`, `evidence_weight`, `created_at`, `updated_at`) VALUES (2, 'Stres Akademis', 'problem', 'Tekanan mental atau emosional akibat beban tugas, ujian, atau ekspektasi prestasi di lingkungan sekolah SMK.', '[\"Kelelahan fisik akibat tugas\",\"Kesulitan tidur karena memikirkan nilai\",\"Merasa cemas berlebih sebelum ujian\",\"Kehilangan fokus belajar\"]', '[\"stres\",\"pusing\",\"tugas banyak\",\"capek\",\"begadang\",\"lelah\",\"cemas\",\"nilai turun\",\"beban\"]', '[\"burnout\",\"tekanan belajar\",\"depresi tugas\",\"frustrasi\"]', '[\"Siswa sering terlambat mengumpulkan tugas praktikum\",\"Siswa tampak lesu saat jam pelajaran pertama\",\"Siswa mengeluhkan pusing saat menjelang ujian tengah semester\"]', '[\"Belakangan ini, tugas-tugas sekolah kerasa sangat menumpuk nggak buat kamu?\",\"Ada waktu nggak buat kamu istirahat atau bersenang-senang di luar jam sekolah?\"]', '[\"Kira-kira mata pelajaran apa yang paling sering bikin kamu kepikiran sampai susah tidur?\",\"Kalau lagi ngerasa jenuh banget sama tugas, apa yang biasanya kamu lakukan?\"]', '{\"student\":\"Atur jadwal belajar dengan metode pomodoro dan luangkan waktu 15 menit untuk relaksasi atau hobi harian.\",\"bk\":\"Bantu siswa menyusun skala prioritas tugas dan berikan konseling manajemen waktu serta teknik mindfulness.\",\"wali\":\"Koordinasikan dengan guru mata pelajaran agar pembagian tenggat waktu tugas praktikum tidak menumpuk di hari yang sama.\",\"parent\":\"Ciptakan suasana rumah yang tenang dan hindari menuntut nilai akademik yang tidak realistis.\"}', 1, '2026-07-06 07:10:39', '2026-07-06 07:10:39');
INSERT INTO `knowledge_base_domains` (`id`, `name`, `category`, `description`, `indicators`, `keywords`, `synonyms`, `example_behaviors`, `exploration_questions`, `follow_up_questions`, `recommendations`, `evidence_weight`, `created_at`, `updated_at`) VALUES (3, 'Minat Rekayasa Perangkat Lunak', 'interest', 'Ketertarikan dan antusiasme siswa SMK terhadap aktivitas pemrograman, pembuatan aplikasi, logika algoritma, dan teknologi perangkat lunak.', '[\"Senang coding berjam-jam\",\"Suka mencari tahu cara kerja suatu aplikasi\",\"Tertarik membuat website atau game sendiri\",\"Senang belajar logika matematika\\/algoritma\"]', '[\"coding\",\"pemrograman\",\"website\",\"aplikasi\",\"laravel\",\"javascript\",\"html\",\"python\",\"github\",\"bikin game\"]', '[\"software engineering\",\"bikin program\",\"ngoding\",\"developer\"]', '[\"Siswa mengutak-atik kode di luar jam sekolah\",\"Siswa bersemangat menceritakan proyek aplikasi buatan sendiri\",\"Siswa aktif berdiskusi di forum programming\"]', '[\"Hal apa yang paling bikin kamu penasaran waktu pertama kali belajar coding di kelas?\",\"Pernah nggak kamu nyoba bikin website atau aplikasi kecil di luar tugas sekolah?\"]', '[\"Bahasa pemrograman atau framework apa yang paling kamu sukai saat ini dan kenapa?\",\"Apa proyek impian yang kepengin banget kamu wujudkan di masa depan?\"]', '{\"student\":\"Ikuti boot camp gratis atau kerjakan proyek open source kecil untuk memperkaya portofolio coding-mu.\",\"bk\":\"Rekomendasikan siswa untuk mengikuti lomba kompetensi siswa (LKS) tingkat daerah di bidang Software Application.\",\"wali\":\"Kelompokkan siswa ini dengan teman yang memiliki minat sejenis agar bisa berkolaborasi membuat proyek tugas akhir yang keren.\",\"parent\":\"Fasilitasi perangkat komputer yang memadai dan koneksi internet yang mendukung eksplorasi belajar pemrograman.\"}', 1.2, '2026-07-06 07:10:39', '2026-07-06 07:10:39');
INSERT INTO `knowledge_base_domains` (`id`, `name`, `category`, `description`, `indicators`, `keywords`, `synonyms`, `example_behaviors`, `exploration_questions`, `follow_up_questions`, `recommendations`, `evidence_weight`, `created_at`, `updated_at`) VALUES (4, 'Motivasi Belajar', 'academic', 'Daya dorong internal maupun eksternal yang memicu siswa untuk giat belajar, memperhatikan pelajaran, dan mencapai prestasi optimal.', '[\"Antusias bertanya saat pelajaran\",\"Mengerjakan tugas tanpa disuruh berkali-kali\",\"Mencari sumber belajar tambahan sendiri\",\"Memiliki target nilai yang jelas\"]', '[\"semangat\",\"belajar\",\"cita-cita\",\"masa depan\",\"males\",\"malas\",\"bosen\",\"tidak semangat\",\"rajin\"]', '[\"learning motivation\",\"dorongan belajar\",\"etos belajar\",\"semangat sekolah\"]', '[\"Siswa mencatat penjelasan guru dengan rapi\",\"Siswa tetap fokus belajar meskipun suasana kelas sedang kurang kondusif\",\"Siswa mempelajari materi sebelum pelajaran dimulai\"]', '[\"Apa sih hal terbesar yang bikin kamu bersemangat buat datang ke sekolah setiap pagi?\",\"Pernah nggak kamu ngerasa bosen banget sama sekolah, dan gimana kamu ngatasinnya?\"]', '[\"Kira-kira apa cita-cita terbesar yang pengin kamu raih setelah lulus dari SMK ini?\",\"Siapa orang yang paling memotivasi kamu untuk terus rajin belajar?\"]', '{\"student\":\"Buat visualisasi papan impian (vision board) di kamar untuk terus mengingatkanmu pada target kelulusan.\",\"bk\":\"Gali hambatan intrinsik siswa dan hubungkan materi pelajaran dengan cita-cita karir yang ia impikan.\",\"wali\":\"Berikan umpan balik positif secara berkala di depan kelas atas peningkatan usaha belajar siswa tersebut.\",\"parent\":\"Tunjukkan minat terhadap apa yang dipelajari anak di sekolah dan berikan apresiasi non-material atas kerja kerasnya.\"}', 1, '2026-07-06 07:10:39', '2026-07-06 07:10:39');
INSERT INTO `knowledge_base_domains` (`id`, `name`, `category`, `description`, `indicators`, `keywords`, `synonyms`, `example_behaviors`, `exploration_questions`, `follow_up_questions`, `recommendations`, `evidence_weight`, `created_at`, `updated_at`) VALUES (5, 'Kewirausahaan', 'career', 'Jiwa bisnis dan keberanian mengambil risiko untuk menciptakan peluang usaha, menjual produk/jasa, serta mengelola keuangan secara mandiri.', '[\"Suka jualan di kelas\",\"Tertarik melihat peluang usaha\",\"Memiliki ide-ide bisnis kreatif\",\"Mampu bernegosiasi dengan baik\"]', '[\"bisnis\",\"jualan\",\"usaha\",\"omset\",\"modal\",\"untung\",\"dagang\",\"wirausaha\",\"startup\",\"marketing\"]', '[\"entrepreneurship\",\"bisnis sendiri\",\"dagang online\",\"jasa\"]', '[\"Siswa menawarkan jasa instalasi OS atau desain stiker ke teman sekelas\",\"Siswa rajin membaca buku\\/menonton konten seputar bisnis\",\"Siswa aktif berdagang makanan ringan saat jam istirahat\"]', '[\"Kamu tertarik nggak sih buat punya usaha atau bisnis sendiri suatu hari nanti?\",\"Pernah nggak kamu mencoba menjual barang atau jasa ke orang lain?\"]', '[\"Kalau dikasih modal gratis, bisnis apa yang pengin banget kamu rintis sekarang juga?\",\"Bagaimana cara kamu menawarkan produk itu biar teman-temanmu tertarik membeli?\"]', '{\"student\":\"Cobalah magang atau membantu usaha lokal untuk mempelajari operasional bisnis langsung dari ahlinya.\",\"bk\":\"Arahkan siswa ke inkubator bisnis sekolah (jika ada) atau berikan pelatihan business plan canvas sederhana.\",\"wali\":\"Libatkan siswa dalam kepanitiaan bazar sekolah bagian pendanaan atau pemasaran produk kelas.\",\"parent\":\"Dukung eksperimen bisnis kecil anak di rumah dan bantu ia belajar mengelola laba usahanya.\"}', 1.1, '2026-07-06 07:10:39', '2026-07-06 07:10:39');
INSERT INTO `knowledge_base_domains` (`id`, `name`, `category`, `description`, `indicators`, `keywords`, `synonyms`, `example_behaviors`, `exploration_questions`, `follow_up_questions`, `recommendations`, `evidence_weight`, `created_at`, `updated_at`) VALUES (6, 'Bullying (Perundungan)', 'problem', 'Pengalaman menjadi korban tindakan penindasan, intimidasi, kekerasan verbal maupun fisik oleh teman sebaya di sekolah.', '[\"Dijauhi teman sekelas\",\"Sering diejek secara fisik\\/verbal\",\"Merasa tidak aman berada di sekolah\",\"Memiliki kecenderungan menyendiri\"]', '[\"diejek\",\"dibully\",\"dijauhi\",\"dipalak\",\"diancam\",\"diledek\",\"dikerjain\",\"dihina\",\"sendirian\"]', '[\"perundungan\",\"intimidasi\",\"dikucilkan\",\"diejek nama orang tua\"]', '[\"Siswa memilih duduk di pojok kelas sendirian\",\"Siswa terlihat cemas saat berpapasan dengan kelompok tertentu di koridor\",\"Siswa sering absen tanpa alasan medis yang jelas\"]', '[\"Gimana hubungan kamu dengan teman-teman di kelas? Ada yang bikin kamu ngerasa nggak nyaman nggak?\",\"Pernah nggak kamu ngalamin perlakuan kurang mengenakkan dari teman di sekolah?\"]', '[\"Apa bentuk perlakuan itu? Apakah berupa ejekan kata-kata, atau ada kekerasan fisik?\",\"Apakah kamu sudah pernah menceritakan hal ini kepada guru, wali kelas, atau orang tuamu?\"]', '{\"student\":\"Kamu tidak sendirian. Jangan ragu untuk melaporkan tindakan tidak nyaman ini kepada Guru BK atau wali kelas.\",\"bk\":\"Segera lakukan investigasi kasus perundungan secara rahasia dan berikan perlindungan psikologis serta mediasi yang aman bagi korban.\",\"wali\":\"Pantau dinamika sosial antar-siswa di kelas saat jam kosong dan ciptakan iklim inklusif bebas perundungan.\",\"parent\":\"Ciptakan ruang aman di rumah agar anak mau bercerita terbuka, perhatikan tanda-tanda perubahan perilaku drastis pada anak.\"}', 1.3, '2026-07-06 07:10:40', '2026-07-06 07:10:40');

-- -----------------------------------------------------
-- Table structure for table `ai_providers`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ai_providers`;
CREATE TABLE `ai_providers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `api_key` varchar(255) DEFAULT NULL,
  `model` varchar(255) NOT NULL,
  `temperature` float NOT NULL DEFAULT '0.7',
  `top_p` float NOT NULL DEFAULT '0.9',
  `max_tokens` int(11) NOT NULL DEFAULT '1000',
  `system_prompt` text NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ai_providers_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `ai_providers`
INSERT INTO `ai_providers` (`id`, `name`, `api_key`, `model`, `temperature`, `top_p`, `max_tokens`, `system_prompt`, `is_active`, `created_at`, `updated_at`) VALUES (1, 'gemini', '', 'gemini-1.5-flash', 0.7, 0.95, 1200, 'Anda adalah Pendamping & Mentor AI siswa SMK bernama Profilku AI. Gaya bahasa Anda harus hangat, ramah, seperti sahabat karib (menggunakan panggilan \'kamu\' atau nama panggilan siswa), menyemangati, dan tidak pernah terdengar seperti sedang mengisi kuesioner formal ataupun psikiater klinis.\nTujuan percakapan ini adalah untuk mengenali minat, bakat, masalah, dan potensi siswa secara mengalir alami. JANGAN AJUKAN PERTANYAAN BERTUBI-TUBI. Ajukan SATU pertanyaan terbuka setiap kali merespons, dengarkan jawaban siswa, lakukan validasi emosi mereka terlebih dahulu, lalu gali lebih dalam jika ada informasi menarik.\nAturan penting: JANGAN PERNAH mendiagnosis penyakit mental (seperti depresi klinis, OCD, bipolar, dll). Jika mendeteksi masalah berat seperti perundungan fisik atau stres akademis yang berlebih, gunakan istilah \'kemungkinan indikasi\', \'memerlukan obrolan lebih lanjut\', atau sarankan secara halus untuk berkonsultasi dengan Guru BK sekolah (Ibu Susi).\nGunakan data memori siswa yang disediakan di dalam context untuk menghindari menanyakan hal yang sudah diketahui sebelumnya.', 1, '2026-07-06 07:10:37', '2026-07-06 07:10:37');
INSERT INTO `ai_providers` (`id`, `name`, `api_key`, `model`, `temperature`, `top_p`, `max_tokens`, `system_prompt`, `is_active`, `created_at`, `updated_at`) VALUES (2, 'openrouter', '', 'google/gemini-2.5-flash', 0.7, 0.9, 1200, 'Anda adalah Pendamping & Mentor AI siswa SMK bernama Profilku AI. Gaya bahasa Anda harus hangat, ramah, seperti sahabat karib (menggunakan panggilan \'kamu\' atau nama panggilan siswa), menyemangati, dan tidak pernah terdengar seperti sedang mengisi kuesioner formal ataupun psikiater klinis.\nTujuan percakapan ini adalah untuk mengenali minat, bakat, masalah, dan potensi siswa secara mengalir alami. JANGAN AJUKAN PERTANYAAN BERTUBI-TUBI. Ajukan SATU pertanyaan terbuka setiap kali merespons, dengarkan jawaban siswa, lakukan validasi emosi mereka terlebih dahulu, lalu gali lebih dalam jika ada informasi menarik.\nAturan penting: JANGAN PERNAH mendiagnosis penyakit mental (seperti depresi klinis, OCD, bipolar, dll). Jika mendeteksi masalah berat seperti perundungan fisik atau stres akademis yang berlebih, gunakan istilah \'kemungkinan indikasi\', \'memerlukan obrolan lebih lanjut\', atau sarankan secara halus untuk berkonsultasi dengan Guru BK sekolah (Ibu Susi).\nGunakan data memori siswa yang disediakan di dalam context untuk menghindari menanyakan hal yang sudah diketahui sebelumnya.', 0, '2026-07-06 07:10:37', '2026-07-06 07:10:37');
INSERT INTO `ai_providers` (`id`, `name`, `api_key`, `model`, `temperature`, `top_p`, `max_tokens`, `system_prompt`, `is_active`, `created_at`, `updated_at`) VALUES (3, 'groq', '', 'llama-3.3-70b-versatile', 0.7, 0.9, 1200, 'Anda adalah Pendamping & Mentor AI siswa SMK bernama Profilku AI. Gaya bahasa Anda harus hangat, ramah, seperti sahabat karib (menggunakan panggilan \'kamu\' atau nama panggilan siswa), menyemangati, dan tidak pernah terdengar seperti sedang mengisi kuesioner formal ataupun psikiater klinis.\nTujuan percakapan ini adalah untuk mengenali minat, bakat, masalah, dan potensi siswa secara mengalir alami. JANGAN AJUKAN PERTANYAAN BERTUBI-TUBI. Ajukan SATU pertanyaan terbuka setiap kali merespons, dengarkan jawaban siswa, lakukan validasi emosi mereka terlebih dahulu, lalu gali lebih dalam jika ada informasi menarik.\nAturan penting: JANGAN PERNAH mendiagnosis penyakit mental (seperti depresi klinis, OCD, bipolar, dll). Jika mendeteksi masalah berat seperti perundungan fisik atau stres akademis yang berlebih, gunakan istilah \'kemungkinan indikasi\', \'memerlukan obrolan lebih lanjut\', atau sarankan secara halus untuk berkonsultasi dengan Guru BK sekolah (Ibu Susi).\nGunakan data memori siswa yang disediakan di dalam context untuk menghindari menanyakan hal yang sudah diketahui sebelumnya.', 0, '2026-07-06 07:10:37', '2026-07-06 07:10:37');
INSERT INTO `ai_providers` (`id`, `name`, `api_key`, `model`, `temperature`, `top_p`, `max_tokens`, `system_prompt`, `is_active`, `created_at`, `updated_at`) VALUES (4, 'huggingface', '', 'meta-llama/Llama-3.2-3B-Instruct', 0.7, 0.9, 1200, 'Anda adalah Pendamping & Mentor AI siswa SMK bernama Profilku AI. Gaya bahasa Anda harus hangat, ramah, seperti sahabat karib (menggunakan panggilan \'kamu\' atau nama panggilan siswa), menyemangati, dan tidak pernah terdengar seperti sedang mengisi kuesioner formal ataupun psikiater klinis.\nTujuan percakapan ini adalah untuk mengenali minat, bakat, masalah, dan potensi siswa secara mengalir alami. JANGAN AJUKAN PERTANYAAN BERTUBI-TUBI. Ajukan SATU pertanyaan terbuka setiap kali merespons, dengarkan jawaban siswa, lakukan validasi emosi mereka terlebih dahulu, lalu gali lebih dalam jika ada informasi menarik.\nAturan penting: JANGAN PERNAH mendiagnosis penyakit mental (seperti depresi klinis, OCD, bipolar, dll). Jika mendeteksi masalah berat seperti perundungan fisik atau stres akademis yang berlebih, gunakan istilah \'kemungkinan indikasi\', \'memerlukan obrolan lebih lanjut\', atau sarankan secara halus untuk berkonsultasi dengan Guru BK sekolah (Ibu Susi).\nGunakan data memori siswa yang disediakan di dalam context untuk menghindari menanyakan hal yang sudah diketahui sebelumnya.', 0, '2026-07-06 07:10:37', '2026-07-06 07:10:37');

-- -----------------------------------------------------
-- Table structure for table `rules`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `rules`;
CREATE TABLE `rules` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `priority` varchar(255) NOT NULL DEFAULT 'Medium',
  `trigger_condition` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `parameters` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `rules`
INSERT INTO `rules` (`id`, `name`, `category`, `priority`, `trigger_condition`, `action`, `description`, `parameters`, `is_active`, `created_at`, `updated_at`) VALUES (1, 'Response Too Short', 'Conversation', 'Medium', 'length(student_response) < 15', 'elaboration_prompt', 'Memicu AI untuk meminta penjelasan tambahan jika jawaban murid terlalu pendek (misalnya hanya \"ya\", \"tidak tahu\", \"biasa saja\").', NULL, 1, '2026-07-06 07:10:37', '2026-07-06 07:10:37');
INSERT INTO `rules` (`id`, `name`, `category`, `priority`, `trigger_condition`, `action`, `description`, `parameters`, `is_active`, `created_at`, `updated_at`) VALUES (2, 'Ambiguous Response', 'Conversation', 'Medium', 'ambiguous_keywords_detected', 'clarify_response', 'Klarifikasi jika jawaban murid mengandung kontradiksi atau keraguan.', NULL, 1, '2026-07-06 07:10:37', '2026-07-06 07:10:37');
INSERT INTO `rules` (`id`, `name`, `category`, `priority`, `trigger_condition`, `action`, `description`, `parameters`, `is_active`, `created_at`, `updated_at`) VALUES (3, 'Generic Response', 'Conversation', 'Low', 'generic_fillers_detected', 'suggest_alternatives', 'Mendorong respon yang lebih spesifik jika murid menggunakan kata-kata pengisi umum.', NULL, 1, '2026-07-06 07:10:38', '2026-07-06 07:10:38');
INSERT INTO `rules` (`id`, `name`, `category`, `priority`, `trigger_condition`, `action`, `description`, `parameters`, `is_active`, `created_at`, `updated_at`) VALUES (4, 'Contradictory Response', 'Conversation', 'High', 'contradiction_detected', 'highlight_contradiction', 'Mendeteksi dan mengonfrontasi secara halus jika respon murid bertentangan dengan fakta sebelumnya.', NULL, 1, '2026-07-06 07:10:38', '2026-07-06 07:10:38');
INSERT INTO `rules` (`id`, `name`, `category`, `priority`, `trigger_condition`, `action`, `description`, `parameters`, `is_active`, `created_at`, `updated_at`) VALUES (5, 'Confidence Below 70%', 'Confidence', 'High', 'confidence_score < 70', 'deepen_exploration', 'Jika skor pemahaman domain masih di bawah 70%, sistem menginstruksikan AI untuk terus mengeksplorasi domain terkait dengan pertanyaan alternatif.', NULL, 1, '2026-07-06 07:10:38', '2026-07-06 07:10:38');
INSERT INTO `rules` (`id`, `name`, `category`, `priority`, `trigger_condition`, `action`, `description`, `parameters`, `is_active`, `created_at`, `updated_at`) VALUES (6, 'Confidence Above 90%', 'Confidence', 'Medium', 'confidence_score > 90', 'lock_domain', 'Mengunci domain jika tingkat keyakinan telah sangat tinggi dan berpindah ke eksplorasi berikutnya.', NULL, 1, '2026-07-06 07:10:38', '2026-07-06 07:10:38');
INSERT INTO `rules` (`id`, `name`, `category`, `priority`, `trigger_condition`, `action`, `description`, `parameters`, `is_active`, `created_at`, `updated_at`) VALUES (7, 'Unexplored Domain', 'Domain Coverage', 'High', 'unexplored_domains_exist', 'switch_domain', 'Pindah ke domain kompetensi atau pemantauan lain yang belum sempat dibahas.', NULL, 1, '2026-07-06 07:10:38', '2026-07-06 07:10:38');
INSERT INTO `rules` (`id`, `name`, `category`, `priority`, `trigger_condition`, `action`, `description`, `parameters`, `is_active`, `created_at`, `updated_at`) VALUES (8, 'Minimum Questions Per Domain', 'Domain Coverage', 'Medium', 'questions_asked_in_domain < 3', 'continue_domain_exploration', 'Memastikan minimal 3 pertanyaan diajukan sebelum menganggap satu domain telah dieksplorasi.', NULL, 1, '2026-07-06 07:10:38', '2026-07-06 07:10:38');
INSERT INTO `rules` (`id`, `name`, `category`, `priority`, `trigger_condition`, `action`, `description`, `parameters`, `is_active`, `created_at`, `updated_at`) VALUES (9, 'Insufficient Evidence', 'Evidence', 'High', 'evidence_count < 2', 'gather_more_evidence', 'Meminta bukti kutipan tambahan jika informasi yang mendukung kesimpulan belum kuat.', NULL, 1, '2026-07-06 07:10:38', '2026-07-06 07:10:38');
INSERT INTO `rules` (`id`, `name`, `category`, `priority`, `trigger_condition`, `action`, `description`, `parameters`, `is_active`, `created_at`, `updated_at`) VALUES (10, 'Conflicting Evidence', 'Evidence', 'Critical', 'conflicting_evidence_detected', 'resolve_conflict', 'Mendeteksi pertentangan bukti antar domain untuk klarifikasi bimbingan.', NULL, 1, '2026-07-06 07:10:38', '2026-07-06 07:10:38');
INSERT INTO `rules` (`id`, `name`, `category`, `priority`, `trigger_condition`, `action`, `description`, `parameters`, `is_active`, `created_at`, `updated_at`) VALUES (11, 'Prevent Duplicate Questions', 'Memory', 'High', 'question_exists_in_memory', 'rephrase_question', 'Mencegah AI menanyakan pertanyaan yang mirip dengan yang sudah pernah ditanyakan.', NULL, 1, '2026-07-06 07:10:38', '2026-07-06 07:10:38');
INSERT INTO `rules` (`id`, `name`, `category`, `priority`, `trigger_condition`, `action`, `description`, `parameters`, `is_active`, `created_at`, `updated_at`) VALUES (12, 'Remember Student Facts', 'Memory', 'Medium', 'new_fact_extracted', 'save_to_memory', 'Secara aktif merekam fakta pribadi murid seperti nama panggilan, cita-cita, atau hobi.', NULL, 1, '2026-07-06 07:10:38', '2026-07-06 07:10:38');
INSERT INTO `rules` (`id`, `name`, `category`, `priority`, `trigger_condition`, `action`, `description`, `parameters`, `is_active`, `created_at`, `updated_at`) VALUES (13, 'Sensitive Content Detection', 'Safety', 'Critical', 'sensitive_content_detected', 'alert_counselor', 'Memicu notifikasi darurat ke Guru BK jika murid membicarakan tindakan self-harm, perundungan fisik, atau kekerasan.', NULL, 1, '2026-07-06 07:10:38', '2026-07-06 07:10:38');
INSERT INTO `rules` (`id`, `name`, `category`, `priority`, `trigger_condition`, `action`, `description`, `parameters`, `is_active`, `created_at`, `updated_at`) VALUES (14, 'Prevent Psychological Diagnosis', 'Safety', 'Critical', 'psychological_diagnosis_attempt', 'refuse_clinical_diagnosis', 'Melarang AI mendiagnosis kelainan mental klinis secara formal (depresi klinis, OCD, bipolar, dll).', NULL, 1, '2026-07-06 07:10:38', '2026-07-06 07:10:38');
INSERT INTO `rules` (`id`, `name`, `category`, `priority`, `trigger_condition`, `action`, `description`, `parameters`, `is_active`, `created_at`, `updated_at`) VALUES (15, 'Unknown Career Goal', 'Career', 'Medium', 'career_goal_empty', 'explore_career_options', 'Mengarahkan percakapan untuk menggali potensi karir jika murid belum menentukan cita-cita.', NULL, 1, '2026-07-06 07:10:39', '2026-07-06 07:10:39');
INSERT INTO `rules` (`id`, `name`, `category`, `priority`, `trigger_condition`, `action`, `description`, `parameters`, `is_active`, `created_at`, `updated_at`) VALUES (16, 'Validate Career Choice', 'Career', 'Medium', 'career_goal_defined', 'validate_alignment', 'Memvalidasi keselarasan antara cita-cita karir dengan hobi dan minat bakat.', NULL, 1, '2026-07-06 07:10:39', '2026-07-06 07:10:39');
INSERT INTO `rules` (`id`, `name`, `category`, `priority`, `trigger_condition`, `action`, `description`, `parameters`, `is_active`, `created_at`, `updated_at`) VALUES (17, 'Hide Low Confidence Conclusions', 'Report', 'High', 'report_generation && confidence_score < 50', 'exclude_from_report', 'Menyembunyikan kesimpulan domain dengan tingkat keyakinan rendah dari laporan resmi.', NULL, 1, '2026-07-06 07:10:39', '2026-07-06 07:10:39');
INSERT INTO `rules` (`id`, `name`, `category`, `priority`, `trigger_condition`, `action`, `description`, `parameters`, `is_active`, `created_at`, `updated_at`) VALUES (18, 'Require Minimum Evidence', 'Report', 'High', 'report_generation && evidence_count < 1', 'flag_as_unverified', 'Menandai domain sebagai \'belum terverifikasi\' jika tidak memiliki minimal satu bukti kutipan percakapan.', NULL, 1, '2026-07-06 07:10:39', '2026-07-06 07:10:39');
INSERT INTO `rules` (`id`, `name`, `category`, `priority`, `trigger_condition`, `action`, `description`, `parameters`, `is_active`, `created_at`, `updated_at`) VALUES (19, 'Auto Save Conversation', 'Session', 'High', 'message_received', 'save_state', 'Menyimpan histori pesan secara berkala untuk mencegah data hilang.', NULL, 1, '2026-07-06 07:10:39', '2026-07-06 07:10:39');
INSERT INTO `rules` (`id`, `name`, `category`, `priority`, `trigger_condition`, `action`, `description`, `parameters`, `is_active`, `created_at`, `updated_at`) VALUES (20, 'Resume Previous Session', 'Session', 'Medium', 'session_reopened', 'load_previous_state', 'Memulihkan sesi percakapan bimbingan terakhir saat murid kembali masuk.', NULL, 1, '2026-07-06 07:10:39', '2026-07-06 07:10:39');
INSERT INTO `rules` (`id`, `name`, `category`, `priority`, `trigger_condition`, `action`, `description`, `parameters`, `is_active`, `created_at`, `updated_at`) VALUES (21, 'End Session When Complete', 'Session', 'Critical', 'current_stage == 12', 'compile_final_report', 'Menghentikan sesi konseling AI dan menyusun laporan profiling akhir saat tahap 12 tercapai.', NULL, 1, '2026-07-06 07:10:39', '2026-07-06 07:10:39');

-- -----------------------------------------------------
-- Table structure for table `conversations`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `conversations`;
CREATE TABLE `conversations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `status` enum('active','paused','completed') NOT NULL DEFAULT 'active',
  `current_stage` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `conversations`
-- No data found for `conversations`

-- -----------------------------------------------------
-- Table structure for table `conversation_messages`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `conversation_messages`;
CREATE TABLE `conversation_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint(20) unsigned NOT NULL,
  `sender` enum('student','ai') NOT NULL,
  `message_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `conversation_messages`
-- No data found for `conversation_messages`

-- -----------------------------------------------------
-- Table structure for table `student_memories`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `student_memories`;
CREATE TABLE `student_memories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `confidence` float NOT NULL DEFAULT '1',
  `source_message_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `student_memories`
-- No data found for `student_memories`

-- -----------------------------------------------------
-- Table structure for table `evidence`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `evidence`;
CREATE TABLE `evidence` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `domain_id` bigint(20) unsigned NOT NULL,
  `indicator` varchar(255) NOT NULL,
  `excerpt` text NOT NULL,
  `weight` float NOT NULL DEFAULT '0.5',
  `reasoning` text NOT NULL,
  `source_message_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `evidence`
-- No data found for `evidence`

-- -----------------------------------------------------
-- Table structure for table `confidence_scores`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `confidence_scores`;
CREATE TABLE `confidence_scores` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `domain_id` bigint(20) unsigned NOT NULL,
  `score` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `confidence_scores_student_id_domain_id_unique` (`student_id`,`domain_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `confidence_scores`
-- No data found for `confidence_scores`

-- -----------------------------------------------------
-- Table structure for table `teacher_notes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `teacher_notes`;
CREATE TABLE `teacher_notes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `teacher_id` bigint(20) unsigned NOT NULL,
  `student_id` bigint(20) unsigned NOT NULL,
  `note_text` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `teacher_notes`
-- No data found for `teacher_notes`

-- -----------------------------------------------------
-- Table structure for table `reports`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `reports`;
CREATE TABLE `reports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `executive_summary` text DEFAULT NULL,
  `personality_analysis` text DEFAULT NULL,
  `strengths` text DEFAULT NULL,
  `development_areas` text DEFAULT NULL,
  `interests` text DEFAULT NULL,
  `talents` text DEFAULT NULL,
  `problems` text DEFAULT NULL,
  `motivation` text DEFAULT NULL,
  `career_goals` text DEFAULT NULL,
  `confidence_scores_json` json DEFAULT NULL,
  `evidence_json` json DEFAULT NULL,
  `student_recommendations` text DEFAULT NULL,
  `bk_recommendations` text DEFAULT NULL,
  `wali_recommendations` text DEFAULT NULL,
  `parent_recommendations` text DEFAULT NULL,
  `follow_up_plan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `reports`
-- No data found for `reports`

SET FOREIGN_KEY_CHECKS = 1;
