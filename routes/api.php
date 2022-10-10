<?php

use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\SectionsController;
use App\Http\Controllers\TasksController;
use App\Http\Controllers\TeamsController;
use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Users Routes
Route::group(['prefix' => 'users'], function () {

    Route::patch('', [UsersController::class, 'updateUser'])->middleware('auth:sanctum');

    Route::post('login', [UsersController::class, 'login']);
    Route::post('register', [UsersController::class, 'register']);
});

// Teams Routes
Route::group(['prefix' => 'teams'], function () {
    Route::post('', [TeamsController::class, 'createTeam'])->middleware('auth:sanctum');
    Route::patch('/{team_id}', [TeamsController::class, 'updateTeam'])->middleware('auth:sanctum');

    // Team Projects Routes
    Route::group(['prefix' => '/{team_id}/projects'], function () {
        Route::get('', [ProjectsController::class, 'getProjects'])->middleware('auth:sanctum');
        Route::post('', [ProjectsController::class, 'createProject'])->middleware('auth:sanctum');
        Route::patch('/{project_id}', [ProjectsController::class, 'updateProject'])->middleware('auth:sanctum');
        Route::get('/{project_id}', [ProjectsController::class, 'showProject'])->middleware('auth:sanctum');

        // Team Project Sections routes
        Route::group(['prefix' => '/{project_id}/sections'], function () {
            Route::get('', [SectionsController::class, 'getSections'])->middleware('auth:sanctum');
            Route::post('', [SectionsController::class, 'createSection'])->middleware('auth:sanctum');
            Route::patch('/{section_id}', [SectionsController::class, 'updateSection'])->middleware('auth:sanctum');
            Route::delete('/{section_id}', [SectionsController::class, 'deleteSection'])->middleware('auth:sanctum');

            // Tasks
            Route::group(['prefix' => '/{section_id}/tasks'], function () {
                Route::post('', [TasksController::class, 'createTask'])->middleware('auth:sanctum');
                Route::get('/{task_id}', [TasksController::class, 'getTask'])->middleware('auth:sanctum');
                Route::delete('/{task_id}', [TasksController::class, 'deleteTask'])->middleware('auth:sanctum');
                Route::patch('/{task_id}', [TasksController::class, 'updateTask'])->middleware('auth:sanctum');
            });
        });
    });
});

