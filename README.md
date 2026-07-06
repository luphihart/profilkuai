# Profilku AI 🤖✨
### Asisten Virtual Bimbingan Konseling & Profiling Kepribadian Murid SMK berbasis AI Adaptif

Profilku AI adalah platform bimbingan konseling cerdas yang dirancang khusus untuk lingkungan sekolah menengah kejuruan (SMK). Menggunakan kombinasi **AI Generatif** (Gemini, OpenRouter, Groq, HuggingFace) dan **Dynamic Expert Rule Engine**, sistem ini mendampingi murid mengenali minat, bakat, potensi diri, serta hambatan belajar secara santai dan mengalir layaknya bercakap-cakap dengan sahabat.

---

## 🌟 Fitur Utama Aplikasi

### 1. Panel Murid (Student Dashboard)
* **Konseling AI Interaktif 12 Tahap**: Percakapan terstruktur dari *Ice Breaking*, perkenalan diri, eksplorasi minat/bakat, lingkungan sosial, hambatan belajar, hingga penutupan dan perumusan cita-cita karir (kerja/kuliah/wirausaha).
* **Suggestion Chips**: Saran balasan dinamis berdasarkan tahap obrolan aktif untuk mempermudah respons murid, terutama saat diakses melalui perangkat mobile/HP.
* **Lencana Pencapaian (Achievements)**: Sistem gamifikasi yang memberikan lencana visual saat murid berhasil membuka memori atau menyelesaikan tahap obrolan tertentu.

### 2. Panel Guru BK (Counselor Panel)
* **Explainable AI (XAI) Tracing**: Tracing keputusan AI dengan bukti kutipan kalimat asli murid (*Evidence Logs*) yang dianalisis oleh *Evidence Engine* disertai bobot skor keyakinan.
* **Rekomendasi Aksi Konkret Otomatis**: Generator draf bimbingan taktis untuk Guru BK, Wali Kelas, dan Orang Tua murid berdasarkan profil kepribadian yang dihimpun AI.
* **Catatan & Reset Sesi**: Menulis catatan konseling manual serta tombol **Reset Sesi** untuk mengosongkan riwayat obrolan agar murid dapat memulai ulang bimbingan dari awal.

### 3. Panel Wali Kelas
* **Monitoring Kelas**: Dasbor khusus untuk memantau kemajuan bimbingan murid-murid di kelas yang diampu beserta grafik statistik kesiapan karir murid.

### 4. Panel Administrator (Super Admin)
* **Dynamic Rule Engine CRUD**: Mengelola aturan pakar alur percakapan (ON/OFF, filter kategori, prioritas aturan, parameter pemicu, dan tindakan sistem).
* **Manajemen Pengguna & Plotting Kelas**: Manajemen data murid, Guru BK, dan Wali Kelas lengkap dengan **impor pengguna massal via Excel (.xlsx) nativ** serta pemetaan kelas dan jurusan.
* **Integrasi AI Multi-Provider**: Switcher provider AI (Gemini, OpenRouter, Groq, HuggingFace) dengan asinkronus model selector dan tombol **Tes Koneksi** real-time berbasis AJAX.
* **Sistem Utilitas**: Cadangan database lengkap (.SQL) ekspor/restore sekali klik, dan pencatatan audit log tindakan admin.

---

## 💻 Persyaratan Sistem

Sebelum memulai instalasi, pastikan server atau komputer lokal Anda telah memenuhi persyaratan berikut:
* **PHP** >= 8.2 (dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `xml`, `zip`, `gd`)
* **MySQL** >= 8.0 atau **MariaDB** >= 10.4
* **Composer** >= 2.0
* **Node.js** >= 18.0 & **NPM** >= 9.0

---

## 🚀 Panduan Instalasi Lokal (Development)

1. **Clone dan Pindahkan File**:
   Ekstrak file atau clone repositori ini ke folder server lokal Anda.

2. **Instal Dependensi PHP**:
   ```bash
   composer install
   ```

3. **Instal Dependensi Javascript**:
   ```bash
   npm install
   ```

4. **Konfigurasi Environment**:
   Salin file `.env.example` menjadi `.env`:
   ```bash
   cp .env.example .env
   ```
   Buka file `.env` menggunakan teks editor Anda dan sesuaikan konfigurasi database:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=db_profilku_ai
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Generate Application Key**:
   ```bash
   php artisan key:generate
   ```

