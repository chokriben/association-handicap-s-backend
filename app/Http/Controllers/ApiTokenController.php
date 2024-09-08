<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiTokenLoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

        return response()->json(['message' => 'User approved successfully'], 200);
    }

    public function rejectRegistration($id)
    {
        $user = User::find($id);

        if (!$user || $user->status !== 'pending') {
            return response()->json(['error' => 'Invalid user or user not pending approval'], 404);
        }

        $user->status = 'rejected';
        $user->save();

        return response()->json(['message' => 'User rejected successfully'], 200);
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

}
