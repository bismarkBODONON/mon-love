<?php

use App\Http\Controllers\Api\AlbumController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BackupController;
use App\Http\Controllers\Api\CalendarEventController;
use App\Http\Controllers\Api\DreamController;
use App\Http\Controllers\Api\GalleryItemController;
use App\Http\Controllers\Api\JournalController;
use App\Http\Controllers\Api\LoveLetterController;
use App\Http\Controllers\Api\MusicController;
use App\Http\Controllers\Api\NotificationScheduleController;
use App\Http\Controllers\Api\QuizController;
use App\Http\Controllers\Api\QuoteController;
use App\Http\Controllers\Api\ReasonController;
use App\Http\Controllers\Api\RelationshipController;
use App\Http\Controllers\Api\TimeCapsuleController;
use App\Http\Controllers\Api\TimelineController;
use App\Http\Controllers\Api\UserSettingController;
use App\Http\Controllers\Api\WebauthnController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes publiques (avant déverrouillage par PIN)
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::get('/has-pin', [AuthController::class, 'hasPin']);
    Route::post('/set-pin', [AuthController::class, 'setPin']);
    Route::post('/verify-pin', [AuthController::class, 'verifyPin']);
});

/*
|--------------------------------------------------------------------------
| Routes protégées (middleware Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/relationship', [RelationshipController::class, 'show']);
    Route::put('/relationship', [RelationshipController::class, 'update']);

    Route::apiResource('timeline', TimelineController::class)->except(['show']);
    Route::apiResource('journal', JournalController::class)->except(['show']);
    Route::apiResource('love-letters', LoveLetterController::class);

    Route::get('/reasons/daily', [ReasonController::class, 'daily']);
    Route::apiResource('reasons', ReasonController::class)->except(['show']);

    Route::apiResource('dreams', DreamController::class)->except(['show']);

    Route::get('/quotes/daily', [QuoteController::class, 'daily']);
    Route::apiResource('quotes', QuoteController::class)->except(['show']);

    Route::apiResource('albums', AlbumController::class);
    Route::post('/albums/{album}/items', [GalleryItemController::class, 'store']);
    Route::put('/gallery-items/{galleryItem}', [GalleryItemController::class, 'update']);
    Route::delete('/gallery-items/{galleryItem}', [GalleryItemController::class, 'destroy']);

    Route::apiResource('musics', MusicController::class)->only(['index', 'store', 'update', 'destroy']);

    // --- Nouvelles fonctionnalités ---
    Route::apiResource('calendar-events', CalendarEventController::class)->except(['show']);
    Route::apiResource('notification-schedules', NotificationScheduleController::class)->except(['show']);
    Route::apiResource('time-capsules', TimeCapsuleController::class)->only(['index', 'store', 'destroy']);
    Route::apiResource('quizzes', QuizController::class);
    Route::post('/quizzes/{quiz}/submit', [QuizController::class, 'submit']);

    // --- Gestion des paramètres utilisateur ---
    Route::get('/settings', [UserSettingController::class, 'show']);
    Route::put('/settings', [UserSettingController::class, 'update']);
    Route::post('/settings/wallpaper', [UserSettingController::class, 'uploadWallpaper']);
    Route::delete('/settings/wallpaper', [UserSettingController::class, 'deleteWallpaper']);

    // --- Sauvegarde et import/export ---
    Route::get('/backup/export', [BackupController::class, 'export']);
    Route::post('/backup/import', [BackupController::class, 'import']);

    // --- WebAuthn (gestion des clés) ---
    Route::get('/webauthn/credentials', [WebauthnController::class, 'index']);
    Route::delete('/webauthn/credentials/{webauthnCredential}', [WebauthnController::class, 'destroy']);
    Route::post('/webauthn/register-options', [WebauthnController::class, 'registerOptions']);
    Route::post('/webauthn/register-verify', [WebauthnController::class, 'registerVerify']);

    // --- Changement de PIN (nécessite d'être authentifié) ---
    Route::post('/auth/change-pin', [AuthController::class, 'changePin']);
});

/*
|--------------------------------------------------------------------------
| Routes WebAuthn hors middleware (connexion)
|--------------------------------------------------------------------------
*/
Route::post('/webauthn/login-options', [WebauthnController::class, 'loginOptions']);
Route::post('/webauthn/login-verify', [WebauthnController::class, 'loginVerify']);