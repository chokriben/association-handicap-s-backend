<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Actualite;
use App\Models\Media;
use App\Models\Publication;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

class PublicationController extends Controller
{
    public function index(Request $request, $perPage = 10)
    {
        // Récupération des publications paginées
        $publications = Actualite::with('user')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Sélection effectuée avec succès',
            'publications' => $publications,
        ], 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'titre_fr' => 'required|string|max:255',
            'contenu_fr' => 'required|string',
            'titre_en' => 'required|string|max:255',
            'contenu_en' => 'required|string',
            'titre_ar' => 'required|string|max:255',
            'contenu_ar' => 'required|string',
            'date_publication' => 'required|date',
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $publication = new Publication();
        $publication->translateOrNew('fr')->titre = $validatedData['titre_fr'];
        $publication->translateOrNew('fr')->contenu = $validatedData['contenu_fr'];
        $publication->translateOrNew('en')->titre = $validatedData['titre_en'];
        $publication->translateOrNew('en')->contenu = $validatedData['contenu_en'];
        $publication->translateOrNew('ar')->titre = $validatedData['titre_ar'];
        $publication->translateOrNew('ar')->contenu = $validatedData['contenu_ar'];
        $publication->date_publication = $validatedData['date_publication'];
        $publication->user_id = $validatedData['user_id'];

        $publication->save();

        return response()->json([
            'success' => true,
            'message' => 'Actualité créée avec succès!',
            'data' => $publication
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $publication = Publication::with('medias')->findOrFail($id);

        return response()->json([
            "success" => true,
            "message" => "Sélection effectuée avec succès",
            "publication" => $publication,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'titre_fr' => 'required|string|max:255',
            'contenu_fr' => 'required|string',
            'titre_en' => 'required|string|max:255',
            'contenu_en' => 'required|string',
            'titre_ar' => 'required|string|max:255',
            'contenu_ar' => 'required|string',
            'date_publication' => 'required|date',
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $publication = Publication::findOrFail($id);
        $publication->translateOrNew('fr')->title = $validatedData['titre_fr'];
        $publication->translateOrNew('fr')->content = $validatedData['contenu_fr'];
        $publication->translateOrNew('en')->title = $validatedData['titre_en'];
        $publication->translateOrNew('en')->content = $validatedData['contenu_en'];
        $publication->translateOrNew('ar')->title = $validatedData['titre_ar'];
        $publication->translateOrNew('ar')->content = $validatedData['contenu_ar'];
        $publication->date_publication = $validatedData['date_publication'];
        $publication->user_id = $validatedData['user_id'];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $allowedPhotoExtension = ['jpg', 'png', 'jpeg', 'gif'];

            if (in_array($extension, $allowedPhotoExtension)) {
                $filename = $file->store('publication/photos', 'ftp');
                $media = new Media([
                    'legende' => 'legende',
                    'type' => '1',
                    'src' => $filename
                ]);
                $publication->medias()->save($media);
            }
        }

        $publication->save();

        return response()->json([
            "success" => true,
            "message" => "Modification effectuée avec succès",
        ], 200);
    }

    public function destroy(Request $request, $id)
    {
        Publication::findOrFail($id)->delete();

        return response()->json([
            "success" => true,
            "message" => "Suppression effectuée avec succès",
        ], 200);
    }
}
