<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hotel;
use Illuminate\Support\Facades\Auth;

class HotelController extends Controller
{
    //
    // Liste tous les hôtels
    public function index()
    {
        $hotels = Hotel::with('user')->get();
        return response()->json($hotels);
    }
    
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
     public function update(Request $request, Hotel $hotel)
    {
        $this->authorize('update', $hotel); // optionnel si tu gères les permissions

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'location' => 'sometimes|required|string|max:255',
            'rooms' => 'sometimes|required|integer|min:1',
            'price' => 'sometimes|required|numeric|min:0',
        ]);

        $hotel->update($validated);

        return response()->json($hotel);
    }
    // Supprime un hôtel
    public function destroy(Hotel $hotel)
    {
        $this->authorize('delete', $hotel); // optionnel si tu gères les permissions
        $hotel->delete();
        return response()->json(['message' => 'Hôtel supprimé']);
    }


}
