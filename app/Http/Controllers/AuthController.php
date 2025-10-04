<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //
    //  Inscription
    public function register(Request $request)
    {
       $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // max 2MB
            'password' => 'required|string|min:6|confirmed',
            
        ]);

        //     $user = User::create([
        //         'name' =>$validated['name'] , 
        //         'email' =>$validated['email'] ,
        //         'password' => Hash::make($validated['password']),
        //     ]);
        //     if ($request->hasFile('image')) {
        //      $path = $request->file('image')->store('users', 'public'); 
        //     $validated['image'] = $path;
        //     }
        //     // $token = auth()->login($user);
        //         Auth::login($user);
        //     // return $this->respondWithToken($token);
        //     return response()->json([
        //         'message'=>'Utilisateur cree avec succes',
        //         'user'=>$user,
        //         // 'token'=> $token
        //     ]);
        
        // }
        $user = User::create ([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('users', 'public'); 
            $user->image = $path;
            $user->save();
        }
        
        return response()->json([
            'message' => 'Utilisateur créé avec succès',
            'user'    => $user
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'deconnexion reussi']);
    }

    // Rafraîchir le token
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}
