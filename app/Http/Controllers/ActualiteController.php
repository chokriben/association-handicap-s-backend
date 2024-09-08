<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Actualite;
use App\Models\Media;
use Illuminate\Support\Facades\Validator;
use Astrotomic\Translatable\Locales;
use Illuminate\Pagination\LengthAwarePaginator;

class ActualiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $perPage = 10)
    {
        // Fetch paginated actualites
        $actualites = Actualite::paginate($perPage);

        // Include user relationship
        $actualites->each(function ($actualite) {
            $user = $actualite->user;
        });

        // Custom pagination if necessary
        if ($request->page) {
            $actualites = new LengthAwarePaginator($actualites, count($actualites), $perPage, $request->page);
        }

        return response()->json([
            'success' => true,
            'message' => 'Sélection effectuée avec succès',
            'actualites' => $actualites,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming data, including translations
        $validatedData = $request->validate([
            'name_fr' => 'required|string|max:255',
            'description_fr' => 'required|string',
            'name_en' => 'required|string|max:255',
            'description_en' => 'required|string',
            'name_ar' => 'required|string|max:255',
            'description_ar' => 'required|string',
            'date' => 'required|date',
            'user_id' => 'required|integer|exists:users,id',
        ]);

        // Create a new Actualite with translations
        $actualite = new Actualite();

        // French translation
        $actualite->translateOrNew('fr')->name = $validatedData['name_fr'];
        $actualite->translateOrNew('fr')->description = $validatedData['description_fr'];

        // English translation
        $actualite->translateOrNew('en')->name = $validatedData['name_en'];
        $actualite->translateOrNew('en')->description = $validatedData['description_en'];

        // Arabic translation
        $actualite->translateOrNew('ar')->name = $validatedData['name_ar'];
        $actualite->translateOrNew('ar')->description = $validatedData['description_ar'];

        // Set additional attributes
        $actualite->date = $validatedData['date'];
        $actualite->user_id = $validatedData['user_id'];

        // Save the actualite
        $actualite->save();

        return response()->json([
            'success' => true,
            'message' => 'Actualité créée avec succès!',
            'data' => $actualite
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $actualite = Actualite::findOrFail($id);
        
        return response()->json([
            "success" => true,
            "message" => "Sélection effectuée avec succès",
            "actualite" => $actualite,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate incoming data with translations
        $validatedData = $request->validate([
            'name_fr' => 'required|string|max:255',
            'description_fr' => 'required|string',
            'name_en' => 'required|string|max:255',
            'description_en' => 'required|string',
            'name_ar' => 'required|string|max:255',
            'description_ar' => 'required|string',
            'date' => 'required|date',
            'user_id' => 'required|integer|exists:users,id',
        ]);

        // Find the existing Actualite
        $actualite = Actualite::findOrFail($id);

        // Update translations
        $actualite->translateOrNew('fr')->name = $validatedData['name_fr'];
        $actualite->translateOrNew('fr')->description = $validatedData['description_fr'];

        $actualite->translateOrNew('en')->name = $validatedData['name_en'];
        $actualite->translateOrNew('en')->description = $validatedData['description_en'];

        $actualite->translateOrNew('ar')->name = $validatedData['name_ar'];
        $actualite->translateOrNew('ar')->description = $validatedData['description_ar'];

        // Update additional attributes
        $actualite->date = $validatedData['date'];
        $actualite->user_id = $validatedData['user_id'];

        // Handle file upload if present
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $allowedPhotoExtension = ['jpg', 'png', 'jpeg', 'gif'];
            $extension = $file->getClientOriginalExtension();

            if (in_array($extension, $allowedPhotoExtension)) {
                $filename = $file->store('Actualite/photos', 'ftp');
                $media = new Media([
                    'legende' => 'legende',
                    'type' => '1',
                    'src' => $filename
                ]);
                $actualite->medias()->save($media);
            }
        }

        // Save the updated actualite
        $actualite->save();

        return response()->json([
            "success" => true,
            "message" => "Modification effectuée avec succès",
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        Actualite::findOrFail($id)->delete();

        return response()->json([
            "success" => true,
            "message" => "Suppression effectuée avec succès",
        ], 200);
    }
}
