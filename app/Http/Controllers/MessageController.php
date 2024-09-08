<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\MessageTranslation; // Add this if you have a MessageTranslation model
use Astrotomic\Translatable\Locales; // Adjust import based on your setup
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    /**
     * Display a listing of the messages.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Récupérer tous les messages
        $messages = Message::with('translations')->get();

        // Retourner les messages en JSON
        return response()->json($messages);
    }

    /**
     * Store a newly created message in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate common fields
        $validated = $request->validate([
            'sender_id' => 'required|exists:users,id',
            'receiver_id' => 'required|exists:users,id',
            'status' => 'nullable|string', // exemple de validation pour un champ optionnel
        ]);

        // Create a new message
        $message = Message::create($validated);

        // Retrieve available languages
        $locales = app(Locales::class)->all();

        // Validate and store translations for each language
        foreach ($locales as $locale) {
            $validator = Validator::make($request->all(), [
                "content_$locale" => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Save translations
            $message->translations()->create([
                'locale' => $locale,
                'content' => $request->input("content_$locale"),
            ]);
        }

        // Return the created message in JSON with status code 201
        return response()->json($message->load('translations'), 201);
    }

    /**
     * Display the specified message.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Find the message by ID
        $message = Message::with('translations')->find($id);

        if (!$message) {
            return response()->json(['message' => 'Message not found'], 404);
        }

        // Return the found message in JSON
        return response()->json($message);
    }

    /**
     * Update the specified message in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Find the message by ID
        $message = Message::find($id);

        if (!$message) {
            return response()->json(['message' => 'Message not found'], 404);
        }

        // Validate common fields
        $validated = $request->validate([
            'sender_id' => 'sometimes|exists:users,id',
            'receiver_id' => 'sometimes|exists:users,id',
            'status' => 'sometimes|string', // Example for an optional field
        ]);

        // Update the message
        $message->update($validated);

        // Retrieve available languages
        $locales = app(Locales::class)->all();

        // Validate and update translations for each language
        foreach ($locales as $locale) {
            $validator = Validator::make($request->all(), [
                "content_$locale" => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Update or create translations
            $message->translations()->updateOrCreate(
                ['locale' => $locale],
                ['content' => $request->input("content_$locale")]
            );
        }

        // Return the updated message in JSON with status code 200
        return response()->json($message->load('translations'), 200);
    }

    /**
     * Remove the specified message from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Find the message by ID
        $message = Message::find($id);

        if (!$message) {
            return response()->json(['message' => 'Message not found'], 404);
        }

        // Delete the message
        $message->delete();

        // Return a JSON response with a success message
        return response()->json(['message' => 'Message deleted successfully']);
    }
}
