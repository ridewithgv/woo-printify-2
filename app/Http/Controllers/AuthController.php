<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index(){
        return view('login');
    }

    public function login(Request $request)
    {
        // Validate the login request
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {

            return redirect()->intended('/horizon');
        }

        return redirect()->back()->withErrors(['error' => 'Invalid credentials']);
    }

    public function Logout(Request $request)
    {
        Auth::logout();

        return redirect('login');
    }
}
