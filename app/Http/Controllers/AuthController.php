<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $email    = $request->input('login');
        $password = $request->input('password');

        $user = DB::table('users')
                  ->where('email', $email)
                  ->first();

        if ($user && Hash::check($password, $user->password)) {
            session(['user' => $user]);
            return redirect()->route('admin.index');
        }

        return redirect()->route('login')
                         ->with('error', 'Email ou mot de passe incorrect.');
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        DB::table('users')->insert([
            'name'     => $request->input('prenom') . ' ' . $request->input('nom'),
            'email'    => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role'     => 'Client',
        ]);

        return redirect()->route('login')
                         ->with('success', 'Compte créé, vous pouvez vous connecter.');
    }
}
