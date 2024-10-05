<?php

namespace App\Http\Controllers;

use App\Models\Evenement; // Importation du modèle Evenement
use Illuminate\Http\Request;

class EvenementController extends Controller
{
    /**
     * Store a newly created evenement in storage.
     */
    public function store(Request $request)
    {
        // Vérification si l'utilisateur est authentifié
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Récupération de l'ID de l'utilisateur authentifié
        $userId = auth()->id();

        // Validation des données
        $validatedData = $request->validate([
            'association_id' => 'required|exists:associations,id', // Validation pour association_id
            'event_date' => 'required|date',
            'capacity' => 'nullable|integer|min:0',
            'contact_email' => 'nullable|email',
            // Validation des traductions
            'title_fr' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description_fr' => 'nullable|string',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'location_fr' => 'nullable|string|max:255',
            'location_en' => 'nullable|string|max:255',
            'location_ar' => 'nullable|string|max:255',
        ]);

        // Création d'un nouvel evenement
        $evenement = new Evenement();
        $evenement->event_date = $validatedData['event_date'];
        $evenement->capacity = $validatedData['capacity'] ?? 0;
        $evenement->contact_email = $validatedData['contact_email'] ?? null;

        // Ajout des relations avec l'utilisateur et l'association
        $evenement->user_id = $userId; // Assigner l'ID de l'utilisateur authentifié
        $evenement->association_id = $validatedData['association_id']; // Assigner l'ID de l'association

        // Gestion des traductions pour les champs multilingues
        $languages = ['fr', 'en', 'ar'];
        $fields = ['title', 'description', 'location'];

        foreach ($languages as $lang) {
            foreach ($fields as $field) {
                $fieldKey = "{$field}_{$lang}";
                if (isset($validatedData[$fieldKey])) {
                    // Utilisation de la méthode `translateOrNew` pour les traductions
                    $evenement->translateOrNew($lang)->$field = $validatedData[$fieldKey];
                }
            }
        }

        // Sauvegarde de l'evenement et de ses traductions
        try {
            $evenement->save();
            return response()->json([
                'message' => 'Événement ajouté avec succès!',
                'evenement' => $evenement,
            ], 201); // 201 Created
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de l\'ajout de l\'événement.',
                'error' => $e->getMessage(),
            ], 500); // 500 Internal Server Error
        }
    }

    /**
     * Display a listing of the events.
     */
    public function index()
    {
        // Récupération de tous les événements avec leurs traductions
        $evenements = Evenement::with('translations')->get();

        return response()->json([
            'evenements' => $evenements,
        ]);
    }
}
