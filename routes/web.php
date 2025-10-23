<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\JadwalCeramahController;
use App\Http\Controllers\Admin\JsonController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Telkominfra\ViewTelkominfraController;
use App\Http\Controllers\Telkominfra\DataTelkominfraController;
use App\Http\Controllers\Telkominfra\KeluhPenggunaController;



// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/proxy/jadwal-sholat', function (Request $request) {
//     $idKota = $request->query('idKota', '0816');
//     $today = $request->query('today', now()->format('Y-m-d'));

//     $response = Http::get("https://api.myquran.com/v2/sholat/jadwal/{$idKota}/{$today}");
//     return $response->json();
// });


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
    $appName = env('APP_NAME');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
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

    Route::get('/esp32Cam', [ApiController::class, 'getData']);
    Route::get('/esp32Cam_motion', [ApiController::class, 'getMotion']);
});


if ($appName == 'PT. Telkominfra') {
    Route::prefix('maintenance')->group(function () {
        Route::get('/search', [ViewTelkominfraController::class, 'comentSearch'])->name('maintenance.comentSearch');
        Route::get('/', [ViewTelkominfraController::class, 'index'])->name('maintenance.index');
        Route::get('/{id}', [ViewTelkominfraController::class, 'show'])->name('maintenance.show');
    });

    Route::prefix('perjalanan')->group(function () {
        Route::get('/search', [ViewTelkominfraController::class, 'ajaxSearch'])->name('perjalanan.ajaxSearch');
        Route::post('/perjalanan', [DataTelkominfraController::class, 'store'])->name('perjalanan.store');
        Route::put('/{id}', [DataTelkominfraController::class, 'update'])->name('perjalanan.update');
        Route::patch('/{id}', [DataTelkominfraController::class, 'update'])->name('perjalanan.update.status');
        Route::delete('/{id}', [DataTelkominfraController::class, 'destroy'])->name('perjalanan.destroy');
        Route::delete('/data/{id}', [DataTelkominfraController::class, 'destroyPerjalananData'])->name('perjalanan.dataDestroy');
    });
        
    Route::prefix('keluh-pengguna')->group(function () {
        Route::get('/search', [KeluhPenggunaController::class, 'search'])->name('keluh_pengguna.search');

        Route::get('/', [KeluhPenggunaController::class, 'index'])->name('keluh_pengguna.index');
        Route::get('/create', [KeluhPenggunaController::class, 'create'])->name('keluh_pengguna.create');
        Route::post('/', [KeluhPenggunaController::class, 'store'])->name('keluh_pengguna.store');
        Route::get('/{id}', [KeluhPenggunaController::class, 'show'])->name('keluh_pengguna.show');
        Route::delete('/{id}', [KeluhPenggunaController::class, 'destroy'])->name('keluh_pengguna.destroy');

        Route::post('/assign', [KeluhPenggunaController::class, 'assign'])->name('keluh_pengguna.assign');
        Route::post('/unassign', [KeluhPenggunaController::class, 'unassign'])->name('keluh_pengguna.unassign');
    });
}


