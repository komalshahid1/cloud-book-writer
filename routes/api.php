<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\api\BookController;
use App\Http\Controllers\api\SectionController;
use App\Http\Controllers\api\CollaboratorController;

/**
 * Authentication Routes (Laravel Sanctum)
 */
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


/**
 * Book Management (RESTful API)
 */
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('books', BookController::class)->except(['grantAccess','revokeAccess' ]);
    
    /**
     * Section Management (Nested within Books)
     */
    Route::get('/books/{book}/sections', [SectionController::class, 'getSectionsByBook']);


    Route::apiResource('sections', SectionController::class)->except(['getSectionsByBook']);

    
    /**
     * Collaborator Management
     */
    Route::post('/books/{book}/grant-access', [CollaboratorController::class, 'grantAccess']);
    Route::delete('/books/{book}/revoke-access', [CollaboratorController::class, 'revokeAccess']);
});
