<?php

namespace App\Http\Controllers\Telkominfra;

use App\Http\Controllers\Controller;
use App\Models\KeluhPengguna;
use App\Models\Perjalanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;


class KeluhPenggunaController extends Controller
{
    public function keluh()
    {
        $totalKeluhan = KeluhPengguna::count();
        $keluhanSelesai = KeluhPengguna::whereHas('perjalanan', function ($query) {
            $query->where('selesai', true);
        })->count();

        $keluhanDiproses = KeluhPengguna::whereNotNull('perjalanan_id')
            ->whereHas('perjalanan', function ($query) {
                $query->where('selesai', false);
            })->count();

        $keluhanBelumSelesai = KeluhPengguna::whereNull('perjalanan_id')->count();

        $keluhanBelumSelesaiList = KeluhPengguna::latest()
            ->whereNull('perjalanan_id')
            ->paginate(20, ['*'], 'page_belum');

        $keluhanDiprosesList = KeluhPengguna::latest()
            ->with('perjalanan')
            ->whereNotNull('perjalanan_id')
            ->whereHas('perjalanan', function ($query) {
                $query->where('selesai', false);
            })
            ->paginate(20, ['*'], 'page_proses');

        $keluhanSelesaiList = KeluhPengguna::latest()
            ->with('perjalanan')
            ->whereHas('perjalanan', function ($query) {
                $query->where('selesai', true);
            })
            ->paginate(20, ['*'], 'page_complete');
        
        $userId = Auth::id();
        $keluhanSayaBelumSelesaiList = collect(); 
        if ($userId) {
            $keluhanSayaBelumSelesaiList = KeluhPengguna::latest()
                ->with(['user'])
                ->where('user_id', $userId) 
                ->paginate(20, ['*'], 'page_saya_belum');
        }

        return [
            'totalKeluhan' => $totalKeluhan,
            'keluhanSelesai' => $keluhanSelesai,
            'keluhanDiproses' => $keluhanDiproses,
            'keluhanBelumSelesai' => $keluhanBelumSelesai,
            'keluhanBelumSelesaiList' => $keluhanBelumSelesaiList,
            'keluhanSayaBelumSelesaiList' => $keluhanSayaBelumSelesaiList,
            'keluhanDiprosesList' => $keluhanDiprosesList,
            'keluhanSelesaiList' => $keluhanSelesaiList,
        ];
    }
    public function index()
    {
        $data = $this->keluh();
        return view('keluh-pengguna', $data);
    }

    
    // public function perjalanan()
    // {
    //     return $this->belongsTo(Perjalanan::class);
    // }

    /**
     * Menangani pencarian AJAX untuk keluhan pengguna.
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $mode = $request->input('mode', 'pending'); 

        if (empty($search)) {
             return response()->json([]);
        }

        $query = KeluhPengguna::query()->with('perjalanan');
        switch ($mode) {
            case 'pending':
                $query->whereNull('perjalanan_id');
                break;
            
            case 'processing':
                $query->whereNotNull('perjalanan_id')
                     ->whereHas('perjalanan', function ($q) {
                         $q->where('selesai', false);
                     });
                break;
            
            case 'complete':
                $query->whereHas('perjalanan', function ($q) {
                    $q->where('selesai', true);
                });
                break;
        }

        $keluhans = $query->where(function ($query) use ($search) {
                $query->where('nama_pengguna', 'like', '%' . $search . '%')
                      ->orWhere('nama_tempat', 'like', '%' . $search . '%')
                      ->orWhere('komentar', 'like', '%' . $search . '%')
                      ->orWhere('id', 'like', '%' . $search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->get(); 
        
        return response()->json($keluhans);
    }
    
    public function create()
    {
        return view('keluh-pengguna');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pengguna' => 'required|string|max:255',
            'nama_tempat'   => 'required|string|max:255',
            'komentar'      => 'nullable|string',
            'foto'          => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();

            $filename = now()->format('Ymd_His') . '_' . $originalName . '.' . $extension;

            $destination = public_path('images/keluh');
            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }

            $file->move($destination, $filename);

            $validated['foto'] = $filename;
        }
        
        $validated['user_id'] = Auth::id(); 
        KeluhPengguna::create($validated);

        return redirect()->route('keluh_pengguna.index')->with('success', 'Keluhan berhasil ditambahkan.');
    }

    public function show($id)
    {
        $keluhPengguna = KeluhPengguna::findOrFail($id);
        return view('keluh-pengguna', compact('keluhPengguna'));
    }

    public function destroy($id)
    {
        $keluh = KeluhPengguna::findOrFail($id);
        $keluh->delete();

        return redirect()->route('keluh_pengguna.index')->with('success', 'Keluhan berhasil dihapus.');
    }



    public function assign(Request $request)
    {
        $validated = $request->validate([
            'perjalanan_id' => 'required|integer|exists:perjalanans,id',
            'keluhan_ids' => 'required|array',
            'keluhan_ids.*' => 'integer|exists:keluh_penggunas,id',
        ]);

        $updatedRows = KeluhPengguna::whereIn('id', $validated['keluhan_ids'])
            ->whereNull('perjalanan_id') // Hanya update yang belum terikat (optional, tapi bagus)
            ->update(['perjalanan_id' => $validated['perjalanan_id']]);

        return response()->json([
            'success' => true, 
            'message' => "{$updatedRows} Komentar berhasil dikaitkan ke perjalanan."
        ]);
    }

    /**
     * Melepaskan satu atau lebih komentar dari perjalanan (mengatur perjalanan_id menjadi NULL).
     */
    public function unassign(Request $request)
    {
        // Cek 1: Validasi harus lulus. Jika gagal, Laravel seharusnya mengembalikan 422 (JSON)
        $validated = $request->validate([
            'keluhan_ids' => 'required|array',
            'keluhan_ids.*' => 'integer|exists:keluh_penggunas,id',
        ]);
        
        try {
            // Cek 2: Pastikan nama tabel/kolom benar
            $updatedRows = KeluhPengguna::whereIn('id', $validated['keluhan_ids'])
                ->whereNotNull('perjalanan_id') 
                ->update(['perjalanan_id' => null]); // <-- Kunci: Set ke NULL

            // Cek 3: Pastikan respons adalah JSON (sudah benar)
            return response()->json([
                'success' => true, 
                'message' => "{$updatedRows} Komentar berhasil dilepaskan dari perjalanan."
            ]);
            
        } catch (\Exception $e) {
            // Cek 4: Tangani error database/server secara eksplisit dan kembalikan JSON
            \Log::error("Unassign Error: " . $e->getMessage()); // Catat ke log server
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat melepaskan komentar: ' . $e->getMessage()
            ], 500); // Pastikan status code 500 dikembalikan
        }
    }
}
