<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Major;
use App\Models\User;
use Illuminate\Http\Request;

class AdminClassController extends Controller
{
    /**
     * Tampilkan Halaman Manajemen Kelas, Jurusan, & Plotting
     */
    public function index()
    {
        $classes = SchoolClass::with(['major', 'homeroomTeacher', 'students'])->orderBy('name')->get();
        $majors = Major::orderBy('name')->get();
        
        // Wali Kelas (role = wali_kelas)
        $homeroomTeachers = User::where('role', 'wali_kelas')->orderBy('name')->get();

        // Students (role = student)
        $students = User::where('role', 'student')->with('schoolClass')->orderBy('name')->get();

        return view('admin.classes.index', compact('classes', 'majors', 'homeroomTeachers', 'students'));
    }

    /**
     * Tambah Kelas Baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:classes,name',
            'major_id' => 'required|exists:majors,id',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
        ]);

        SchoolClass::create([
            'name' => $request->name,
            'major_id' => $request->major_id,
            'homeroom_teacher_id' => $request->homeroom_teacher_id ?: null,
        ]);

        return back()->with('success', 'Kelas baru berhasil ditambahkan.');
    }

    /**
     * Edit / Update Kelas
     */
    public function update(Request $request, $id)
    {
        $class = SchoolClass::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:classes,name,' . $id,
            'major_id' => 'required|exists:majors,id',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
        ]);

        $class->update([
            'name' => $request->name,
            'major_id' => $request->major_id,
            'homeroom_teacher_id' => $request->homeroom_teacher_id ?: null,
        ]);

        return back()->with('success', 'Data kelas berhasil diperbarui.');
    }

    /**
     * Hapus Kelas
     */
    public function destroy($id)
    {
        // Unplot semua siswa di kelas ini agar tidak mengalami constraint error
        User::where('class_id', $id)->update(['class_id' => null]);
        
        SchoolClass::destroy($id);
        return back()->with('success', 'Kelas berhasil dihapus.');
    }

    /**
     * Plotting Wali Kelas ke Kelas
     */
    public function plotHomeroom(Request $request, $classId)
    {
        $class = SchoolClass::findOrFail($classId);

        $request->validate([
            'homeroom_teacher_id' => 'nullable|exists:users,id',
        ]);

        $class->update([
            'homeroom_teacher_id' => $request->homeroom_teacher_id ?: null,
        ]);

        return back()->with('success', 'Plotting Wali Kelas ke kelas ' . $class->name . ' berhasil diperbarui.');
    }

    /**
     * Plotting Siswa ke Kelas (Plotting Kelas)
     */
    public function plotStudent(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'class_id' => 'nullable|exists:classes,id',
        ]);

        $student = User::where('role', 'student')->findOrFail($request->student_id);
        $student->update([
            'class_id' => $request->class_id ?: null,
        ]);

        return back()->with('success', 'Plotting kelas untuk murid ' . $student->name . ' berhasil diperbarui.');
    }

    /**
     * Tambah Jurusan Baru
     */
    public function storeMajor(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:majors,name',
            'code' => 'required|string|max:10|unique:majors,code',
        ]);

        Major::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
        ]);

        return back()->with('success', 'Jurusan baru berhasil ditambahkan.');
    }

    /**
     * Edit / Update Jurusan
     */
    public function updateMajor(Request $request, $id)
    {
        $major = Major::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:majors,name,' . $id,
            'code' => 'required|string|max:10|unique:majors,code,' . $id,
        ]);

        $major->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
        ]);

        return back()->with('success', 'Data jurusan berhasil diperbarui.');
    }

    /**
     * Hapus Jurusan (Hapus Kelas di dalamnya juga)
     */
    public function destroyMajor($id)
    {
        $major = Major::findOrFail($id);
        
        $classes = SchoolClass::where('major_id', $id)->get();
        foreach ($classes as $c) {
            User::where('class_id', $c->id)->update(['class_id' => null]);
            $c->delete();
        }

        $major->delete();
        return back()->with('success', 'Jurusan beserta kelas di dalamnya berhasil dihapus.');
    }
}
