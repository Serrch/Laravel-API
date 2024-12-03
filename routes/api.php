<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\fighterController;


Route::get('/fighters', [fighterController::class, 'index']); //Obtener una lista de peleadores

Route::get('/fighters/{id}', [fighterController::class, 'show']);

Route::post('/fighters', [fighterController::class, 'store']);

Route::put('/fighters/{id}', [fighterController::class, 'update']);

Route::patch('/fighters/{id}', [fighterController::class, 'updatePartial']);

Route::delete('/fighters/{id}', [fighterController::class, 'destroy']);