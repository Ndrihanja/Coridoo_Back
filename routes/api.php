<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Projet\ProjetController;
use App\Http\Controllers\API\Projet\TacheController;
use App\Http\Controllers\API\User\UserController;

Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [LoginController::class, 'login']);

Route::post('projet/ajoutMembre', [ProjetController::class, 'ajoutMembre'])->middleware(('auth:api'));
Route::resource('projet', ProjetController::class)->except(['edit', 'create'])->middleware(('auth:api'));
Route::resource('tache', TacheController::class)->except(['edit', 'create'])->middleware('auth:api');
Route::resource('user', UserController::class)->except(['edit', 'create'])->middleware('auth:api');
