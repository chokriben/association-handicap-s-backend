<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiTokenLoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Notifications\UserApproved;
use App\Notifications\UserRejected;

class ApiTokenController extends Controller
{
    public function approveRegistration($id)
    {
        $user = User::find($id);

        if (!$user || $user->status !== 'pending') {
            return response()->json(['error' => 'Invalid user or user not pending approval'], 404);
        }

        $user->status = 'approved';
        $user->save();

        // Envoyer l'email de notification
        $user->notify(new UserApproved());

        return response()->json(['message' => 'User approved successfully, email sent'], 200);
    }


    public function rejectRegistration($id)
    {
        $user = User::find($id);

        if (!$user || $user->status !== 'pending') {
            return response()->json(['error' => 'Invalid user or user not pending approval'], 404);
        }

        $user->status = 'rejected';
        $user->save();

        // Envoyer l'email de notification
        $user->notify(new UserRejected());

        return response()->json(['message' => 'User rejected successfully, email sent'], 200);
    }

    public function register(Request $request)
    {
        if (User::where('email', $request->email)->exists()) {
            return response()->json(['error' => "User already registered"], 409);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'nom_association' => 'required|string|max:255',
            'type_organisation' => 'required|string|in:Centre,Association,Organisme',
            'telephone' => 'required|string|max:20',
            'role' => 'required|in:administrateur,membre',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nom_association' => $request->nom_association,
            'type_organisation' => $request->type_organisation,
            'telephone' => $request->telephone,
            'role' => $request->role,
            'status' => 'pending', // New administrators are pending approval
        ]);

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
    public function getUsersByStatus()
    {
        // Compter les utilisateurs par statut
        $acceptedUsers = User::where('status', 'approved')->count();
        $pendingUsers = User::where('status', 'pending')->count();
        $rejectedUsers = User::where('status', 'rejected')->count();

        // Compter tous les utilisateurs (indépendamment du statut)
        $totalUsers = User::count();

        // Retourner les résultats dans la réponse JSON
        return response()->json([
            'accepted' => $acceptedUsers,
            'pending' => $pendingUsers,
            'rejected' => $rejectedUsers,
            'total' => $totalUsers, // Ajouter le nombre total d'utilisateurs
        ]);
    }


}
