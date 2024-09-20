<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reception;
use Illuminate\Support\Facades\Validator;
use Astrotomic\Translatable\Locales;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use PgSql\Lob;

class ReceptionController  extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Fetch all receptions
        $receptions = Reception::all();

        return response()->json([
            'success' => true,
            'message' => 'Sélection effectuée avec succès',
            'receptions' => $receptions,
        ], 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming data, including translations
        $validatedData = $request->validate([
            'association_id' => 'required|exists:associations,id',
            'nom_fr' => 'nullable|string|max:255',
            'prenom_fr' => 'nullable|string',
            'nom_en' => 'nullable|string|max:255',
            'prenom_en' => 'nullable|string',
            'nom_ar' => 'nullable|string|max:255',
            'prenom_ar' => 'nullable|string',
            'adresse_ar' => 'nullable|string',
            'adresse_fr' => 'nullable|string',
            'adresse_en' => 'nullable|string',
            'message_ar' => 'nullable|string',
            'message_fr' => 'nullable|string',
            'message_en' => 'nullable|string',
            'email' => 'nullable|email',
            'num_postale' => 'nullable|string|max:10'
        ]);


        // Debugging: Check validated data
        Log::info('Validated Data:', $validatedData);

        $reception = isset($validatedData['id']) ? Reception::find($validatedData['id']) : new Reception();

        // Set additional attributes
        $reception->association_id = $validatedData['association_id'];
        $reception->email = $validatedData['email'] ;
        $reception->num_postale = $validatedData['num_postale'] ;

        $languages = ['fr', 'en', 'ar'];
        $fields = ['adresse', 'message', 'nom', 'prenom'];

        foreach ($languages as $lang) {
            foreach ($fields as $field) {
                $fieldKey = "{$field}_{$lang}";
                $reception->translateOrNew($lang)->$field = $validatedData[$fieldKey] ?? null;
            }
        }

        // Save the reception
        $reception->save();

        return response()->json([
            'success' => true,
            'message' => 'reception créée avec succès!',
            'data' => $reception
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $reception = Reception::findOrFail($id);

        return response()->json([
            "success" => true,
            "message" => "Sélection effectuée avec succès",
            "reception" => $reception,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, $id)
    // {
    //     // Validate incoming data with translations
    //     $validatedData = $request->validate([
    //         'name_fr' => 'required|string|max:255',
    //         'description_fr' => 'required|string',
    //         'name_en' => 'required|string|max:255',
    //         'description_en' => 'required|string',
    //         'name_ar' => 'required|string|max:255',
    //         'description_ar' => 'required|string',

    //     ]);

    //     // Find the existing reception
    //     $reception = Reception::findOrFail($id);

    //     // Update translations
    //     $reception->translateOrNew('fr')->name = $validatedData['name_fr'];
    //     $reception->translateOrNew('fr')->description = $validatedData['description_fr'];

    //     $reception->translateOrNew('en')->name = $validatedData['name_en'];
    //     $reception->translateOrNew('en')->description = $validatedData['description_en'];

    //     $reception->translateOrNew('ar')->name = $validatedData['name_ar'];
    //     $reception->translateOrNew('ar')->description = $validatedData['description_ar'];


    //     // Save the updated actualite
    //     $reception->save();

    //     return response()->json([
    //         "success" => true,
    //         "message" => "Modification effectuée avec succès",
    //     ], 200);
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        Reception::findOrFail($id)->delete();

        return response()->json([
            "success" => true,
            "message" => "Suppression effectuée avec succès",
        ], 200);
    }
}
