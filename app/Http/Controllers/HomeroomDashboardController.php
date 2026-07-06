<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\User;
use App\Models\Conversation;
use App\Models\StudentMemory;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeroomDashboardController extends Controller
{
    /**
     * Halaman Utama Wali Kelas
     */
    public function index()
    {
        $teacher = Auth::user();

        // 1. Cari kelas yang diampu oleh wali kelas ini
        $class = SchoolClass::where('homeroom_teacher_id', $teacher->id)->with('major')->first();

        if (!$class) {
            return view('wali.dashboard', [
                'class' => null,
                'students' => collect(),
                'classStats' => []
            ]);
        }

        // 2. Ambil daftar siswa di kelas tersebut
        $students = User::where('class_id', $class->id)
            ->where('role', 'student')
            ->get();

        // 3. Gabungkan info percakapan & memori untuk setiap siswa
        $studentData = [];
        $completedCount = 0;
        $activeCount = 0;

        foreach ($students as $student) {
            $conversation = Conversation::where('student_id', $student->id)
                ->where('status', 'active')
                ->first();

            $latestConversation = Conversation::where('student_id', $student->id)->latest()->first();

            $stage = $latestConversation ? $latestConversation->current_stage : 0;
            $status = $latestConversation ? $latestConversation->status : 'inactive';

            if ($status === 'completed') {
                $completedCount++;
            } elseif ($status === 'active') {
                $activeCount++;
            }

            // Ambil cita-cita karir dan hobi dari memori
            $memories = StudentMemory::where('student_id', $student->id)->get()->pluck('value', 'key')->toArray();
            $careerGoal = $memories['career_goals'] ?? 'Belum teridentifikasi';
            $hobby = $memories['hobbies'] ?? 'Belum diceritakan';

            $report = Report::where('student_id', $student->id)->latest()->first();

            $studentData[] = [
                'student' => $student,
                'stage' => $stage,
                'status' => $status,
                'career_goal' => $careerGoal,
                'hobby' => $hobby,
                'report' => $report
            ];
        }

        // 4. Hitung statistik kelas
        $classStats = [
            'total' => $students->count(),
            'active' => $activeCount,
            'completed' => $completedCount,
            'not_started' => $students->count() - ($activeCount + $completedCount)
        ];

        return view('wali.dashboard', compact('class', 'studentData', 'classStats'));
    }

    /**
     * Lihat laporan profiling siswa kelasnya
     */
    public function viewStudentReport($id)
    {
        $teacher = Auth::user();
        $class = SchoolClass::where('homeroom_teacher_id', $teacher->id)->first();

        if (!$class) {
            return redirect()->route('wali.dashboard')->with('error', 'Anda tidak mengampu kelas mana pun.');
        }

        // Pastikan siswa yang dilihat memang benar berada di kelas wali kelas ini (Security check)
        $student = User::where('id', $id)
            ->where('class_id', $class->id)
            ->where('role', 'student')
            ->firstOrFail();

        $latestReport = Report::where('student_id', $student->id)->latest()->first();

        if (!$latestReport) {
            return back()->with('error', 'Laporan profiling belum dibuat untuk siswa ini. Harap minta siswa menyelesaikan obrolan dengan AI.');
        }

        return view('wali.student-report', compact('student', 'latestReport'));
    }
}
