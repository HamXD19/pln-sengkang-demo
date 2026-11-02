<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return \Inertia\Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nid' => 'required_without:email|nullable|string',
            'email' => 'required_without:nid|nullable|string',
            'password' => 'required|string',
        ], [
            'nid.required_without' => 'Nomor Induk Dokumen (NID) wajib diisi.',
            'email.required_without' => 'Nomor Induk Dokumen (NID) wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $input = trim((string) ($request->input('nid') ?? $request->input('email')));

        if (empty($input)) {
            return back()->withErrors([
                'nid' => 'Nomor Induk Dokumen (NID) wajib diisi.',
                'email' => 'Nomor Induk Dokumen (NID) wajib diisi.',
            ])->with('error', 'NID wajib diisi.');
        }

        $password = (string) $request->password;
        $remember = $request->boolean('remember');

        // Cari user berdasarkan NID (employee_no) case-insensitive, atau fallback email
        $user = \App\Models\User::whereRaw('UPPER(employee_no) = ?', [strtoupper($input)])
            ->orWhereRaw('LOWER(email) = ?', [strtolower($input)])
            ->first();

        if ($user && \Illuminate\Support\Facades\Hash::check($password, $user->password)) {
            if (!$user->is_active) {
                return back()->withErrors([
                    'nid' => 'Akun Anda dinonaktifkan. Silakan hubungi Administrator.',
                    'email' => 'Akun Anda dinonaktifkan. Silakan hubungi Administrator.',
                ])->with('error', 'Akun Anda dinonaktifkan. Silakan hubungi Administrator.');
            }

            Auth::login($user, $remember);
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))->with('just_logged_in', true);
        }

        return back()->withErrors([
            'nid' => 'NID atau kata sandi yang Anda masukkan tidak sesuai.',
            'email' => 'NID atau kata sandi yang Anda masukkan tidak sesuai.',
        ])->with('error', 'NID atau kata sandi yang Anda masukkan tidak sesuai.')->onlyInput('nid', 'email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
