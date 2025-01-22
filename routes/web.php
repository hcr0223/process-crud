<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;

Route::get('/', function () {
    return redirect()->route('login');
});


Route::get('registro', [AuthController::class, 'showRegisterForm'])->name('registro');
Route::post('registro', [AuthController::class, 'register'])->name('register.create');
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.create');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function() {
    Route::get('tareas', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('obtener-tareas', [TaskController::class, 'list'])->name('tasks.list');
    Route::post('crea-tarea', [TaskController::class, 'create'])->name('tasks.create');
    Route::post('actualizar-tarea', [TaskController::class, 'update'])->name('tasks.update');
    Route::post('cargar-archivo', [TaskController::class, 'uploadFile'])->name('tasks.upload');
    Route::post('eliminar-archivo', [TaskController::class, 'deleteFile'])->name('tasks.file.delete');
});
