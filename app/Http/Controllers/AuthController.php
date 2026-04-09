<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function loginForm() {
        return view('login');
    }

    public function login(Request $r) {
        if($r->username == 'admin' && $r->password == '123') {
            session(['user' => 'admin']);
            return redirect('/regions');
        }
        return back()->with('error','Login salah');
    }

    public function logout() {
        session()->forget('user');
        return redirect('/');
    }
}
