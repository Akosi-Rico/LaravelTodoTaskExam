<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\Register;
use App\Models\User;
class LoginController extends Controller
{
    public function index()
    {
        return view("auth.login");
    }

    public function create()
    {
        return view("auth.register");
    }

    public function store(Register $request)
    {
        return User::registerNewUser(request()->payload);
    }

    public function login(LoginRequest $request)
    {
        return User::login(request()->payload);
    }

    public function logout()
    {
        auth()->guard('web')->logout();

        request()->session()->invalidate();

        request()->session()->regenerateToken();
    }
}
