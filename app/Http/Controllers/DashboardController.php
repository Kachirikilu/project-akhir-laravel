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
        // 2. DATA PENGGUNA (BARU: LOGIKA FILTER ROLE MENGGUNAKAN whereHas)
        // =======================================================
        $search = $request->input('search');
        $searchMode = $request->input('mode', ''); // Gunakan string kosong untuk 'all'

        $query = User::query();

        // Filter berdasarkan Mode (Role) - MENGGUNAKAN RELASI
        if ($searchMode === 'admin') {
            $query->whereHas('admin'); // Cek relasi ke tabel 'admins'
        } elseif ($searchMode === 'dosen') { // Tambahkan filter Dosen
            $query->whereHas('dosen');
        } elseif ($searchMode === 'mahasiswa') { // Tambahkan filter Mahasiswa
            $query->whereHas('mahasiswa');
        } elseif ($searchMode === 'user') {
            // User Biasa: Tidak memiliki relasi admin, dosen, MAHASISWA
            $query->whereDoesntHave('admin')
                  ->whereDoesntHave('dosen')
                  ->whereDoesntHave('mahasiswa');
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

        // Hitung total untuk Badge Tab - MENGGUNAKAN RELASI
        $totalUsers = User::count();
        $totalAdmins = User::whereHas('admin')->count(); // Diperbaiki
        $totalDosens = User::whereHas('dosen')->count(); // Baru
        $totalMahasiswas = User::whereHas('mahasiswa')->count(); // Baru
        
        // Total Normal User: Pengguna yang TIDAK memiliki relasi Admin, Dosen, atau Mahasiswa
        $totalNormalUsers = User::whereDoesntHave('admin')
                                 ->whereDoesntHave('dosen')
                                 ->whereDoesntHave('mahasiswa')
                                 ->count(); // Diperbaiki

        // Ambil data yang sudah difilter dan paginasi
        $users = $query->latest()->paginate(10)->withQueryString(); 

        // =======================================================
        // 3. GABUNGKAN SEMUA DATA DAN KIRIM KE VIEW
        // =======================================================
        $userData = [
            'users' => $users,
            'totalUsers' => $totalUsers,
            'totalAdmins' => $totalAdmins,
            'totalDosens' => $totalDosens, // Tambah
            'totalMahasiswas' => $totalMahasiswas, // Tambah
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

        $query = User::query()->with(['admin', 'dosen', 'mahasiswa']);

        // Filter berdasarkan role
        if ($mode === 'admin') {
            $query->whereHas('admin');
        } elseif ($mode === 'dosen') {
            $query->whereHas('dosen');
        } elseif ($mode === 'mahasiswa') {
            $query->whereHas('mahasiswa');
        } elseif ($mode === 'user') {
            $query->whereDoesntHave('admin')
                  ->whereDoesntHave('dosen')
                  ->whereDoesntHave('mahasiswa');
        }

        // Filter pencarian
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', '%' . $search . '%')
                  ->orWhereHas('admin', fn($sub) => $sub->where('name', 'like', '%' . $search . '%'))
                  ->orWhereHas('dosen', fn($sub) => $sub->where('name', 'like', '%' . $search . '%'))
                  ->orWhereHas('mahasiswa', fn($sub) => $sub->where('name', 'like', '%' . $search . '%'));

                if (is_numeric($search)) {
                    $q->orWhere('id', $search);
                }
            });
        }

        // Ambil data + paginasi
        $users = $query->latest()->paginate(10)->withQueryString();

        // Buat struktur data dengan "name" dari relasi
        $mappedUsers = $users->map(function ($user) {
            $role = 'User';
            $name = $user->email;

            if ($user->admin) {
                $role = 'Admin';
                $name = $user->admin->name;
            } elseif ($user->dosen) {
                $role = 'Dosen';
                $name = $user->dosen->name;
            } elseif ($user->mahasiswa) {
                $role = 'Mahasiswa';
                $name = $user->mahasiswa->name;
            }

            return [
                'id' => $user->id,
                'name' => $name,
                'email' => $user->email,
                'role' => $role,
                'created_at' => $user->created_at->format('d M Y H:i'),
            ];
        });

        // Hitung total
        $counts = [
            'totalUsers' => User::count(),
            'totalAdmins' => User::whereHas('admin')->count(),
            'totalDosen' => User::whereHas('dosen')->count(),
            'totalMahasiswa' => User::whereHas('mahasiswa')->count(),
            'totalNormalUsers' => User::whereDoesntHave('admin')
                ->whereDoesntHave('dosen')
                ->whereDoesntHave('mahasiswa')
                ->count(),
        ];

        return response()->json([
            'users' => [
                'data' => $mappedUsers,
                'links' => $users->linkCollection()->toArray(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
            ],
            'counts' => $counts
        ]);
    }

    public function destroy(User $user)
    {
        // Perbaikan: Gunakan relasi admin() untuk otorisasi
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
