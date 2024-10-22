<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Association;
use App\Models\AssociationTranslation;
use App\Models\User;

class AssociationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Récupérer l'utilisateur connecté
        $users_id = auth()->id();

        if (!$users_id) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to view associations.',
            ], 401);
        }

        // Récupérer l'association de l'utilisateur connecté
        $association = Association::with('translations')
            ->where('users_id', $users_id)
            ->first();

        if (!$association) {
            return response()->json([
                'success' => false,
                'message' => 'No association found for this administrator.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Association retrieved successfully',
            'association' => $association,
        ], 200);
    }

    /**
     * Store or update the resource in storage.
     */
    public function store(Request $request)
    {
        $users_id = auth()->id();

        // Vérifiez si l'ID de l'administrateur est fourni
        if (!$users_id) {
            return response()->json([
                'success' => false,
                'message' => 'Administrator ID is required to create an association.',
            ], 400);
        }

        // Chercher l'association existante pour cet utilisateur
        $association = Association::where('users_id', $users_id)->first();

        // Si l'association n'existe pas, on est en mode création, sinon c'est une mise à jour
        $emailRule = 'required|email|unique:associations,email';
        if ($association) {
            // Si une association existe, ignorer l'email actuel dans la vérification de l'unicité
            $emailRule = 'required|email|unique:associations,email,' . $association->id;
        }

        // Valider les données de la requête
        $validatedData = $request->validate([
            'type_association_id' => 'required|exists:type_associations,id',
            'phone' => 'nullable|string',
            'phone_fax' => 'nullable|string',
            'rip' => 'nullable|string',
            'email' => $emailRule, // Utiliser la règle dynamique pour l'email
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

        // Si l'association n'existe pas, créer une nouvelle
        if (!$association) {
            $association = new Association();
        }

        // Affectation des données
        $association->type_association_id = $validatedData['type_association_id'];
        $association->phone = $validatedData['phone'];
        $association->phone_fax = $validatedData['phone_fax'];
        $association->rip = $validatedData['rip'];
        $association->email = $validatedData['email'];
        $association->users_id = $users_id; // Utiliser l'ID de l'administrateur

        // Gérer les traductions
        $languages = ['fr', 'en', 'ar'];
        $fields = ['adresse', 'adresse_reception', 'name', 'description'];

        foreach ($languages as $lang) {
            $nameKey = "name_{$lang}";

            // Vérifier les doublons dans la même locale et l'association
            $existingTranslation = AssociationTranslation::where('locale', $lang)
                ->where('name', $validatedData[$nameKey])
                ->where('association_id', '!=', $association->id) // Ignorer l'association actuelle lors de la mise à jour
                ->first();

            if ($existingTranslation) {
                return response()->json([
                    'success' => false,
                    'message' => "The name '{$validatedData[$nameKey]}' already exists for the '{$lang}' locale."
                ], 422);
            }

            // Remplissage des champs traduits après vérification des doublons
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
        $association = Association::with('translations')->where('users_id', auth()->id())->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Association retrieved successfully',
            'association' => $association,
        ], 200);
    }

    /**
 * Filter associations by type_association_id.
 */
public function filterByType(Request $request)
{
    // Valider que le type d'association est bien fourni et qu'il existe
    $validatedData = $request->validate([
        'type_association_id' => 'required|exists:type_associations,id',
    ]);

    // Récupérer les associations qui correspondent au type fourni
    $associations = Association::with('translations')
        ->where('type_association_id', $validatedData['type_association_id'])
        ->get();

    // Vérifier s'il y a des associations qui correspondent
    if ($associations->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No associations found for this type.',
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => 'Associations filtered by type retrieved successfully',
        'associations' => $associations,
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
