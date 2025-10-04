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
    public function index() {
        return Hotel::all();
    }
    //
    // Liste tous les hôtels
    // public function index()
    // {
    //     $hotels = Hotel::with('user')->get();
    //     return response()->json($hotels);
    // }
    
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

     // Affiche un hôtel spécifique 
    public function show(Hotel $hotel)
    {
        $hotel->load('user');
        return response()->json($hotel);
    }
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
    public function update(Request $request, Hotel $hotel)
    {
        // Récupère l'utilisateur connecté via JWT
        $user = auth()->user();

        // Vérifie que l'utilisateur connecté est bien le propriétaire de l'hôtel
        if ($hotel->user_id !== $user->id) {
            return response()->json(['error' => 'Vous n\'êtes pas autorisé à modifier cet hôtel'], 403);
        }

        // Valide uniquement les champs envoyés
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'location' => 'sometimes|required|string|max:255',
            'rooms' => 'sometimes|required|integer|min:1',
            'price' => 'sometimes|required|numeric|min:0',
        ]);
        // dd( $validated);

        // Met à jour l'hôtel
        $hotel->update($validated);

        // Retourne l'hôtel mis à jour
        return response()->json([
            'message' => 'Hôtel mis à jour avec succès',
            'hotel' => $hotel
        ]);
    }
        
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
