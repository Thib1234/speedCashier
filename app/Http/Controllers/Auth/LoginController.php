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

        //tenter de créer une fonction pour protegere la connexion.
        $date = now('GMT+1'); // ok affiche l'heure au bon fuseau horaire

        $date->toTimeString(); // ne fonctionne pas
        $credentials = $request->validated();

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Créer un jeton d'authentification pour cet utilisateur
            $token = $user->createToken('Token Name')->plainTextToken;

            // Rediriger vers la page souhaitée avec le jeton
            return redirect()->intended(route('cashier'))->with('user', $user)->with('token', $token);
        }

        // dd('il est ' . $date);
        return redirect()->route('index')->withErrors([
            'name' => 'erreur dans le nom'
        ])->onlyInput('name');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('index');
    }
}
