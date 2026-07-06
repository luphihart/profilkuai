<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Tampilan Halaman Login
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectUserBasedOnRole(Auth::user());
        }
        return view('auth.login');
    }

    /**
     * Proses Login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return $this->redirectUserBasedOnRole(Auth::user());
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Tampilan Halaman Registrasi (Khusus Siswa)
     */
    public function showRegister()
    {
        $classes = SchoolClass::with(['major'])->get();
        return view('auth.register', compact('classes'));
    }

    /**
     * Proses Registrasi Siswa
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'class_id' => 'required|exists:classes,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student',
            'class_id' => $request->class_id,
        ]);

        Auth::login($user);

        return redirect()->route('student.dashboard');
    }

    /**
     * Logout Pengguna
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    /**
     * Redirect User ke Halaman Sesuai Peran
     */
    private function redirectUserBasedOnRole(User $user)
    {
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'guru_bk':
                return redirect()->route('bk.dashboard');
            case 'wali_kelas':
                return redirect()->route('wali.dashboard');
            case 'student':
            default:
                return redirect()->route('student.dashboard');
        }
    }
}
