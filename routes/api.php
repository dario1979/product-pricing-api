<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductPriceController;
use Illuminate\Support\Facades\Route;

Route::get('/products', [ProductController::class, 'index']);

Route::post('/products', [ProductController::class, 'store']);

Route::get('/products/{id}', [ProductController::class, 'show'])
    ->whereNumber('id');

Route::put('/products/{id}', [ProductController::class, 'update'])
    ->whereNumber('id');

Route::delete('/products/{id}', [ProductController::class, 'destroy'])
    ->whereNumber('id');


Route::get('/products/{id}/prices', [ProductPriceController::class, 'index',
])->whereNumber('id');

Route::post('/products/{id}/prices', [ProductPriceController::class,'store',
])->whereNumber('id');
