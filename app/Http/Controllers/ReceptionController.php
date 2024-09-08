<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reception;
use Illuminate\Support\Facades\Validator;
use Astrotomic\Translatable\Locales;
use Illuminate\Pagination\LengthAwarePaginator;

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
            'nom_fr' => 'required|string|max:255',
            'prenom_fr' => 'required|string',
            'nom_en' => 'required|string|max:255',
            'prenom_en' => 'required|string',
            'nom_ar' => 'required|string|max:255',
            'prenom_ar' => 'required|string',
            'adresse_ar' => 'required|string',
            'adresse_fr' => 'required|string',
            'adresse_en' => 'required|string',
            'message_ar' => 'required',
            'message_fr' => 'required',
            'message_en' => 'required',
            'email' => 'required|email',
            'num_postale' => 'required|string|max:10',

        ]);

        // Create a new Actualite with translations
        $reception = new Reception();

        // French translation
        $reception->translateOrNew('fr')->nom = $validatedData['nom_fr'];
        $reception->translateOrNew('fr')->message = $validatedData['message_fr'];
        $reception->translateOrNew('fr')->prenom = $validatedData['prenom_fr'];
        $reception->translateOrNew('fr')->adresse = $validatedData['adresse_fr'];

        // English translation
        $reception->translateOrNew('en')->nom = $validatedData['nom_en'];
        $reception->translateOrNew('en')->message = $validatedData['message_en'];
        $reception->translateOrNew('en')->prenom = $validatedData['prenom_en'];
        $reception->translateOrNew('en')->adresse = $validatedData['adresse_en'];

        // Arabic translation
        $reception->translateOrNew('ar')->nom = $validatedData['nom_ar'];
        $reception->translateOrNew('ar')->adresse = $validatedData['adresse_ar'];
        $reception->translateOrNew('ar')->prenom = $validatedData['prenom_ar'];
        $reception->translateOrNew('ar')->message = $validatedData['message_ar'];


 // Set additional attributes
        $reception->email = $validatedData['email'];
       $reception->num_postale = $validatedData['num_postale'];
        // Save the recep
        $reception->save();

        return response()->json([
            'success' => true,
            'message' => 'Actualité créée avec succès!',
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
