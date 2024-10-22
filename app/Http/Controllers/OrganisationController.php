<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organisation;
use App\Models\OrganisationTranslation;
use App\Models\User;

class OrganisationController extends Controller
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
                'message' => 'You must be logged in to view Organisations.',
            ], 401);
        }

        // Récupérer l'Organisation de l'utilisateur connecté
        $organisation = Organisation::with('translations')
            ->where('users_id', $users_id)
            ->first();

        if (!$organisation) {
            return response()->json([
                'success' => false,
                'message' => 'No Organisation found for this administrator.',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Organisation retrieved successfully',
            'Organisation' => $organisation,
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
                'message' => 'Administrator ID is required to create an Organisation.',
            ], 400);
        }

        // Chercher l'Organisation existante pour cet utilisateur
        $organisation = Organisation::where('users_id', $users_id)->first();

        // Si l'Organisation n'existe pas, on est en mode création, sinon c'est une mise à jour
        $emailRule = 'required|email|unique:Organisations,email';
        if ($organisation) {
            // Si une Organisation existe, ignorer l'email actuel dans la vérification de l'unicité
            $emailRule = 'required|email|unique:Organisations,email,' . $organisation->id;
        }

        // Valider les données de la requête
        $validatedData = $request->validate([
            'type_organisation_id' => 'required|exists:type_organisations,id',
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

            'category_fr' => 'nullable|string',
            'category_en' => 'nullable|string',
            'category_ar' => 'nullable|string',
        ]);

        // Si l'Organisation n'existe pas, créer une nouvelle
        if (!$organisation) {
            $organisation = new Organisation();
        }

        // Affectation des données
        $organisation->type_organisation_id = $validatedData['type_organisation_id'];
        $organisation->phone = $validatedData['phone'];
        $organisation->phone_fax = $validatedData['phone_fax'];
        $organisation->rip = $validatedData['rip'];
        $organisation->email = $validatedData['email'];
        $organisation->users_id = $users_id; // Utiliser l'ID de l'administrateur

        // Gérer les traductions
        $languages = ['fr', 'en', 'ar'];
        $fields = ['adresse', 'adresse_reception', 'name', 'description','category'];

        foreach ($languages as $lang) {
            $nameKey = "name_{$lang}";

            // Vérifier les doublons dans la même locale et l'Organisation
            $existingTranslation = OrganisationTranslation::where('locale', $lang)
                ->where('name', $validatedData[$nameKey])
                ->where('organisation_id', '!=', $organisation->id) // Ignorer l'Organisation actuelle lors de la mise à jour
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
                $organisation->translateOrNew($lang)->$field = $validatedData[$fieldKey] ?? null;
            }
        }

        $organisation->save();

        return response()->json([
            'success' => true,
            'message' => $organisation->wasRecentlyCreated ? 'Organisation created successfully!' : 'Organisation updated successfully',
            'data' => $organisation
        ], $organisation->wasRecentlyCreated ? 201 : 200);
    }



    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $organisation = Organisation::with('translations')->where('users_id', auth()->id())->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Organisation retrieved successfully',
            'Organisation' => $organisation,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $organisation = Organisation::findOrFail($id);
        $organisation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Organisation deleted successfully',
        ], 200);
    }
}
