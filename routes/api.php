<?php

use App\Http\Controllers\Api\PriceCollectionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/prices', [PriceCollectionController::class, 'store'])
        ->middleware(['abilities:prices:create']);
});
