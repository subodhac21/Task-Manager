<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function __construct(){
        if(Auth::check()){
             redirect()->intended("/dashboard");
        }
    }
    
    public function showLogin(){
        return view("auth.login");
    }

    public function showRegister(){
        return view("auth.register");
    }


    public function login(LoginRequest $request){



        $credentials = $request->only("email","password");

        if(!Auth::attempt($credentials)){
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->withInput();
        }
        $request->session()->regenerate();
        return redirect()->intended("/dashboard")->with("success", "Login successful");



    }

    public function register(RegisterRequest $request)
{
    $credentials = $request->only("email", "password", "name");

    $credentials['password'] = Hash::make($credentials['password']);

    $user = User::create($credentials);

    // Log the user in
    Auth::login($user);

    return redirect()->intended('/dashboard')->with("success", "Registration successful and logged in!");
}
}
