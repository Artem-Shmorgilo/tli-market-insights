<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogController::class, 'index'])
    ->name('catalog.index');

Route::get('/catalog/items', [CatalogController::class, 'items'])
    ->name('catalog.items');

Route::get('/items/{item}', [ItemController::class, 'show'])
    ->name('items.show');
