<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;


class RegisterController extends Controller
{

    public function __invoke(Request $request)
    {
        /* 
        Validation
        */
        $request->validate([
            'name' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);

        /*
        Database Insert
        */
        $user = User::create([
            'name' => $request->name,
            'password' => Hash::make($request->password),
        ]);

    }
}
