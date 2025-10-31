<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\JadwalCeramahController;
use App\Http\Controllers\Admin\JsonController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Telkominfra\ViewTelkominfraController;
use App\Http\Controllers\Telkominfra\DataTelkominfraController;
use App\Http\Controllers\Telkominfra\KeluhPenggunaController;


$appName = env('APP_NAME');

Route::get('/', [DashboardController::class, 'user'])->name('user');

if ($appName == 'Al-Aqobah 1') {
    Route::get('/api/jadwal-ceramahs', [JsonController::class, 'index']);
    Route::get('/api/jadwal-ceramahs/{slug}', [JsonController::class, 'show']);
    Route::get('/schedules/show/{slug}', [JadwalCeramahController::class, 'show'])->name('admin.schedules.show');
}
Route::get('/iot/all-data/{id}', [ApiController::class, 'allData'])->name('admin.iot.allData');


Route::middleware([
        'auth:sanctum',
        config('jetstream.auth_session'),
        'verified',
    ])->group(function () {

    Route::middleware('is_admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/admin/users/ajax', [DashboardController::class, 'ajaxSearch'])->name('users.ajaxSearch');
        Route::delete('/admin/users/{user}', [DashboardController::class, 'destroy'])->name('users.destroy');
        $appName = env('APP_NAME');
        if ($appName == 'Al-Aqobah 1') {
            Route::resource('schedules', JadwalCeramahController::class)->names([
                'index' => 'admin.schedules.index',
                'create' => 'admin.schedules.create',
                'store' => 'admin.schedules.store',
                'edit' => 'admin.schedules.edit',
                'update' => 'admin.schedules.update',
                'destroy' => 'admin.schedules.destroy',
            ]);
        }
    });

    Route::get('/esp32Cam', [ApiController::class, 'getData']);
    Route::get('/esp32Cam_motion', [ApiController::class, 'getMotion']);
});


// --------------------------------------------------
// APLIKASI PT. TELKOMINFRA
// --------------------------------------------------
if ($appName == 'PT. Telkominfra') {
    
    Route::prefix('maintenance')
        // ->middleware('is_admin') 
        ->group(function () {
            Route::get('/search', [ViewTelkominfraController::class, 'comentSearch'])->name('maintenance.comentSearch');
            Route::get('/', [ViewTelkominfraController::class, 'index'])->name('maintenance.index');
            Route::get('/{id}', [ViewTelkominfraController::class, 'show'])->name('maintenance.show');
        });

    Route::prefix('perjalanan')->group(function () {
        Route::get('/search', [ViewTelkominfraController::class, 'ajaxSearch'])->name('perjalanan.ajaxSearch');
        Route::post('/perjalanan', [DataTelkominfraController::class, 'store'])->name('perjalanan.store'); 

        
        Route::middleware('is_admin')->group(function () {
            Route::put('/{id}', [DataTelkominfraController::class, 'update'])->name('perjalanan.update');
            Route::patch('/{id}', [DataTelkominfraController::class, 'update'])->name('perjalanan.update.status');
            Route::delete('/{id}', [DataTelkominfraController::class, 'destroy'])->name('perjalanan.destroy');
            Route::delete('/data/{id}', [DataTelkominfraController::class, 'destroyPerjalananData'])->name('perjalanan.dataDestroy');
        });
    });
        
    Route::prefix('keluh-pengguna')->group(function () {
        Route::get('/search', [KeluhPenggunaController::class, 'search'])->name('keluh_pengguna.search');
        Route::get('/', [KeluhPenggunaController::class, 'index'])->name('keluh_pengguna.index');
        
        Route::middleware([
            'auth:sanctum',
            config('jetstream.auth_session'),
            'verified',
        ])->group(function () {
            Route::get('/create', [KeluhPenggunaController::class, 'create'])->name('keluh_pengguna.create');
            Route::post('/', [KeluhPenggunaController::class, 'store'])->name('keluh_pengguna.store');
            Route::delete('/{id}', [KeluhPenggunaController::class, 'destroy'])->name('keluh_pengguna.destroy'); 
        });

        Route::middleware('is_admin')->group(function () {
            Route::post('/assign', [KeluhPenggunaController::class, 'assign'])->name('keluh_pengguna.assign');
            Route::post('/unassign', [KeluhPenggunaController::class, 'unassign'])->name('keluh_pengguna.unassign');
        });

        Route::get('/{id}', [KeluhPenggunaController::class, 'show'])->name('keluh_pengguna.show');
    });
}