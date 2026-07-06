<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'class_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // --- Relasi ---

    /**
     * Kelas siswa (hanya untuk role student)
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Kelas yang diampu (hanya untuk wali_kelas)
     */
    public function managedClass(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'homeroom_teacher_id');
    }

    /**
     * Percakapan siswa (hanya untuk student)
     */
    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'student_id');
    }

    /**
     * Memori ingatan siswa (hanya untuk student)
     */
    public function memories(): HasMany
    {
        return $this->hasMany(StudentMemory::class, 'student_id');
    }

    /**
     * Bukti-bukti profiling siswa (hanya untuk student)
     */
    public function evidence(): HasMany
    {
        return $this->hasMany(Evidence::class, 'student_id');
    }

    /**
     * Skor keyakinan domain siswa (hanya untuk student)
     */
    public function confidenceScores(): HasMany
    {
        return $this->hasMany(ConfidenceScore::class, 'student_id');
    }

    /**
     * Laporan profiling siswa (hanya untuk student)
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'student_id');
    }

    /**
     * Catatan konseling dari Guru BK / Wali Kelas (siswa sebagai subjek)
     */
    public function studentNotes(): HasMany
    {
        return $this->hasMany(TeacherNote::class, 'student_id');
    }

    /**
     * Catatan konseling yang ditulis (guru sebagai penulis)
     */
    public function authoredNotes(): HasMany
    {
        return $this->hasMany(TeacherNote::class, 'teacher_id');
    }

    // --- Helper Roles ---

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isGuruBk(): bool
    {
        return $this->role === 'guru_bk';
    }

    public function isWaliKelas(): bool
    {
        return $this->role === 'wali_kelas';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Reset seluruh data sesi percakapan bimbingan murid (riwayat obrolan, skor keyakinan, bukti profiling, memori AI, & laporan)
     */
    public function resetSessionData()
    {
        if (!$this->isStudent()) {
            return;
        }

        // Hapus bukti profiling
        $this->evidence()->delete();

        // Hapus skor keyakinan
        $this->confidenceScores()->delete();

        // Hapus memori AI
        $this->memories()->delete();

        // Hapus laporan profiling akhir
        $this->reports()->delete();

        // Hapus seluruh percakapan (ini otomatis menghapus riwayat obrolan/pesan karena cascade delete)
        $this->conversations()->delete();
    }
}