6. **Migrasi Database & Seeder**:
   Jalankan migrasi tabel beserta data awal bawaan (Jurusan, Kelas, Aturan Pakar, Akun Uji Coba):
   ```bash
   php artisan migrate:fresh --seed
   ```

7. **Compile Aset Frontend (Vite & Tailwind CSS)**:
   ```bash
   npm run build
   ```

8. **Jalankan Aplikasi**:
   ```bash
   php artisan serve
   ```
   Buka browser Anda dan akses: `http://127.0.0.1:8000`

---

## 🔐 Kredensial Akun Uji Coba Default

Semua akun uji coba di bawah menggunakan password default: **`password`**

* **Administrator**: `admin@profilku.ai`
* **Guru BK (Ibu Susi)**: `gurubk@profilku.ai`
* **Wali Kelas**: `walikelas@profilku.ai`
* **Murid Uji Coba (Rian)**: `student@profilku.ai`

---

## 🌐 Panduan Deployment ke Web Hosting

### METODE A: Shared Hosting / cPanel (Paling Populer di Sekolah)

Dalam cPanel shared hosting, folder publik Laravel (`public`) harus disejajarkan atau diarahkan dengan benar. Cara termudah tanpa memindahkan file keluar dari struktur Laravel adalah dengan menggunakan file `.htaccess` di root directory.

#### Langkah 1: Persiapan File & Upload
1. Jalankan `npm run build` di komputer lokal Anda untuk memastikan seluruh aset CSS/JS ter-compile.
2. Kompres seluruh folder proyek (termasuk folder `public`, `vendor`, `node_modules`, `.env`, dll) menjadi satu berkas **`archive.zip`**.
3. Buka **cPanel** sekolah Anda, lalu masuk ke **File Manager**.
4. Upload file `archive.zip` ke direktori root hosting Anda (misal: di dalam direktori `/home/username/public_html/` atau satu tingkat di atasnya `/home/username/`).
5. Ekstrak file zip tersebut di lokasi upload Anda.

#### Langkah 2: Konfigurasi File `.htaccess` di cPanel
Jika Anda mengekstrak file proyek langsung di dalam folder `public_html`, buat file bernama **`.htaccess`** di dalam folder `public_html` tersebut (jika belum ada) dan isi dengan kode pengalihan berikut agar folder `public` Laravel diakses secara otomatis sebagai root domain:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_URI} !^/public/
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

#### Langkah 3: Setup Database & Environment
1. Masuk ke menu **cPanel** > **MySQL Database Wizard**.
2. Buat database baru (misal: `sekolah_db_profilku`).
3. Buat user database baru dan buat password yang kuat, lalu berikan hak akses penuh (*All Privileges*) kepada user tersebut untuk database baru.
4. Edit file **`.env`** proyek Anda di File Manager cPanel, ubah variabel berikut dengan detail baru:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://nama-domain-sekolah.sch.id

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_DATABASE=namausercpanel_sekolah_db_profilku
   DB_USERNAME=namausercpanel_userdbbaru
   DB_PASSWORD=password_user_db_baru
   ```

#### Langkah 4: Jalankan Migrasi Database
* **Opsi 1 (Jika cPanel memiliki akses Terminal)**:
  Buka aplikasi **Terminal** di cPanel Anda, ketik command:
  ```bash
  php artisan migrate --force
  php artisan db:seed --force
  ```
* **Opsi 2 (Jika cPanel tidak memiliki Terminal)**:
  Tambahkan rute sementara di bagian paling bawah file `routes/web.php` Anda:
  ```php
  Route::get('/run-migration-helper', function() {
      Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
      return 'Migrasi & Seeder Database Berhasil!';
  });
  ```
  Akses URL `https://nama-domain-sekolah.sch.id/run-migration-helper` di browser untuk memicu migrasi database. **Penting: Segera hapus kode rute sementara ini setelah migrasi berhasil demi keamanan!**

#### Langkah 5: Hubungkan Storage Symlink
Agar foto profil/berkas laporan dapat diakses publik, buat symlink manual.
Jika cPanel tidak memiliki Terminal, buat file PHP baru bernama `symlink.php` di dalam folder `public_html` Anda:
```php
<?php
symlink('/home/username/storage/app/public', '/home/username/public_html/storage');
echo 'Symlink berhasil dibuat!';
```
Akses URL `https://nama-domain-sekolah.sch.id/symlink.php` satu kali melalui browser, lalu hapus berkas tersebut.

