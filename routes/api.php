<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\MetricsController;

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
    ]);
});

Route::get('/metrics', [MetricsController::class, 'index']);

Route::post('/contact', [ContactController::class, 'store']);
