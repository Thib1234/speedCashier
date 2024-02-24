<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    public function login()
    {
        return view('auth.login');
    }

    public function doLogin(LoginRequest $request)
{
    $creditentials = $request->validated();

    if(Auth::attempt($creditentials)){
        $request->session()->regenerate();
        $user = Auth::user();
        // dd($user['name']);
        return redirect()->intended(route('cashier'))->with('user', $user);
    }

    return redirect()->route('auth.login')->withErrors([
        'name' => 'Nom invalide'
    ])->onlyInput('name');
}

    public function logout(){
        Auth::logout();
        return to_route('auth.login');
    }
    
}
