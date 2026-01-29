<?php

use App\Http\Controllers\API\WorkJobController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\SkillController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// PUBLIC (no token)
Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);

// PROTECTED (token required)
Route::middleware('auth:sanctum')->group(function() {
    Route::get('/user', [UserController::class, 'me']);    
    Route::get('/skills', [SkillController::class, 'index']);
    Route::post('/skills', [SkillController::class, 'store']);

    // 
    // ---------------------------------Technician------------------------------------------------
    //
    Route::get('/work-jobs', [WorkJobController::class, 'index']);
    Route::get('/work-jobs/{id}', [WorkJobController::class, 'show']);
    Route::post('/work-jobs/{id}/accept', [WorkJobController::class, 'accept']);
    Route::patch('/work-jobs/{id}/status', [WorkJobController::class, 'updateStatus']);
    Route::get('/my-jobs', [WorkJobController::class, 'myJobs']);    
    Route::get('/technician/skills', [UserController::class, 'getSkills']);
    Route::put('/technician/skills', [UserController::class, 'updateSkills']);

    // 
    // ---------------------------------Client----------------------------------------------------
    //
    Route::post('/work-jobs', [WorkJobController::class, 'store']);
    Route::put('/work-jobs/{id}', [WorkJobController::class, 'update']);
    Route::get('/client/my-jobs', [WorkJobController::class, 'clientJobs']);
    
});
