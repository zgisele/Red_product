<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hotel;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;


class HotelController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/hotels",
     *     summary="Liste des hôtels",
     *     tags={"Hotels"},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des hôtels",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="city", type="string")
     *             )
     *         )
     *     )
     * )
     */
    // public function index() {
    //     return Hotel::all();
    // }

    public function index()
    {
        $hotels = Hotel::where('user_id', Auth::id())->get();
        return response()->json($hotels);
    }
    //
    // Liste tous les hôtels
    // public function index()
    // {
    //     $hotels = Hotel::with('user')->get();
    //     return response()->json($hotels);
    // }
    

    /**
 * @OA\Post(
 *     path="/api/hotels",
 *     summary="Créer un nouvel hôtel",
 *     description="Crée un hôtel pour l'utilisateur connecté (JWT requis)",
 *     tags={"Hotels"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"name","location","rooms","price"},
 *                 @OA\Property(property="name", type="string", example="Hotel Luxe"),
 *                 @OA\Property(property="location", type="string", example="Dakar"),
 *                 @OA\Property(property="rooms", type="integer", example=20),
 *                 @OA\Property(property="price", type="number", format="float", example=100.50),
 *                 @OA\Property(property="image", type="file", description="Image de l'hôtel")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Hôtel créé avec succès"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Non authentifié"
 *     )
 * )
 */
    // Crée un nouvel hôtel pour l'utilisateur connecté
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'rooms' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // max 2MB
        ]);
        if ($request->hasFile('image')) {
         $path = $request->file('image')->store('hotels', 'public'); 
        $validated['image'] = $path;
        }

        $hotel = Auth::user()->hotels()->create($validated);

        return response()->json($hotel, 201);
    }



    /**
 * @OA\Get(
 *     path="/api/hotels/{id}",
 *     summary="Afficher un hôtel",
 *     description="Retourne les détails d’un hôtel (JWT requis)",
 *     tags={"Hotels"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de l'hôtel",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Détails de l'hôtel"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Hôtel non trouvé"
 *     )
 * )
 */
     // Affiche un hôtel spécifique 
    // public function show(Hotel $hotel)
    // {
    //     $hotel->load('user');
    //     return response()->json($hotel);
    // }

    // Affichage d'un hôtel de l'utilisateur connecté
    public function show($id)
    {
        $hotel = Hotel::where('user_id', Auth::id())->where('id', $id)->first();

        if (!$hotel) {
            return response()->json(['message' => 'Hôtel introuvable ou non autorisé'], 404);
        }

        return response()->json($hotel);
    }




    /**
 * @OA\Put(
 *     path="/api/hotels/{id}",
 *     summary="Mettre à jour un hôtel",
 *     description="Met à jour les informations d’un hôtel appartenant à l’utilisateur connecté (JWT requis)",
 *     tags={"Hotels"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de l'hôtel",
 *         @OA\Schema(type="integer" ,example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=false,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 @OA\Property(property="name", type="string", example="Hotel Royal"),
 *                 @OA\Property(property="location", type="string", example="Paris"),
 *                 @OA\Property(property="rooms", type="integer", example=15),
 *                 @OA\Property(property="price", type="number", format="float", example=200.00),
 *                 @OA\Property(property="image", type="file", description="Nouvelle image de l'hôtel")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Hôtel mis à jour avec succès"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Non autorisé"
 *     )
 * )
 */
    // Met à jour un hôtel
    //  public function update(Request $request, Hotel $hotel)
    // {

    //      // Récupère l'utilisateur connecté via JWT
    //       $user = auth()->user();
    //     // $this->authorize('update', $hotel); // optionnel si tu gères les permissions
    //     if ($hotel->user_id !== $user->id ) {
    //         return response()->json(['error' => 'Forbidden'], 403);
    //     }


    //     $validated = $request->validate([
    //         'name' => 'sometimes|required|string|max:255',
    //         'location' => 'sometimes|required|string|max:255',
    //         'rooms' => 'sometimes|required|integer|min:1',
    //         'price' => 'sometimes|required|numeric|min:0',
    //     ]);

    //     $hotel->update($validated);
        

    //     return response()->json($hotel);
    //     // return $user->id === $hotel->user_id; // l’utilisateur doit être le propriétaire
       
    // }
    // public function update(Request $request, Hotel $hotel)
    // {
    //     // Récupère l'utilisateur connecté via JWT
    //     $user = auth()->user();

    //     // Vérifie que l'utilisateur connecté est bien le propriétaire de l'hôtel
    //     if ($hotel->user_id !== $user->id) {
    //         return response()->json(['error' => 'Vous n\'êtes pas autorisé à modifier cet hôtel'], 403);
    //     }

    //     // Valide uniquement les champs envoyés
    //     $validated = $request->validate([
    //         'name' => 'sometimes|required|string|max:255',
    //         'location' => 'sometimes|required|string|max:255',
    //         'rooms' => 'sometimes|required|integer|min:1',
    //         'price' => 'sometimes|required|numeric|min:0',
    //          'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    //     ]);
    //     // dd( $validated);

    //     // Met à jour l'hôtel
    //     $hotel->update($validated);

    //     // Retourne l'hôtel mis à jour
    //     return response()->json([
    //         'message' => 'Hôtel mis à jour avec succès',
    //         'hotel' => $hotel//
    //     ]);
    // }
    public function update(Request $request, Hotel $hotel)
    {
        $user = auth()->user();

        // Vérifie que l'hôtel appartient à l'utilisateur connecté
        if ($hotel->user_id !== $user->id) {
            return response()->json(['error' => 'Vous n\'êtes pas autorisé à modifier cet hôtel'], 403);
        }

        // Valide uniquement les champs envoyés
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'location' => 'sometimes|required|string|max:255',
            'rooms' => 'sometimes|required|integer|min:1',
            'price' => 'sometimes|required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Gestion de l'image si présente
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('hotels', 'public'); 
            $validated['image'] = $path;
        }

        $hotel->update($validated);
        return response()->json([
            'message' => 'Hôtel mis à jour avec succès',
            'hotel' => $hotel
        ]);
    }


    


  
/**
 * @OA\Delete(
 *     path="/api/hotels/{id}",
 *     summary="Supprimer un hôtel",
 *     description="Supprime un hôtel appartenant à l’utilisateur connecté (JWT requis)",
 *     tags={"Hotels"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de l'hôtel",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Hôtel supprimé avec succès"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Non autorisé"
 *     )
 * )
 */

        
    public function destroy(Hotel $hotel)
    {
        // Récupère l'utilisateur connecté via JWT
        $user = auth()->user();

        // Vérifie que l'hôtel appartient à l'utilisateur connecté
        if ($hotel->user_id !== $user->id) {
            return response()->json(['error' => 'Vous n\'êtes pas autorisé à supprimer cet hôtel'], 403);
        }

        // Supprimer l'hôtel
        $hotel->delete();

        return response()->json(['message' => 'Hôtel supprimé avec succès']);
    }


}
