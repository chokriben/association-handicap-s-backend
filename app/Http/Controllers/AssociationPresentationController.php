<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssociationPresentation;

class AssociationPresentationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all association presentations with their translations
        $associationPresentations = AssociationPresentation::with('translations')->get();

        return response()->json([
            'success' => true,
            'message' => 'Sélection effectuée avec succès',
            'association_presentations' => $associationPresentations,
        ], 200);
    }

    /**
     * Store or update the resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming data
        $validatedData = $request->validate([
            'id' => 'nullable|integer|exists:association_presentations,id', // Allow optional id for update
            'de_nous_fr' => 'nullable|string',
            'notre_vision_fr' => 'nullable|string',
            'notre_message_fr' => 'nullable|string',
            'nos_objectifs_fr' => 'nullable|string',
            'de_nouvelles_valeurs_fr' => 'nullable|string',
            'de_nous_en' => 'nullable|string',
            'notre_vision_en' => 'nullable|string',
            'notre_message_en' => 'nullable|string',
            'nos_objectifs_en' => 'nullable|string',
            'de_nouvelles_valeurs_en' => 'nullable|string',
            'de_nous_ar' => 'nullable|string',
            'notre_vision_ar' => 'nullable|string',
            'notre_message_ar' => 'nullable|string',
            'nos_objectifs_ar' => 'nullable|string',
            'de_nouvelles_valeurs_ar' => 'nullable|string',
        ]);

        // Find the existing AssociationPresentation if an ID is provided
        $associationPresentation = isset($validatedData['id']) ? AssociationPresentation::find($validatedData['id']) : new AssociationPresentation();

        // Define the languages and fields
        $languages = ['fr', 'en', 'ar'];
        $fields = [
            'de_nous',
            'notre_vision',
            'notre_message',
            'nos_objectifs',
            'de_nouvelles_valeurs'
        ];

        // Set translations dynamically
        foreach ($languages as $lang) {
            foreach ($fields as $field) {
                $fieldKey = "{$field}_{$lang}";
                $associationPresentation->translateOrNew($lang)->$field = $validatedData[$fieldKey] ?? null;
            }
        }

        // Save the association presentation
        $associationPresentation->save();

        return response()->json([
            'success' => true,
            'message' => $associationPresentation->wasRecentlyCreated ? 'Association présentation créée avec succès!' : 'Modification effectuée avec succès',
            'data' => $associationPresentation
        ], $associationPresentation->wasRecentlyCreated ? 201 : 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $associationPresentation = AssociationPresentation::with('translations')->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Sélection effectuée avec succès',
            'association_presentation' => $associationPresentation,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        AssociationPresentation::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Suppression effectuée avec succès',
        ], 200);
    }
}
