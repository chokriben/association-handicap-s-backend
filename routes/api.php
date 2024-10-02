<?php

use App\Http\Controllers\ActualiteController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApiTokenController;
use App\Http\Controllers\AssociationController;
use App\Http\Controllers\EvenementController;

use App\Http\Controllers\PublicationController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ReceptionController;
use App\Http\Controllers\TypeAssociationController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Route;

Route::get('/getAllLangs', [LanguageController::class, 'getAllLangs']);
Route::get('/getCurrentLang', [LanguageController::class, 'getCurrentLang']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();


});

Route::post('/auth/login', [ApiTokenController::class, 'login']);
Route::post('/auth/register', [ApiTokenController::class, 'register']);
Route::middleware('auth:sanctum')->put('/profile', [ApiTokenController::class, 'updateProfile']);
Route::middleware('auth:sanctum')->post('/auth/logout', [ApiTokenController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/user', [ApiTokenController::class, 'getUser']);
Route::middleware(['auth:sanctum', 'super_admin'])->post('/approve-registration/{id}', [ApiTokenController::class, 'approveRegistration']);
Route::middleware(['auth:sanctum', 'super_admin'])->post('/reject-registration/{id}', [ApiTokenController::class, 'rejectRegistration']);
Route::middleware('auth:sanctum')->post('/add/members', [ApiTokenController::class, 'addMember']); // Updated middleware
Route::middleware('auth:sanctum')->group(function () {
    Route::put('/members/{id}', [ApiTokenController::class, 'updateMember']);
    Route::delete('/members/{id}', [ApiTokenController::class, 'deleteMember']);
    Route::get('/members', [ApiTokenController::class, 'getMembers']);
});

// Routes pour la gestion des événements
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/add/evenements', [EvenementController::class, 'store']); // Créer un événement
    Route::get('/evenements', [EvenementController::class, 'index']); // Récupérer tous les événements
    Route::get('/evenements/{id}', [EvenementController::class, 'show']); // Récupérer un événement par ID
    Route::put('/evenements/{id}', [EvenementController::class, 'update']); // Mettre à jour un événement
    Route::delete('/evenements/{id}', [EvenementController::class, 'destroy']); // Supprimer un événement
});
Route::get('/administrators', [ApiTokenController::class, 'getAdministrators'])->middleware('auth:sanctum');

//api get users status
Route::get('/users', [ApiTokenController::class, 'getUsersByStatusAdmin']);
Route::get('/users/membre', [ApiTokenController::class, 'getUsersByStatusMembre']);

 Route::middleware('api')->group(function () {
    Route::resource('publications', PublicationController::class);
});
Route::middleware('api')->group(function () {
    Route::resource('evenements', EvenementController::class);
});

Route::middleware('api')->group(function () {
    Route::resource('actualites', ActualiteController::class);
});
Route::middleware('api')->group(function () {
    Route::resource('receptions', ReceptionController::class);
});


Route::middleware('api')->group(function () {
    Route::resource('type_associations', TypeAssociationController::class);
});
Route::middleware('api')->group(function () {
    Route::resource('associations', AssociationController::class);
});

Route::post('/admin/members/{user}/accept', [AdminController::class, 'acceptMember']);
Route::post('/admin/members/{user}/reject', [AdminController::class, 'rejectMember']);
