<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin']);
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::get('/register', [AuthController::class, 'showRegister']);
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});


Route::middleware("auth")->group(function () {
    // Route::get('/dashboard', function () {
    //     return view('dashboard.index');
    // })->name('dashboard');

    Route::get("/dashboard", [TaskController::class, "home"])->name("dashboard");

    Route::get("/tasks", [TaskController::class, "index"])->name("tasks.index");
    Route::post("/store-task", [TaskController::class, "store"])->name("tasks.store");

    Route::post("/update-task-details/{task}", [TaskController::class, "updateTaskDetails"])->name("tasks.update.details");
    Route::put("/update-task/{task}", [TaskController::class, "update"])->name("tasks.update");
    Route::delete("/delete-task/{task}", [TaskController::class, "destroy"])->name("tasks.destroy");
});