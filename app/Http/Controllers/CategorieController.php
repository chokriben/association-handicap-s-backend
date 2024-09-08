<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categorie;
use App\Models\CategorieTranslation; // Import the translation model

class CategorieController extends Controller
{
    /**
     * Display a listing of the categories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Récupérer toutes les catégories avec leurs traductions
        $categories = Categorie::with('translations')->get();

        // Retourner les catégories en JSON
        return response()->json($categories);
    }

    /**
     * Store a newly created category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Valider les données de la catégorie
        $validated = $request->validate([
            'translations' => 'required|array',
            'translations.*.locale' => 'required|string|max:5',
            'translations.*.name' => 'required|string|max:255',
        ]);

        // Créer une nouvelle catégorie
        $categorie = Categorie::create();

        // Sauvegarder les traductions
        foreach ($validated['translations'] as $translation) {
            CategorieTranslation::create([
                'categorie_id' => $categorie->id,
                'locale' => $translation['locale'],
                'name' => $translation['name'],
            ]);
        }

        // Retourner la catégorie créée en JSON avec le code de statut 201
        return response()->json($categorie->load('translations'), 201);
    }

    /**
     * Display the specified category.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Trouver la catégorie par ID avec ses traductions
        $categorie = Categorie::with('translations')->find($id);

        if (!$categorie) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        // Retourner la catégorie trouvée en JSON
        return response()->json($categorie);
    }

    /**
     * Update the specified category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Trouver la catégorie par ID
        $categorie = Categorie::find($id);

        if (!$categorie) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        // Valider les données de la catégorie
        $validated = $request->validate([
            'translations' => 'required|array',
            'translations.*.locale' => 'required|string|max:5',
            'translations.*.name' => 'required|string|max:255',
        ]);

        // Mettre à jour les traductions existantes ou en ajouter de nouvelles
        foreach ($validated['translations'] as $translation) {
            CategorieTranslation::updateOrCreate(
                ['categorie_id' => $categorie->id, 'locale' => $translation['locale']],
                ['name' => $translation['name']]
            );
        }

        // Retourner la catégorie mise à jour en JSON avec le code de statut 200
        return response()->json($categorie->load('translations'), 200);
    }

    /**
     * Remove the specified category from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Trouver la catégorie par ID
        $categorie = Categorie::find($id);

        if (!$categorie) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        // Supprimer les traductions associées
        $categorie->translations()->delete();

        // Supprimer la catégorie
        $categorie->delete();

        // Retourner une réponse JSON avec un message de succès
        return response()->json(['message' => 'Category deleted successfully']);
    }
}
