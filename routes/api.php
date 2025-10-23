<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Telkominfra\ViewTelkominfraController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/send-data', [ApiController::class, 'handleHttpData']);

Route::get('/telkominfra', [ViewTelkominfraController::class, 'index'])->name('api.maintenance.index');
Route::get('/telkominfra/{id}', [ViewTelkominfraController::class, 'show'])->name('api.maintenance.show');

// // Debug route to inspect headers and host when accessed via devtunnels/reverse proxies
// Route::get('/debug-headers', function (Request $request) {
//     return response()->json([
//         'path' => $request->path(),
//         'full_url' => $request->fullUrl(),
//         'host' => $request->getHost(),
//         'method' => $request->method(),
//         'accept_header' => $request->header('Accept'),
//         'headers' => $request->headers->all(),
//     ]);
// });

// // Lightweight ping to verify JSON responses through proxies (HTTP/2) work
// Route::get('/ping', function () {
//     return response()->json(['ok' => true]);
// });

