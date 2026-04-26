<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BranchController;
use App\Http\Controllers\Api\V1\ChatController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\DepartmentController;
use App\Http\Controllers\Api\V1\DiagnosisController;
use App\Http\Controllers\Api\V1\DoctorController;
use App\Http\Controllers\Api\V1\MessageController;
use App\Http\Controllers\Api\V1\PatientController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\UserController;
Route::prefix('v1')->group(function () {

    // Public auth routes
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);

        // Protected auth routes
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('me', [AuthController::class, 'me']);
        });
    });

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {

        // Branches
        Route::get('branches', [BranchController::class, 'index']);
        Route::post('branches', [BranchController::class, 'store'])->middleware('role:absolute_admin');
        Route::get('branches/{id}', [BranchController::class, 'show']);
        Route::put('branches/{id}', [BranchController::class, 'update'])->middleware('role:absolute_admin');
        Route::delete('branches/{id}', [BranchController::class, 'destroy'])->middleware('role:moderator,absolute_admin');
        // Departments
        Route::get('departments/{id}', [DepartmentController::class, 'show']);
        Route::get('branches/{branchId}/departments', [DepartmentController::class, 'index']);
        Route::post('branches/{branchId}/departments', [DepartmentController::class, 'store'])->middleware('role:moderator,absolute_admin');
        Route::put('departments/{id}', [DepartmentController::class, 'update'])->middleware('role:moderator,absolute_admin');
        Route::delete('departments/{id}', [DepartmentController::class, 'destroy'])->middleware('role:moderator,absolute_admin');
        Route::get('departments/{id}', [DepartmentController::class, 'show']);

        // Doctors
        Route::get('branches/{branchId}/doctors', [DoctorController::class, 'index']);
        Route::post('doctors', [DoctorController::class, 'store'])->middleware('role:moderator,absolute_admin');
        Route::put('doctors/{id}', [DoctorController::class, 'update'])->middleware('role:moderator,absolute_admin');
        Route::delete('doctors/{id}', [DoctorController::class, 'destroy'])->middleware('role:moderator,absolute_admin');
        Route::get('doctors/{id}', [DoctorController::class, 'show']);
        
        // Patients
        Route::get('patients', [PatientController::class, 'index'])->middleware('role:moderator,absolute_admin');
        Route::get('patients/{id}', [PatientController::class, 'show']);
        Route::post('patients', [PatientController::class, 'store'])->middleware('role:moderator,absolute_admin');
        Route::delete('patients/{id}', [PatientController::class, 'destroy'])->middleware('role:moderator,absolute_admin');

        // Diagnoses
        Route::get('patients/{patientId}/diagnoses', [DiagnosisController::class, 'index']);
        Route::post('patients/{patientId}/diagnoses', [DiagnosisController::class, 'store'])->middleware('role:doctor');
        Route::put('diagnoses/{id}', [DiagnosisController::class, 'update'])->middleware('role:doctor');
        Route::get('diagnoses/{id}', [DiagnosisController::class, 'show']);

        // Chats
        Route::get('chats', [ChatController::class, 'index']);
        Route::post('chats', [ChatController::class, 'store'])->middleware('role:patient');
        Route::get('chats/{id}', [ChatController::class, 'show']);

        // Messages
        Route::get('chats/{chatId}/messages', [MessageController::class, 'index']);
        Route::post('chats/{chatId}/messages', [MessageController::class, 'store']);
        Route::patch('messages/{id}/read', [MessageController::class, 'markRead']);

        // Dashboard
        Route::get('dashboard', [DashboardController::class, 'index']);
        Route::post('users', [UserController::class, 'store'])
            ->middleware('role:moderator,absolute_admin');
    });
});

