<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{


        /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Inscription d'un utilisateur",
     *     description="Crée un nouvel utilisateur",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
    *             mediaType="multipart/form-data",
        *             @OA\Schema(
        *                 required={"name","email","password","password_confirmation"},
        *                 @OA\Property(property="name", type="string", example="Alice"),
        *                 @OA\Property(property="email", type="string", format="email", example="alice@example.com"),
        *                 @OA\Property(property="password", type="string", format="password", example="secret123"),
        *                 @OA\Property(property="password_confirmation", type="string", format="password", example="secret123"),
        *                 @OA\Property(property="image", type="file", description="Image de profil")
        *             )
 *              )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Utilisateur créé avec succès"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation des données échouée"
     *     )
     * )
     */
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



        /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Connexion utilisateur",
     *     description="Authentifie l'utilisateur et retourne un token JWT",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="alice@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Connexion réussie, retourne le token"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }



        /**
     * @OA\Get(
     *     path="/api/me",
     *     summary="Profil utilisateur",
     *     description="Retourne les informations de l'utilisateur connecté",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Informations de l'utilisateur"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     )
     * )
     */

    public function me()
    {
        return response()->json(auth()->user());
    }




        /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Déconnexion utilisateur",
     *     description="Invalidate le token JWT de l'utilisateur connecté",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Déconnexion réussie"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     )
     * )
     */

    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'deconnexion reussi']);
    }


        /**
     * @OA\Post(
     *     path="/api/refresh",
     *     summary="Rafraîchir le token JWT",
     *     description="Génère un nouveau token JWT pour l'utilisateur connecté",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Nouveau token généré"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     )
     * )
     */

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
