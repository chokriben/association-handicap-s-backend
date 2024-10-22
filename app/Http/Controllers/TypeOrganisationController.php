<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TypeOrganisation;
use App\Models\TypeOrganisationTranslation;
use Astrotomic\Translatable\Locales;
class TypeOrganisationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all type associations with their translations
        $typeOrganisations = TypeOrganisation::with('translations')->get();

        return response()->json([
            'success' => true,
            'message' => 'Type organisation retrieved successfully',
            'type_organisation' => $typeOrganisations,
        ], 200);
    }
    public function indexAll()
    {
        // Fetch all type associations with their translations
        $typeOrganisations = TypeOrganisation::with('translations')->get();

        // Format the data to include translations properly
        $formattedTypeOrganisations = $typeOrganisations->map(function ($TypeOrganisation) {
            return [
                'id' => $TypeOrganisation->id,
                'name_fr' => $TypeOrganisation->translate('fr')->name ?? null,
                'name_en' => $TypeOrganisation->translate('en')->name ?? null,
                'name_ar' => $TypeOrganisation->translate('ar')->name ?? null,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Type organisation retrieved successfully',
            'type_organisation' => $formattedTypeOrganisations,
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

        // Find the existing TypeOrganisation if an ID is provided
        $typeOrganisation = isset($validatedData['id']) ? TypeOrganisation::find($validatedData['id']) : new TypeOrganisation();

        // Define the languages and fields
        $languages = ['fr', 'en', 'ar'];
        $fields = ['name'];

        // Set translations dynamically
        foreach ($languages as $lang) {
            foreach ($fields as $field) {
                $fieldKey = "{$field}_{$lang}";
                $typeOrganisation->translateOrNew($lang)->$field = $validatedData[$fieldKey] ?? null;
            }
        }

        // Save the type association
        $typeOrganisation->save();

        return response()->json([
            'success' => true,
            'message' => $typeOrganisation->wasRecentlyCreated ? 'Type association created successfully!' : 'Type association updated successfully',
            'data' => $typeOrganisation
        ], $typeOrganisation->wasRecentlyCreated ? 201 : 200);
    }
   public function update(Request $request, $id)
    {
        // Validate incoming data
        $validatedData = $request->validate([
            'name_fr' => 'nullable|string',
            'name_en' => 'nullable|string',
            'name_ar' => 'nullable|string',
        ]);

        // Find the existing TypeOrganisation
        $typeOrganisation = TypeOrganisation::findOrFail($id);

        // Define the languages and fields
        $languages = ['fr', 'en', 'ar'];
        $fields = ['name'];

        // Set translations dynamically
        foreach ($languages as $lang) {
            foreach ($fields as $field) {
                $fieldKey = "{$field}_{$lang}";
                $typeOrganisation->translateOrNew($lang)->$field = $validatedData[$fieldKey] ?? $typeOrganisation->translate($lang)->$field ?? null;
            }
        }

        // Save the type association
        $typeOrganisation->save();

        return response()->json([
            'success' => true,
            'message' => 'Type organisation updated successfully',
            'data' => $typeOrganisation
        ], 200);
    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $TypeOrganisation = TypeOrganisation::with('translations')->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Type organisation retrieved successfully',
            'type_organisation' => $TypeOrganisation,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $TypeOrganisation = TypeOrganisation::findOrFail($id);
        $TypeOrganisation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Type organisation deleted successfully',
        ], 200);
    }
}
