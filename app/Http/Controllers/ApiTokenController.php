<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiTokenLoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ApiTokenController extends Controller
{

    public function register(Request $request)
    {
        if (User::where('email', $request->email)->exists()) {
            return response()->json(['error' => "User already register"], 409);
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

        ]);

        return response()->json(['message' => 'Inscription réussie', 'user' => $user], 201);
    }
    public function login(ApiTokenLoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => "Invalid credentials"], 401);
        }

        $user->tokens()->where('name', $request->token_name)->delete();

        $token = $user->createToken($request->token_name);
        // Abilities
        //$token = $user->createToken($request->token_name, ['repo:view']);

        return [
            'token' => $token->plainTextToken,
            'user' => $user
        ];
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response(null, 204);
    }
}
