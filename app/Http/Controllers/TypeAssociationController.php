<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TypeAssociation;
use App\Models\TypeAssociationTranslation;
use Astrotomic\Translatable\Locales;
class TypeAssociationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all type associations with their translations
        $typeAssociations = TypeAssociation::with('translations')->get();

        return response()->json([
            'success' => true,
            'message' => 'Type associations retrieved successfully',
            'type_associations' => $typeAssociations,
        ], 200);
    }

    /**
     * Store or update the resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming data
        $validatedData = $request->validate([
            'name_fr' => 'nullable|string',
            'name_en' => 'nullable|string',
            'name_ar' => 'nullable|string',
        ]);

        // Find the existing TypeAssociation if an ID is provided
        $typeAssociation = isset($validatedData['id']) ? TypeAssociation::find($validatedData['id']) : new TypeAssociation();

        // Define the languages and fields
        $languages = ['fr', 'en', 'ar'];
        $fields = ['name'];

        // Set translations dynamically
        foreach ($languages as $lang) {
            foreach ($fields as $field) {
                $fieldKey = "{$field}_{$lang}";
                $typeAssociation->translateOrNew($lang)->$field = $validatedData[$fieldKey] ?? null;
            }
        }

        // Save the type association
        $typeAssociation->save();

        return response()->json([
            'success' => true,
            'message' => $typeAssociation->wasRecentlyCreated ? 'Type association created successfully!' : 'Type association updated successfully',
            'data' => $typeAssociation
        ], $typeAssociation->wasRecentlyCreated ? 201 : 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $typeAssociation = TypeAssociation::with('translations')->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Type association retrieved successfully',
            'type_association' => $typeAssociation,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $typeAssociation = TypeAssociation::findOrFail($id);
        $typeAssociation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Type association deleted successfully',
        ], 200);
    }
}
