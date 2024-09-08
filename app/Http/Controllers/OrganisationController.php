<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organisation;
use App\Models\OrganisationTranslation;
use Illuminate\Support\Facades\Validator;

class OrganisationController extends Controller
{
    /**
     * Display a listing of the organisations.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Récupérer toutes les organisations avec leurs traductions
        $organisations = Organisation::with('translations')->get();

        // Retourner les organisations en JSON
        return response()->json($organisations);
    }

    /**
     * Store a newly created organisation in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate the main fields
        $validated = $request->validate([
            'date_creation' => 'required|date',
            'user_id' => 'required|exists:users,id',
        ]);

        // Créer une nouvelle organisation
        $organisation = Organisation::create($validated);

        // Retrieve available languages
        $locales = app(\Astrotomic\Translatable\Locales::class)->all();

        // Validate and store translations
        foreach ($locales as $locale) {
            $validator = Validator::make($request->all(), [
                "nom_$locale" => 'required|string|max:255',
                "description_$locale" => 'nullable|string',
                "adresse_reception_$locale" => 'nullable|string',
                "adresse_locale_$locale" => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            OrganisationTranslation::create([
                'locale' => $locale,
                'organisation_id' => $organisation->id,
                'nom' => $request->input("nom_$locale"),
                'description' => $request->input("description_$locale"),
                'adresse_reception' => $request->input("adresse_reception_$locale"),
                'adresse_locale' => $request->input("adresse_locale_$locale"),
            ]);
        }

        // Retourner l'organisation créée en JSON avec le code de statut 201
        return response()->json($organisation->load('translations'), 201);
    }

    /**
     * Display the specified organisation.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Trouver l'organisation par ID avec ses traductions
        $organisation = Organisation::with('translations')->find($id);

        if (!$organisation) {
            return response()->json(['message' => 'Organisation not found'], 404);
        }

        // Retourner l'organisation trouvée en JSON
        return response()->json($organisation);
    }

    /**
     * Update the specified organisation in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Trouver l'organisation par ID
        $organisation = Organisation::find($id);

        if (!$organisation) {
            return response()->json(['message' => 'Organisation not found'], 404);
        }

        // Validate the main fields
        $validated = $request->validate([
            'date_creation' => 'sometimes|date',
            'user_id' => 'sometimes|exists:users,id',
        ]);

        // Mettre à jour l'organisation
        $organisation->update($validated);

        // Retrieve available languages
        $locales = app(\Astrotomic\Translatable\Locales::class)->all();

        // Validate and update translations
        foreach ($locales as $locale) {
            $validator = Validator::make($request->all(), [
                "nom_$locale" => 'required|string|max:255',
                "description_$locale" => 'nullable|string',
                "adresse_reception_$locale" => 'nullable|string',
                "adresse_locale_$locale" => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            OrganisationTranslation::updateOrCreate(
                ['locale' => $locale, 'organisation_id' => $organisation->id],
                [
                    'nom' => $request->input("nom_$locale"),
                    'description' => $request->input("description_$locale"),
                    'adresse_reception' => $request->input("adresse_reception_$locale"),
                    'adresse_locale' => $request->input("adresse_locale_$locale"),
                ]
            );
        }

        // Retourner l'organisation mise à jour en JSON avec le code de statut 200
        return response()->json($organisation->load('translations'), 200);
    }

    /**
     * Remove the specified organisation from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Trouver l'organisation par ID
        $organisation = Organisation::find($id);

        if (!$organisation) {
            return response()->json(['message' => 'Organisation not found'], 404);
        }

        // Supprimer l'organisation
        $organisation->delete();

        // Retourner une réponse JSON avec un message de succès
        return response()->json(['message' => 'Organisation deleted successfully']);
    }
}
