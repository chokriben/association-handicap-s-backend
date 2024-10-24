<?php

use App\Http\Controllers\ActualiteController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApiTokenController;
use App\Http\Controllers\AssociationController;
use App\Http\Controllers\EvenementController;

use App\Http\Controllers\PublicationController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\OrganisationController;
use App\Http\Controllers\ReceptionController;
use App\Http\Controllers\TypeAssociationController;
use App\Http\Controllers\TypeOrganisationController;
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

Route::middleware(['auth:sanctum', 'administrateur'])->post('/approve-registrations/{id}', [ApiTokenController::class, 'approveRegistration']);
Route::middleware(['auth:sanctum', 'administrateur'])->post('/reject-registrations/{id}', [ApiTokenController::class, 'rejectRegistration']);


Route::middleware('auth:sanctum')->post('/add/members', [ApiTokenController::class, 'addMember']); // Updated middleware
Route::middleware('auth:sanctum')->group(function () {
    Route::put('/members/{id}', [ApiTokenController::class, 'updateMember']);
    Route::delete('/members/{id}', [ApiTokenController::class, 'deleteMember']);
    Route::get('/members', [ApiTokenController::class, 'getMembers']);
});
Route::middleware('auth:sanctum')->get('/user/association-id', [ApiTokenController::class, 'getUserAssociationId']);

Route::middleware(['auth:sanctum'])->group(function () {
    // Define resourceful routes for the EvenementController
    Route::resource('evenements', EvenementController::class);
});

Route::get('/administrators', [ApiTokenController::class, 'getAdministrators'])->middleware('auth:sanctum');
Route::get('/administrators_pendings', [ApiTokenController::class, 'getPendingsAdministrators'])->middleware('auth:sanctum');

Route::get('/administrators_rejecteds', [ApiTokenController::class, 'getRejectedsAdministrators'])->middleware('auth:sanctum');
Route::get('/administrators_approveds', [ApiTokenController::class, 'getApprovedsAdministrators'])->middleware('auth:sanctum');
//api get users status
Route::get('/users', [ApiTokenController::class, 'getUsersByStatusAdmin']);
Route::get('/users/membre', [ApiTokenController::class, 'getUsersByStatusMembre']);

 Route::middleware('api')->group(function () {
    Route::resource('publications', PublicationController::class);
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
    Route::resource('type_organisations', TypeOrganisationController::class);
});

Route::middleware('api')->get('/associations_type', [TypeAssociationController::class, 'indexAll']);


Route::middleware('api')->get('/filter_associations', [AssociationController::class, 'filterByType']);

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('associations', AssociationController::class);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::resource('publications', PublicationController::class);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('organisations', OrganisationController::class);
});
Route::post('/admin/members/{user}/accept', [AdminController::class, 'acceptMember']);
Route::post('/admin/members/{user}/reject', [AdminController::class, 'rejectMember']);


Route::post('unblock-member/{id}', [ApiTokenController::class, 'unblockMember'])->middleware('auth:sanctum');


Route::get('/membres_pendings', [ApiTokenController::class, 'getPendingsMember'])->middleware('auth:sanctum');

Route::get('/membres_rejecteds', [ApiTokenController::class, 'getRejectedsMember'])->middleware('auth:sanctum');
Route::get('/membres_approveds', [ApiTokenController::class, 'getApprovedsMember'])->middleware('auth:sanctum');
