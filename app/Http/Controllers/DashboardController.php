<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\JadwalCeramah;
// use App\Models\IoTCamera;
use Carbon\Carbon;
use App\Http\Controllers\Telkominfra\KeluhPenggunaController;
use App\Http\Controllers\Telkominfra\ViewTelkominfraController;
use Illuminate\Http\Request;


class DashboardController extends Controller
{
    protected $today;
    protected $nowTime;
    protected $startOfWeek;
    protected $endOfWeek;
    protected $startOfNextWeek;
    protected $endOfNextWeek;
    protected $startOfLastWeek;
    protected $endOfLastWeek;
    protected $jadwalHariIni;
    protected $jadwalBelumTerlaksanaCount;
    protected $jadwalSudahTerlaksanaCount;
    protected $totalJadwalCount;
    protected $jadwalMingguIni;
    protected $jadwalMingguDepan;
    protected $jadwalMingguSelanjutnya;
    protected $jadwalSudahTerlaksana;
    // protected $iotCamera;

    public function __construct()
    {
        $this->jadwal();
    }

    public function jadwal()
    {
        $now = Carbon::now();
        $this->today = $now->toDateString();
        $this->nowTime = $now->toTimeString();

        $this->startOfWeek = $now->startOfWeek()->format('Y-m-d');
        $this->endOfWeek = $now->endOfWeek()->format('Y-m-d');

        $nextWeek = $now->copy()->addWeek();
        $this->startOfNextWeek = $nextWeek->startOfWeek()->format('Y-m-d');
        $this->endOfNextWeek = $nextWeek->endOfWeek()->format('Y-m-d');

        $lastWeek = $now->copy()->subWeek();
        $this->startOfLastWeek = $lastWeek->startOfWeek()->format('Y-m-d');
        $this->endOfLastWeek = $lastWeek->endOfWeek()->format('Y-m-d');

       $this->jadwalHariIni = JadwalCeramah::whereRaw("DATE(tanggal_ceramah) = ?", [$this->today])
            ->orderBy('jam_mulai')
            ->get();


        $this->jadwalBelumTerlaksanaCount = JadwalCeramah::where('tanggal_ceramah', '>=', $this->today)->count();
        $this->jadwalSudahTerlaksanaCount = JadwalCeramah::where('tanggal_ceramah', '<', $this->today)->count();
        $this->totalJadwalCount = JadwalCeramah::count();

        $this->jadwalMingguIni = JadwalCeramah::whereRaw("DATE(tanggal_ceramah) BETWEEN ? AND ?", [$this->startOfWeek, $this->endOfWeek])
            ->orderByDesc('tanggal_ceramah')
            ->orderByDesc('jam_mulai')
            ->get();

        $this->jadwalMingguDepan = JadwalCeramah::whereRaw("DATE(tanggal_ceramah) BETWEEN ? AND ?", [$this->startOfNextWeek, $this->endOfNextWeek])
            ->orderByDesc('tanggal_ceramah')
            ->orderByDesc('jam_mulai')
            ->get();

        $this->jadwalMingguSelanjutnya = JadwalCeramah::whereRaw("DATE(tanggal_ceramah) > ?", [$this->endOfNextWeek])
            ->orderByDesc('tanggal_ceramah')
            ->orderByDesc('jam_mulai')
            ->paginate(12);

        $this->jadwalSudahTerlaksana = JadwalCeramah::where(function ($query) {
            $query->whereRaw("DATE(tanggal_ceramah) < ?", [$this->today])
                ->orWhere(function ($q) {
                    $q->whereRaw("DATE(tanggal_ceramah) = ?", [$this->today])
                        ->where('jam_mulai', '<=', $this->nowTime);
                });
        })
            ->orderByDesc('tanggal_ceramah')
            ->orderByDesc('jam_mulai')
            ->paginate(12);

        // $this->iotCamera = IoTCamera::orderByDesc('created_at')->paginate(12);
    }

    public function index(Request $request)
    {
        $keluhController = new KeluhPenggunaController;
        $keluhData = $keluhController->keluh();

        $perjalananController = new ViewTelkominfraController;
        $perjalananData = $perjalananController->perjalanan($request);

        return view('dashboard', array_merge($keluhData, $perjalananData, [
            'jadwalBelumTerlaksanaCount' => $this->jadwalBelumTerlaksanaCount,
            'jadwalSudahTerlaksanaCount' => $this->jadwalSudahTerlaksanaCount,
            'totalJadwalCount' => $this->totalJadwalCount,
            'jadwalMingguIni' => $this->jadwalMingguIni,
            'jadwalMingguDepan' => $this->jadwalMingguDepan,
            'jadwalMingguSelanjutnya' => $this->jadwalMingguSelanjutnya,
            'jadwalSudahTerlaksana' => $this->jadwalSudahTerlaksana,
            'jadwalHariIni' => $this->jadwalHariIni,
            // 'iotCamera' => $this->iotCamera
        ]));
    }

    public function user(Request $request)
    {
        $keluhController = new KeluhPenggunaController;
        $keluhData = $keluhController->keluh();

        $perjalananController = new ViewTelkominfraController;
        $perjalananData = $perjalananController->perjalanan($request);

        return view('user-interface', array_merge($keluhData, $perjalananData, [
            'jadwalBelumTerlaksanaCount' => $this->jadwalBelumTerlaksanaCount,
            'jadwalSudahTerlaksanaCount' => $this->jadwalSudahTerlaksanaCount,
            'totalJadwalCount' => $this->totalJadwalCount,
            'jadwalMingguIni' => $this->jadwalMingguIni,
            'jadwalMingguDepan' => $this->jadwalMingguDepan,
            'jadwalMingguSelanjutnya' => $this->jadwalMingguSelanjutnya,
            'jadwalSudahTerlaksana' => $this->jadwalSudahTerlaksana,
            'jadwalHariIni' => $this->jadwalHariIni,
            // 'iotCamera' => $this->iotCamera
        ]));
    }
}