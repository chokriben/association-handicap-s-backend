<?php
namespace App\Http\Controllers;

use App\Models\User; // Importation du modèle User
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MemberController extends Controller
{
    public function store(Request $request)
    {
        // Vérification si l'utilisateur est authentifié
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Validation des données
        $validatedData = $request->validate([
            'users_id' => 'required|exists:users,id', // Assurez-vous qu'il existe un utilisateur
            'name_fr' => 'nullable|string|max:255',
            'prenom_fr' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'prenom_en' => 'nullable|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'prenom_ar' => 'nullable|string|max:255',
            'adresse_fr' => 'nullable|string',
            'adresse_en' => 'nullable|string',
            'adresse_ar' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:10',
            'password' => 'nullable|string|min:8|max:255',
        ]);

        // Création d'un nouvel utilisateur avec le rôle 'membre'
        $user = User::create([
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => 'membre', // Attribution du rôle 'membre'
        ]);

        // Création d'un nouvel member
        $member = new Member();

        // Mise à jour des attributs du member
        $member->users_id = $user->id; // Lier le member à l'utilisateur
        $member->phone = $validatedData['phone'] ?? null;

        // Gestion des traductions pour les champs multilingues
        $languages = ['fr', 'en', 'ar'];
        $fields = ['adresse', 'name', 'prenom'];

        foreach ($languages as $lang) {
            foreach ($fields as $field) {
                $fieldKey = "{$field}_{$lang}";
                if (isset($validatedData[$fieldKey])) {
                    // Utilisation de la méthode `translateOrNew` pour les traductions
                    $member->translateOrNew($lang)->$field = $validatedData[$fieldKey];
                }
            }
        }

        // Sauvegarde du member et de ses traductions
        try {
            $member->save();
            return response()->json([
                'message' => 'Member ajouté avec succès!',
                'member' => $member,
            ], 201); // 201 Created
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de l\'ajout du member.',
                'error' => $e->getMessage(),
            ], 500); // 500 Internal Server Error
        }
    }

    public function index()
    {
        // Récupération de tous les members
        $members = Member::with('translations')->get(); // Inclut les traductions pour chaque member

        return response()->json([
            'members' => $members,
        ]);
    }
}
