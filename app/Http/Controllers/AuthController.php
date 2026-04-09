<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = [
            'email'    => $request->input('login'),
            'password' => $request->input('password'),
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            if ($user->role === 'Administrateur') {
                return redirect()->route('admin.index');
            }
            return redirect()->route('dashboard');
        }

        return redirect()->route('login')
                         ->with('error', 'Email ou mot de passe incorrect.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        User::create([
            'name'     => $request->input('prenom') . ' ' . $request->input('nom'),
            'email'    => $request->input('email'),
            'password' => $request->input('password'), // User model casts password to hashed
            'role'     => 'Client',
        ]);

        return redirect()->route('login')
                         ->with('success', 'Compte créé, vous pouvez vous connecter.');
    }
}