---

### METODE B: Virtual Private Server (VPS) - Ubuntu Server

Metode ini disarankan untuk performa terbaik dan kestabilan sistem tingkat lanjut menggunakan Nginx Web Server dan SSL gratis (Let's Encrypt).

#### Langkah 1: Persiapan Server & Clone Repo
1. Hubungkan ke VPS Anda via SSH:
   ```bash
   ssh root@ip-address-vps
   ```
2. Instal PHP, Nginx, MySQL, Node.js, dan Git di server Ubuntu Anda:
   ```bash
   sudo apt update
   sudo apt install git nginx mysql-server php-fpm php-mysql php-xml php-mbstring php-zip php-gd unzip curl -y
   ```
3. Unduh Composer secara global:
   ```bash
   curl -sS https://getcomposer.org/installer | php
   sudo mv composer.phar /usr/local/bin/composer
   ```
4. Kloning proyek ke direktori server:
   ```bash
   cd /var/www
   sudo git clone https://github.com/username/profilku-ai.git profilku-ai
   cd profilku-ai
   ```

#### Langkah 2: Instal Dependensi & Konfigurasi
1. Jalankan instalasi composer (produksi):
   ```bash
   composer install --no-dev --optimize-autoloader
   ```
2. Salin dan konfigurasi berkas `.env` seperti panduan lokal, pastikan mengubah `APP_ENV=production` dan mengisi detail database produksi.
3. Jalankan migrasi database:
   ```bash
   php artisan migrate --force --seed
   ```
4. Atur kepemilikan dan hak akses direktori penyimpanan agar dapat ditulisi oleh web server Nginx (`www-data`):
   ```bash
   sudo chown -R www-data:www-data /var/www/profilku-ai/storage
   sudo chown -R www-data:www-data /var/www/profilku-ai/bootstrap/cache
   sudo chmod -R 775 /var/www/profilku-ai/storage
   ```
5. Buat tautan penyimpanan publik:
   ```bash
   php artisan storage:link
   ```

#### Langkah 3: Konfigurasi Nginx Server Block
Buat berkas konfigurasi baru untuk situs Anda di `/etc/nginx/sites-available/profilku-ai`:
```nginx
server {
    listen 80;
    server_name nama-domain-sekolah.sch.id;
    root /var/www/profilku-ai/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock; # Sesuaikan dengan versi PHP FPM Anda
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```
Aktifkan konfigurasi Nginx baru dan muat ulang web server:
```bash
sudo ln -s /etc/nginx/sites-available/profilku-ai /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

#### Langkah 4: Memasang Sertifikat SSL Let's Encrypt (HTTPS)
Amankan aplikasi web sekolah Anda menggunakan SSL gratis dari Let's Encrypt:
```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d nama-domain-sekolah.sch.id
```
Ikuti instruksi di layar, pilih opsi untuk mengalihkan (*redirect*) semua lalu lintas HTTP ke HTTPS secara otomatis.

---

## 🛠️ Pemecahan Masalah (Troubleshooting)

* **Error: `500 Internal Server Error` setelah deploy cPanel**
  * Periksa versi PHP hosting Anda. Pastikan versi PHP diset minimal PHP 8.2.
  * Pastikan file `.env` sudah ada dan konfigurasinya benar.
  * Periksa hak akses file: folder `storage` dan `bootstrap/cache` harus memiliki permission `775` atau `755` (bukan `777` di beberapa shared hosting karena masalah keamanan).

* **Aset CSS/JS (Vite) tidak muncul atau rusak (Blank Black Page)**
  * Jalankan perintah `npm run build` secara lokal sebelum mengompresi dan mengunggah berkas zip ke cPanel. File kompilasi aset berada di folder `public/build`. Pastikan folder `build` ikut terunggah di hosting.

* **Koneksi AI Gagal / Melantur**
  * Pastikan Anda sudah mengatur provider AI yang aktif di panel Admin > Pengaturan Sistem.
  * Lakukan **Tes Koneksi** di tab provider aktif untuk memverifikasi API Key dan model LLM yang diisi sudah terhubung dengan benar.

---

## 📄 Lisensi

Platform Profilku AI ini dilisensikan di bawah lisensi [MIT](https://opensource.org/licenses/MIT). Silakan kembangkan dan sesuaikan sesuai kebutuhan bimbingan konseling di sekolah Anda.
