<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Association;
use App\Models\AssociationTranslation;

class AssociationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Récupérer les paramètres de filtre (nom et type_association_id)
        $name = $request->input('name');
        $type_association_id = $request->input('type_association_id');

        // Construire la requête de base avec les traductions
        $query = Association::with('translations');

        // Appliquer le filtre par nom s'il est fourni
        if ($name) {
            $query->whereHas('translations', function ($q) use ($name) {
                $q->where('name', 'like', "%{$name}%");
            });
        }

        // Appliquer le filtre par type_association_id s'il est fourni
        if ($type_association_id) {
            $query->where('type_association_id', $type_association_id);
        }

        // Exécuter la requête et récupérer les associations filtrées
        $associations = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Associations retrieved successfully',
            'associations' => $associations,
        ], 200);
    }

    // Les autres méthodes restent inchangées (store, show, destroy)
    // public function index()
    // {
    //     // Fetch all associations with their translations
    //     $associations = Association::with('translations')->get();

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Associations retrieved successfully',
    //         'associations' => $associations,
    //     ], 200);
    // }

    /**
     * Store or update the resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'type_association_id' => 'required|exists:type_associations,id',
            'phone' => 'nullable|string',
            'phone_fax' => 'nullable|string',
            'rip' => 'nullable|string',
            'email' => 'required|email|unique:associations,email',
            'adresse_fr' => 'nullable|string',
            'adresse_en' => 'nullable|string',
            'adresse_ar' => 'nullable|string',
            'adresse_reception_fr' => 'nullable|string',
            'adresse_reception_en' => 'nullable|string',
            'adresse_reception_ar' => 'nullable|string',
            'name_fr' => 'required|string',
            'name_en' => 'required|string',
            'name_ar' => 'required|string',
            'description_fr' => 'nullable|string',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
        ]);

        $association = isset($validatedData['id']) ? Association::find($validatedData['id']) : new Association();
        $association->type_association_id = $validatedData['type_association_id'];
        $association->phone = $validatedData['phone'];
        $association->phone_fax = $validatedData['phone_fax'];
        $association->rip = $validatedData['rip'];
        $association->email = $validatedData['email'];

        $languages = ['fr', 'en', 'ar'];
        $fields = ['adresse', 'adresse_reception', 'name', 'description'];

        foreach ($languages as $lang) {
            $nameKey = "name_{$lang}";

            // Check for duplicates within the same locale and association
            $existingTranslation = AssociationTranslation::where('locale', $lang)
                ->where('name', $validatedData[$nameKey])
                ->where('association_id', '!=', $association->id) // Ignore the current association in case of an update
                ->first();

            if ($existingTranslation) {
                return response()->json([
                    'success' => false,
                    'message' => "The name '{$validatedData[$nameKey]}' already exists for the '{$lang}' locale."
                ], 422);
            }

            // Set the translated fields after checking for duplicates
            foreach ($fields as $field) {
                $fieldKey = "{$field}_{$lang}";
                $association->translateOrNew($lang)->$field = $validatedData[$fieldKey] ?? null;
            }
        }

        $association->save();

        return response()->json([
            'success' => true,
            'message' => $association->wasRecentlyCreated ? 'Association created successfully!' : 'Association updated successfully',
            'data' => $association
        ], $association->wasRecentlyCreated ? 201 : 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $association = Association::with('translations')->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Association retrieved successfully',
            'association' => $association,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $association = Association::findOrFail($id);
        $association->delete();

        return response()->json([
            'success' => true,
            'message' => 'Association deleted successfully',
        ], 200);
    }
}
