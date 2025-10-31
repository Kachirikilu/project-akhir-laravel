<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\JadwalCeramah;
// use App\Models\IoTCamera;
use Carbon\Carbon;
use App\Http\Controllers\Telkominfra\KeluhPenggunaController;
use App\Http\Controllers\Telkominfra\ViewTelkominfraController;
use Illuminate\Http\Request;
use App\Models\User;


class DashboardController extends Controller
{
   protected $today, $nowTime, $startOfWeek, $endOfWeek, $startOfNextWeek, $endOfNextWeek,
              $startOfLastWeek, $endOfLastWeek, $jadwalHariIni, $jadwalBelumTerlaksanaCount,
              $jadwalSudahTerlaksanaCount, $totalJadwalCount, $jadwalMingguIni, $jadwalMingguDepan,
              $jadwalMingguSelanjutnya, $jadwalSudahTerlaksana;
    // protected $iotCamera;

    public function __construct()
    {
        // Memastikan data jadwal sudah diinisialisasi
        $this->jadwal();
    }
    public function jadwal()
    {
        $now = Carbon::now();
        $this->today = $now->toDateString();
        $this->nowTime = $now->toTimeString();

        // Salin Carbon instance untuk manipulasi tanggal/minggu agar tidak saling mempengaruhi
        $startOfWeekInstance = $now->copy()->startOfWeek();
        $endOfWeekInstance = $now->copy()->endOfWeek();
        
        $this->startOfWeek = $startOfWeekInstance->format('Y-m-d');
        $this->endOfWeek = $endOfWeekInstance->format('Y-m-d');

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
        // =======================================================
        // 1. DATA KELUHAN (TIDAK BERUBAH)
        // =======================================================
        $keluhController = new KeluhPenggunaController;
        $keluhData = $keluhController->keluh();

        // =======================================================
        // 2. DATA PENGGUNA (BARU: LOGIKA FILTER USER/ADMIN)
        // =======================================================
        $search = $request->input('search');
        $searchMode = $request->input('mode', ''); // Gunakan string kosong untuk 'all'

        $query = User::query();

        // Filter berdasarkan Mode (Role)
        if ($searchMode === 'admin') {
            $query->where('admin', true);
        } elseif ($searchMode === 'user') {
            $query->where('admin', false);
        }

        // Filter berdasarkan Keyword Pencarian
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%');
                if (is_numeric($search)) {
                    $q->orWhere('id', $search);
                }
            });
        }

        // Hitung total untuk Badge Tab (diperlukan untuk tampilan Blade awal)
        $totalUsers = User::count();
        $totalAdmins = User::where('admin', true)->count();
        $totalNormalUsers = User::where('admin', false)->count();

        // Ambil data yang sudah difilter dan paginasi
        $users = $query->latest()->paginate(10)->withQueryString(); 

        // =======================================================
        // 3. GABUNGKAN SEMUA DATA DAN KIRIM KE VIEW
        // =======================================================
        $userData = [
            'users' => $users,
            'totalUsers' => $totalUsers,
            'totalAdmins' => $totalAdmins,
            'totalNormalUsers' => $totalNormalUsers,
            'search' => $search,
            'searchMode' => $searchMode,
        ];

        // Ambil semua properti jadwal yang telah diinisialisasi di __construct/jadwal()
        $jadwalData = [
            'jadwalBelumTerlaksanaCount' => $this->jadwalBelumTerlaksanaCount,
            'jadwalSudahTerlaksanaCount' => $this->jadwalSudahTerlaksanaCount,
            'totalJadwalCount' => $this->totalJadwalCount,
            'jadwalMingguIni' => $this->jadwalMingguIni,
            'jadwalMingguDepan' => $this->jadwalMingguDepan,
            'jadwalMingguSelanjutnya' => $this->jadwalMingguSelanjutnya,
            'jadwalSudahTerlaksana' => $this->jadwalSudahTerlaksana,
            'jadwalHariIni' => $this->jadwalHariIni,
            // 'iotCamera' => $this->iotCamera, // Tambahkan jika diaktifkan
        ];

        // Gunakan array_merge untuk menggabungkan data keluhan, data pengguna, dan data jadwal
        return view('dashboard', array_merge(
            $keluhData, 
            $userData, 
            $jadwalData
        ));
    }

    public function ajaxSearch(Request $request)
    {
        $search = $request->input('search');
        $mode = $request->input('mode', ''); 
        $page = $request->input('page', 1);

        $query = User::query();

        if ($mode === 'admin') {
            $query->where('admin', true);
        } elseif ($mode === 'user') {
            $query->where('admin', false);
        }
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
                if (is_numeric($search)) {
                    $q->orWhere('id', $search);
                }
            });
        }
        $users = $query->latest()->paginate(10)->withQueryString(); 
        
        $totalUsers = User::count();
        $totalAdmins = User::where('admin', true)->count();
        $totalNormalUsers = User::where('admin', false)->count();

        $paginationData = $users->toArray();
        
        return response()->json([
            'users' => $paginationData['data'], 
            'pagination' => [
                'links' => $users->linkCollection()->toArray(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
            ],
            'counts' => [
                'totalUsers' => $totalUsers,
                'totalAdmins' => $totalAdmins,
                'totalNormalUsers' => $totalNormalUsers,
            ]
        ]);
    }

    public function destroy(User $user)
    {
        if (!auth()->check() || !auth()->user()->admin) {
             if (request()->ajax()) {
                 return response()->json(['message' => 'Akses ditolak.'], 403);
             }
             abort(403, 'Akses ditolak.');
        }

        $user->delete();
        if (request()->ajax()) {
            return response()->json(['message' => 'Pengguna berhasil dihapus.'], 200);
        }
        return back()->with('success', 'Pengguna berhasil dihapus.');
    }


    public function user(Request $request)
    {
        $keluhController = new KeluhPenggunaController;
        $keluhData = $keluhController->keluh();

        // $perjalananController = new ViewTelkominfraController;
        // $perjalananData = $perjalananController->perjalanan($request);

        return view('user-interface', array_merge($keluhData, 
        // $perjalananData, 
        [
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