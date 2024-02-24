<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cookie;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('name', 'password');

        // Tentative de connexion de l'utilisateur
        // Dans votre contrôleur LoginController
        if (Auth::attempt($credentials)) {
            // Authentification réussie, retourner l'utilisateur authentifié
            $user = Auth::user();
            // Créer un cookie d'authentification
            // Cookie::queue('user_authenticated', "testtezt", 60);
            // Renvoyer la réponse JSON avec l'utilisateur authentifié
            return response()->json(['user' => $user])->withCookie('user_authenticated', "3", 1000);
        }

        // Échec de l'authentification, retourner une réponse avec le code 401
        return new JsonResponse(['error' => 'Identifiants invalides'], 401);
    }
}
