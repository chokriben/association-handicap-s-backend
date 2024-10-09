<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiTokenLoginRequest;
use App\Models\User;
use App\Notifications\NotifyAdminOfNewMember;
use App\Notifications\NotifySuperAdmin;
use App\Notifications\UserApproved;
use App\Notifications\UserPendingApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Notifications\UserRejected;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class ApiTokenController extends Controller
{

    public function approveRegistration($id)
    {
        // Trouver l'utilisateur par ID
        $user = User::findOrFail($id);

        // Mettre à jour le statut de l'utilisateur à 'approved'
        $user->status = 'approved';

        // Notifier l'utilisateur que son inscription est approuvée
        $superAdminName = 'chokri ben mahjoub';
        $user->notify(new UserApproved($user, $superAdminName));

        // Vider le champ plain_password
        $user->plain_password = null;

        // Sauvegarder les modifications
        $user->save();

        return response()->json(['message' => 'User approved successfully.']);
    }


    // Méthode pour rejeter l'inscription
    public function rejectRegistration($id)
    {
        // Trouver l'utilisateur par ID
        $user = User::findOrFail($id);

        // Mettre à jour le statut de l'utilisateur à 'rejected'
        $user->status = 'rejected';
        $user->save();

        // Notifier l'utilisateur que son inscription est rejetée
        $user->notify(new UserRejected($user));

        return response()->json(['message' => 'User rejected successfully.']);
    }

    public function register(Request $request)
    {
        // Vérifier si l'utilisateur existe déjà avec cet email
        if (User::where('email', $request->email)->exists()) {
            return response()->json(['error' => "User already registered"], 409);
        }

        // Valider les champs
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'nom_association' => 'required|string|max:255',
            'type_organisation' => 'required|string|in:Centre,Association,Organisme',
            'telephone' => 'required|string|max:20',
            'role' => 'required|in:administrateur,membre',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Si le rôle est "membre", vérifier si l'association est gérée par un administrateur approuvé
        if ($request->role === 'membre') {
            $associationExists = User::where('nom_association', $request->nom_association)
                ->where('role', 'administrateur')
                ->where('status', 'approved') // Vérification du statut "approved"
                ->exists();

            if (!$associationExists) {
                return response()->json(['error' => "L'association spécifiée n'existe pas ou n'est pas gérée par un administrateur approuvé."], 422);
            }
        }

        // Stocker le mot de passe en clair avant de le hasher
        $plainPassword = $request->password;

        // Rechercher l'administrateur associé à la même association si le rôle est "membre"
        $admin = null;
        if ($request->role === 'membre') {
            $admin = User::where('nom_association', $request->nom_association)
                ->where('role', 'administrateur')
                ->where('status', 'approved')
                ->first();
        }

        // Créer le nouvel utilisateur
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($plainPassword),
            'plain_password' => $plainPassword,
            'nom_association' => $request->nom_association,
            'type_organisation' => $request->type_organisation,
            'telephone' => $request->telephone,
            'role' => $request->role,
            'status' => 'pending', // Set status to pending
            'admin_id' => $admin ? $admin->id : null, // Associer à un admin si trouvé pour les membres
            'admin_id' => $admin ? $admin->id : null,
        ]);
        // Gestion de l'upload de la photo de profil
        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile_photos', 'public'); // Stockage dans le dossier 'storage/app/public/profile_photos'
            $user->profile_photo = $path; // Enregistrer le chemin de la photo dans le modèle
            $user->save(); // Sauvegarder les modifications
        }
        // Notifier en fonction du rôle
        if ($user->role === 'administrateur') {
            // Notifier le super administrateur pour les nouveaux administrateurs
            $superAdminEmail = config('mail.superadmin');
            Notification::route('mail', $superAdminEmail)->notify(new NotifySuperAdmin($user));
        } else if ($user->role === 'membre' && $admin) {
            // Notifier l'administrateur de l'association pour les nouveaux membres
            $admin->notify(new NotifyAdminOfNewMember($user));
        }

        return response()->json(['message' => 'Registration successful, awaiting approval', 'user' => $user], 201);
    }



    public function login(ApiTokenLoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => "Invalid credentials"], 401);
        }

        if ($user->status !== 'approved') {
            return response()->json(['error' => 'Your account is not approved yet'], 403);
        }

        // Generate a new token for the user
        $token = $user->createToken('default')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ], 200);
    }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        // Retourner une réponse JSON avec un message de succès
        return response()->json(['message' => 'Déconnexion réussie'], 200);
    }
    public function getUser(Request $request)
    {
        // Get the currently authenticated user
        $user = $request->user();

        // Exclude the role attribute for super-admin
        if ($user->role === 'super-admin') {
            unset($user->role);
        }

        return response()->json(['user' => $user], 200);
    }

    public function getAdministrators()
    {
        // Récupérer tous les utilisateurs ayant le rôle d'administrateur
        $administrators = User::where('role', 'administrateur')->get();

        // Retourner une réponse JSON avec la liste des administrateurs
        return response()->json(['administrators' => $administrators], 200);
    }
    public function updateProfile(Request $request)
    {
        // Récupérer l'utilisateur authentifié (administrateur)
        $user = $request->user();

        // Valider les nouvelles informations du profil
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'telephone' => 'required|string|max:20',
            'adresse' => 'nullable|string|max:255', // Optionnel, l'adresse peut être nulle
            'password' => 'nullable|string|min:8|confirmed', // Optionnel, seulement si l'utilisateur veut changer son mot de passe
        ]);

        // Mettre à jour les informations de l'utilisateur
        $user->name = $request->name;
        $user->email = $request->email;
        $user->telephone = $request->telephone;
        $user->adresse = $request->adresse;

        // Si un nouveau mot de passe est fourni, le mettre à jour
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Sauvegarder les modifications
        $user->save();

        // Retourner une réponse JSON avec un message de succès et les nouvelles données de l'utilisateur
        return response()->json(['message' => 'Profile updated successfully', 'user' => $user], 200);
    }
    public function getUsersByStatusAdmin()
    {
        // Count users with role 'administrateur' by status
        $acceptedAdmins = User::where('role', 'administrateur')->where('status', 'approved')->count();
        $pendingAdmins = User::where('role', 'administrateur')->where('status', 'pending')->count();
        $rejectedAdmins = User::where('role', 'administrateur')->where('status', 'rejected')->count();

        // Total count of users with role 'administrateur'
        $totalAdmins = User::where('role', 'administrateur')->count();

        // Return the results in a JSON response
        return response()->json([
            'accepted' => $acceptedAdmins,
            'pending' => $pendingAdmins,
            'rejected' => $rejectedAdmins,
            'total' => $totalAdmins,
        ]);
    }
    public function getUsersByStatusMembre()
    {
        // Count users with role 'membre' by status
        $acceptedMembers = User::where('role', 'membre')->where('status', 'approved')->count();
        $pendingMembers = User::where('role', 'membre')->where('status', 'pending')->count();
        $rejectedMembers = User::where('role', 'membre')->where('status', 'rejected')->count();

        // Total count of users with role 'membre'
        $totalMembers = User::where('role', 'membre')->count();

        // Return the results in a JSON response
        return response()->json([
            'accepted' => $acceptedMembers,
            'pending' => $pendingMembers,
            'rejected' => $rejectedMembers,
            'total' => $totalMembers,
        ]);
    }
    public function addMember(Request $request)
    {
        // Récupérer l'administrateur connecté
        $admin = $request->user();

        // Vérifier que l'utilisateur connecté est bien un administrateur approuvé
        if ($admin->role !== 'administrateur' || $admin->status !== 'approved') {
            return response()->json(['error' => "Only approved administrators can add members."], 403);
        }

        // Valider les champs d'entrée
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'telephone' => 'required|string|max:20',
            'adresse' => 'nullable|string|max:255',
        ]);

        // Stocker le mot de passe en clair avant de le hasher
        $plainPassword = $request->password;

        // Créer le nouvel utilisateur (membre)
        $member = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($plainPassword),
            'plain_password' => $plainPassword,
            'nom_association' => $admin->nom_association,
            'type_organisation' => $admin->type_organisation,
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
            'role' => 'membre',
            'status' => 'approved',
            'admin_id' => $admin->id,
        ]);

        // Notifier l'administrateur que le membre a été ajouté avec succès
        $admin->notify(new NotifyAdminOfNewMember($member));

        return response()->json(['message' => 'Member added successfully and pending approval.', 'member' => $member], 201);
    }
    public function updateMember(Request $request, $id)
    {
        // Récupérer l'administrateur connecté
        $admin = $request->user();

        // Vérifier que l'utilisateur connecté est bien un administrateur approuvé
        if ($admin->role !== 'administrateur' || $admin->status !== 'approved') {
            return response()->json(['error' => "Only approved administrators can update members."], 403);
        }

        // Trouver le membre par ID
        $member = User::where('id', $id)
            ->where('admin_id', $admin->id) // S'assurer que le membre est associé à l'administrateur connecté
            ->where('role', 'membre') // S'assurer que l'utilisateur est un membre
            ->firstOrFail();

        // Valider les champs d'entrée
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $member->id, // Unique, sauf pour l'utilisateur actuel
            'telephone' => 'required|string|max:20',
            'adresse' => 'nullable|string|max:255', // Optionnel
            'password' => 'nullable|string|min:8|confirmed', // Optionnel, seulement si l'utilisateur veut changer le mot de passe
        ]);

        // Mettre à jour les informations du membre
        $member->name = $request->name;
        $member->email = $request->email;
        $member->telephone = $request->telephone;
        $member->adresse = $request->adresse;

        // Si un nouveau mot de passe est fourni, le mettre à jour
        if ($request->filled('password')) {
            $member->password = Hash::make($request->password);
        }

        // Sauvegarder les modifications
        $member->save();

        return response()->json(['message' => 'Member updated successfully', 'member' => $member], 200);
    }

    public function deleteMember(Request $request, $id)
    {
        // Récupérer l'administrateur connecté
        $admin = $request->user();

        // Vérifier que l'utilisateur connecté est bien un administrateur approuvé
        if ($admin->role !== 'administrateur' || $admin->status !== 'approved') {
            return response()->json(['error' => "Only approved administrators can delete members."], 403);
        }

        // Trouver le membre par ID
        $member = User::where('id', $id)
            ->where('admin_id', $admin->id) // S'assurer que le membre est associé à l'administrateur connecté
            ->where('role', 'membre') // S'assurer que l'utilisateur est un membre
            ->firstOrFail();

        // Supprimer le membre
        $member->delete();

        return response()->json(['message' => 'Member deleted successfully'], 200);
    }
    public function getMembers(Request $request)
    {
        // Récupérer l'administrateur connecté
        $admin = $request->user();

        // Vérifier que l'utilisateur connecté est bien un administrateur approuvé
        if ($admin->role !== 'administrateur' || $admin->status !== 'approved') {
            return response()->json(['error' => "Only approved administrators can view members."], 403);
        }

        // Récupérer les membres associés à cet administrateur
        $members = User::where('admin_id', $admin->id)->where('role', 'membre')->get();

        return response()->json(['members' => $members], 200);
    }
    public function getUserAssociationId()
    {
        // Vérifier si l'utilisateur est authentifié
        if (Auth::check()) {
            // Récupérer l'utilisateur connecté
            $user = Auth::user();

            // Récupérer l'association associée
            $association = $user->association;

            // Vérifier si l'association existe
            if ($association) {
                return response()->json([
                    'success' => true,
                    'association_id' => $association->id,
                    'message' => 'Association ID retrieved successfully.',
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No association found for this user.',
                ], 404);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'User is not authenticated.',
        ], 401);
    }


}
