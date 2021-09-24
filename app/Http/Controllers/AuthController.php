<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        return User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password'))
        ]);
    }

    public function login(Request $request)
    {
        if(!Auth::attempt($request->only('email', 'password'))){
            return response([
                'message' => "Credenciais Inválidas!",
                'status' => Response::HTTP_UNAUTHORIZED
            ]);
        }
        
        $user = Auth::user();
        $token = $user->createToken('token')->plainTextToken; 

        $cookie = cookie('jwt', $token, 60 * 24); // 1 day

        return response([
            'message' => 'Success',
            'token' => $token
        ])->withCookie($cookie);
    }

    public function user()
    { 
        $user = Auth::user();

        return $user;
    }


    public function logout()
    {
        $cookie = Cookie::forget('jwt');

        $user = Auth::user();
        
        $user->tokens()->delete();

        return response([
            'message' => 'Success'
        ])->withoutCookie($cookie);
    }


}
