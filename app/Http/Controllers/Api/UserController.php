<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function index(){
        $users = User::get();
        return response()->json([
            'message'=>"Lista de usuarios.",
            'users'=>$users
        ], Response::HTTP_OK);
    }

    public function register (Request $request){
        $request->validate([
            'name'=>'required|string|max:50',
            'email'=>'required|unique:users,email|email',
            'password'=>'required|confirmed|max:50|string',
        ]);

        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=> Hash::make($request->password)
        ]);

        $token = $user->createToken('token')->plainTextToken;

        return response()->json([
            'message'=>'Usuario creado.',
            'user'=>$user,
            'token'=> $token
        ],Response::HTTP_CREATED);
    }

    public function login(Request $request){
        $request->validate([
            'email'=>'required|exists:users,email|email',
            'password'=>'required|max:50|string',
        ]);

        $user = User::where('email',$request->email)->first();
        
        if(!Hash::check($request->password,$user->password)){
            return response()->json([
                'message'=>'Contrasena incorrecta',
            ],Response::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken('token')->plainTextToken;

        return response()->json([
            'message'=>"Se inicio sesion correctamente.",
            'user'=>$user,
            'token'=>$token
        ]);
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message'=>'Sesion cerrada.'    
        ], Response::HTTP_OK);
    }
}