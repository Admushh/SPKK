<?php

use App\Http\Controllers\SmartController;

Route::get('/', [SmartController::class, 'index']);
Route::post('/criteria', [SmartController::class, 'storeCriteria']);
Route::post('/alternatives', [SmartController::class, 'storeAlternative']);
Route::post('/calculate', [SmartController::class, 'calculate'])->name('calculate');
Route::get('/results', [SmartController::class, 'results'])->name('results');
Route::delete('/criteria/{id}', [SmartController::class, 'deleteCriteria']);
Route::delete('/alternatives/{id}', [SmartController::class, 'deleteAlternative']);
Route::delete('/values/{id}', [SmartController::class, 'deleteValue']);





