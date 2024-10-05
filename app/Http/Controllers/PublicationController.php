<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class PublicationController extends Controller
{
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

        // Validation des données
        $validatedData = $request->validate([
            'contenu_fr' => 'nullable|string|max:255',
            'titre_fr' => 'nullable|string|max:255',
            'contenu_en' => 'nullable|string|max:255',
            'titre_en' => 'nullable|string|max:255',
            'contenu_ar' => 'nullable|string|max:255',
            'titre_ar' => 'nullable|string|max:255',
            'pdf' => 'nullable|file|mimes:pdf|max:2048', // Validation fichier PDF
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:1024', // Validation photo
            'video' => 'nullable|mimetypes:video/mp4,video/mpeg|max:10240', // Validation vidéo
        ]);

        $publication = new Publication();

        $publication->users_id = $users_id;

        // Gestion des fichiers téléchargés (pdf, photo, video)
        if ($request->hasFile('pdf')) {
            $pdfPath = $request->file('pdf')->store('public/pdf'); // Stockage du fichier PDF
            $publication->pdf = $pdfPath;
        }

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('public/photos'); // Stockage de la photo
            $publication->photo = $photoPath;
        }

        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('public/videos'); // Stockage de la vidéo
            $publication->video = $videoPath;
        }

        // Gestion des traductions pour les champs multilingues
        $languages = ['fr', 'en', 'ar'];
        $fields = ['contenu', 'titre'];

        foreach ($languages as $lang) {
            foreach ($fields as $field) {
                $fieldKey = "{$field}_{$lang}";
                if (isset($validatedData[$fieldKey])) {
                    // Utilisation de la méthode `translateOrNew` pour les traductions
                    $publication->translateOrNew($lang)->$field = $validatedData[$fieldKey];
                }
            }
        }

        // Sauvegarde de la publication et de ses traductions
        try {
            $publication->save();
            return response()->json([
                'message' => 'Publication ajoutée avec succès!',
                'publication' => $publication,
            ], 201); // 201 Created
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de l\'ajout de la publication.',
                'error' => $e->getMessage(),
            ], 500); // 500 Internal Server Error
        }
    }

    public function index()
    {
        // Récupération de l'ID de l'utilisateur connecté
        $users_id = auth()->id();

        // Vérifiez si l'utilisateur est connecté
        if (!$users_id) {
            return response()->json([
                'success' => false,
                'message' => 'L\'utilisateur doit être connecté pour voir ses publications.',
            ], 400);
        }

        // Récupération des publications de l'utilisateur connecté
        $publications = Publication::where('users_id', $users_id)
            ->with('translations')
            ->get();

        return response()->json([
            'success' => true,
            'publications' => $publications,
        ]);
    }
    // Méthode de mise à jour d'une publication
    public function update(Request $request, $id)
    {
        $users_id = auth()->id();

        // Récupérer la publication de l'utilisateur connecté
        $publication = Publication::where('id', $id)
            ->where('users_id', $users_id)
            ->first();

        // Vérifier si la publication existe et appartient à l'utilisateur
        if (!$publication) {
            return response()->json([
                'success' => false,
                'message' => 'Publication non trouvée ou vous n\'êtes pas autorisé à la modifier.',
            ], 404);
        }

        // Validation des données mises à jour
        $validatedData = $request->validate([
            'contenu_fr' => 'nullable|string|max:255',
            'titre_fr' => 'nullable|string|max:255',
            'contenu_en' => 'nullable|string|max:255',
            'titre_en' => 'nullable|string|max:255',
            'contenu_ar' => 'nullable|string|max:255',
            'titre_ar' => 'nullable|string|max:255',
            'pdf' => 'nullable|file|mimes:pdf|max:2048',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
            'video' => 'nullable|mimetypes:video/mp4,video/mpeg|max:10240',
        ]);

        // Mise à jour des fichiers uniquement si un fichier est présent
        if ($request->hasFile('pdf')) {
            // Supprimer l'ancien fichier PDF si nécessaire
            if ($publication->pdf) {
                Storage::delete($publication->pdf);
            }
            $pdfPath = $request->file('pdf')->store('public/pdf');
            $publication->pdf = $pdfPath;
        }

        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo si nécessaire
            if ($publication->photo) {
                Storage::delete($publication->photo);
            }
            $photoPath = $request->file('photo')->store('public/photos');
            $publication->photo = $photoPath;
        }

        if ($request->hasFile('video')) {
            // Supprimer l'ancienne vidéo si nécessaire
            if ($publication->video) {
                Storage::delete($publication->video);
            }
            $videoPath = $request->file('video')->store('public/videos');
            $publication->video = $videoPath;
        }

        // Mise à jour des champs multilingues
        foreach (['fr', 'en', 'ar'] as $lang) {
            foreach (['contenu', 'titre'] as $field) {
                $fieldKey = "{$field}_{$lang}";
                if (array_key_exists($fieldKey, $validatedData)) {
                    $publication->translateOrNew($lang)->$field = $validatedData[$fieldKey];
                }
            }
        }

        $publication->save();

        return response()->json([
            'message' => 'Publication mise à jour avec succès!',
            'publication' => $publication,
        ]);
    }

    // Méthode de suppression d'une publication
    public function destroy($id)
    {
        $users_id = auth()->id();

        // Récupérer la publication de l'utilisateur connecté
        $publication = Publication::where('id', $id)
            ->where('users_id', $users_id)
            ->first();

        // Vérifier si la publication existe et appartient à l'utilisateur
        if (!$publication) {
            return response()->json([
                'success' => false,
                'message' => 'Publication non trouvée ou vous n\'êtes pas autorisé à la supprimer.',
            ], 404);
        }

        // Supprimer la publication
        $publication->delete();

        return response()->json([
            'message' => 'Publication supprimée avec succès!',
        ]);
    }
}
