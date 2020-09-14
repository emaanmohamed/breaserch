<?php

namespace App\Http\Controllers\Admin\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function viewLogin()
    {
        return view('admin.auth.login');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
