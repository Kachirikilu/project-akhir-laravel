<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Perjalanan;
use App\Models\PerjalananData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File; 

class TelkominfraController extends Controller
{
    // public function index()
    // {
    //     $perjalanans = Perjalanan::all();

    //     $allData = [];

    //     foreach ($perjalanans as $perjalanan) {
    //         $nmfPath = public_path('uploads/perjalanan/' . $perjalanan->file_nmf);
    //         $gpxPath = public_path('uploads/perjalanan/' . $perjalanan->file_gpx);

    //         if (!file_exists($nmfPath) || !file_exists($gpxPath)) {
    //             continue;
    //         }

    //         $dataGPS = $this->parseGpxAndNmfGps($gpxPath, $nmfPath, $perjalanan->id);
    //         $dataSinyal = $this->parseNmfSinyal($nmfPath, $perjalanan->id);

    //         $visualData = [];
    //         foreach ($dataSinyal as $i => $sinyal) {
    //             $gps = $dataGPS[$i % max(1, count($dataGPS))] ?? null;
    //             if (!$gps) continue;

    //             $visualData[] = [
    //                 'latitude'  => $gps['latitude'],
    //                 'longitude' => $gps['longitude'],
    //                 'rsrp'      => $sinyal['rsrp'],
    //                 'rsrq'      => $sinyal['rsrq'],
    //                 'sinr'      => $sinyal['sinr'],
    //                 'pci'       => $sinyal['pci'],
    //             ];
    //         }

    //         $midIndex = intval(count($visualData) / 2);
    //         $centerCoords = [
    //             $visualData[$midIndex]['latitude'],
    //             $visualData[$midIndex]['longitude'],
    //         ];

    //     }
    //     return view('telkominfra', [
    //         'id_perjalanan' => null,
    //         'centerCoords' => $centerCoords ?? [-2.9105859, 104.8536157],
    //         'visualData' => $visualData ?? [],
    //         'perjalananDetail' => $perjalanan ?? null,
    //     ]);
    // }

    public function index()
    {
        // Hanya mengambil data Perjalanan (Sesi) dengan pagination
        $perjalanans = Perjalanan::paginate(10); 

        // Mengirimkan daftar sesi perjalanan ke view
        return view('telkominfra', [ // Pastikan ini adalah nama view yang benar
            'perjalanans' => $perjalanans, 
        ]);
    }

    // public function show(string $id) 
    // {
    //     $perjalanan = Perjalanan::where('id', $id)->firstOrFail();
    //     $perjalananData = PerjalananData::where('perjalanan_id', $perjalanan->id)->firstOrFail();
    //     $nmfPath = public_path('uploads/perjalanan/' . $perjalananData->file_nmf);
    //     $dataSinyal = $this->parseNmfSinyal($nmfPath, $perjalananData->id);
    //     $visualData = [];
        
    //     foreach ($dataSinyal as $sinyal) {
            
    //         if ($sinyal['latitude'] === null || $sinyal['longitude'] === null) {
    //             continue; 
    //         }

    //         $visualData[] = [
    //             'latitude'  => $sinyal['latitude'],
    //             'longitude' => $sinyal['longitude'],
    //             'rsrp'      => $sinyal['rsrp'],
    //             'rsrq'      => $sinyal['rsrq'],
    //             'sinr'      => $sinyal['sinr'],
    //             'pci'       => $sinyal['pci'],
    //         ];
    //     }

    //     if (empty($visualData)) {
    //         $centerCoords = [-2.9105859, 104.8536157]; 
    //     } else {
    //         $midIndex = intval(count($visualData) / 2);

    //         $centerCoords = [
    //             $visualData[$midIndex]['latitude'],
    //             $visualData[$midIndex]['longitude'],
    //         ];
    //     }
        
    //     return view('telkominfra', [
    //         'centerCoords' => $centerCoords,
    //         'visualData' => $visualData ?? [],
    //         'perjalananDetail' => $perjalanan ?? null,
    //     ]);
    // }

    // public function show(string $id) 
    // {
    // //     // 1. Ambil Perjalanan (Induk)
    // //     $perjalanan = Perjalanan::where('id', $id)->firstOrFail();
    // //     $perjalananDatas = PerjalananData::where('perjalanan_id', $perjalanan->id)->get(); 
        
    // //     $visualData = []; 

    // //     foreach ($perjalananDatas as $dataItem) {
            
    // //         try {
    // //             $nmfPath = public_path('uploads/perjalanan/' . $dataItem->file_nmf);
    // //             $dataSinyal = $this->parseNmfSinyal($nmfPath, $dataItem->id); 

    // //             foreach ($dataSinyal as $sinyal) {
                    
    // //                 if ($sinyal['latitude'] === null || $sinyal['longitude'] === null) {
    // //                     continue; 
    // //                 }

    // //                 $visualData[] = [
    // //                     'latitude'  => (float) $sinyal['latitude'],
    // //                     'longitude' => (float) $sinyal['longitude'],
    // //                     'rsrp'      => $sinyal['rsrp'] ?? null,
    // //                     'rsrq'      => $sinyal['rsrq'] ?? null,
    // //                     'sinr'      => $sinyal['sinr'] ?? null,
    // //                     'pci'       => $sinyal['pci'] ?? null,
    // //                 ];
    // //             }
    // //         } catch (\Exception $e) {
    // //             Log::error("Gagal memproses file NMF untuk PerjalananData ID: " . $dataItem->id . ". Error: " . $e->getMessage());
    // //             continue; 
    // //         }
    // //     }

    // //     if (empty($visualData)) {
    // //         $centerCoords = [-2.9105859, 104.8536157]; 
    // //     } else {
    // //         $midIndex = intval(count($visualData) / 2);

    // //         $centerCoords = [
    // //             $visualData[$midIndex]['latitude'],
    // //             $visualData[$midIndex]['longitude'],
    // //         ];
    // //     }
        
    // //     // 6. Kirim ke View
    // //     return view('telkominfra', [
    // //         'perjalananDetail' => $perjalanan,
    // //         // 'id_perjalanan' => $perjalanan->id_perjalanan,
    // //         'centerCoords' => $centerCoords,
    // //         'visualData' => $visualData,
    // //     ]);
    // }

    // public function show(string $id) 
    // {
    //     // 1. Ambil Perjalanan (Induk)
    //     $perjalanan = Perjalanan::where('id', $id)->firstOrFail();
    //     $perjalananDatas = PerjalananData::where('perjalanan_id', $perjalanan->id)->get(); 
        
    //     $visualData = []; 

    //     foreach ($perjalananDatas as $dataItem) {
            
    //         try {
    //             $nmfPath = public_path('uploads/perjalanan/' . $dataItem->file_nmf);
    //             $dataSinyal = $this->parseNmfSinyal($nmfPath, $dataItem->id); 

    //             foreach ($dataSinyal as $sinyal) {
                    
    //                 if ($sinyal['latitude'] === null || $sinyal['longitude'] === null) {
    //                     continue; 
    //                 }

    //                 // Kumpulkan semua data sinyal dari semua log ke dalam satu array
    //                 $visualData[] = [
    //                     'latitude'  => (float) $sinyal['latitude'],
    //                     'longitude' => (float) $sinyal['longitude'],
    //                     'rsrp'      => $sinyal['rsrp'] ?? null,
    //                     'rsrq'      => $sinyal['rsrq'] ?? null,
    //                     'sinr'      => $sinyal['sinr'] ?? null,
    //                     'pci'       => $sinyal['pci'] ?? null,
    //                 ];
    //             }
    //         } catch (\Exception $e) {
    //             Log::error("Gagal memproses file NMF untuk PerjalananData ID: " . $dataItem->id . ". Error: " . $e->getMessage());
    //             continue; // Lanjut ke data log berikutnya
    //         }
    //     }

    //     // Tentukan center berdasarkan data gabungan (visualData)
    //     if (empty($visualData)) {
    //         $centerCoords = [-2.9105859, 104.8536157]; // Default jika tidak ada data
    //     } else {
    //         $midIndex = intval(count($visualData) / 2);

    //         $centerCoords = [
    //             $visualData[$midIndex]['latitude'],
    //             $visualData[$midIndex]['longitude'],
    //         ];
    //     }
        
    //     // 6. Kirim ke View (dengan data gabungan untuk satu peta)
    //     return view('telkominfra', [
    //         'perjalananDetail' => $perjalanan,
    //         'centerCoords' => $centerCoords,
    //         'visualData' => $visualData,
    //     ]);
    // }

    // public function show(string $id) 
    // {
    //     // 1. Ambil Perjalanan (Induk)
    //     $perjalanan = Perjalanan::where('id', $id)->firstOrFail();
        
    //     // 2. Ambil Data Anak (PerjalananData)
    //     $perjalananDatas = PerjalananData::where('perjalanan_id', $perjalanan->id)->get(); 
        
    //     // PERBAIKAN: Urutkan data berdasarkan status: 'Before' dahulu, kemudian 'After'.
    //     // Menggunakan sorting PHP in-memory untuk menghindari masalah indeks database.
    //     $perjalananDatas = $perjalananDatas->sortBy(function ($dataItem) {
    //         // Beri bobot 0 untuk 'Before' (prioritas tertinggi) dan 1 untuk 'After'
    //         return $dataItem->status === 'Before' ? 0 : 1;
    //     })->values(); // values() untuk mengindeks ulang array

    //     // Inisialisasi array untuk menampung data visual dari SEMUA log, dipecah per log
    //     $mapsData = []; 

    //     foreach ($perjalananDatas as $dataItem) {
    //         $visualData = [];
    //         $centerCoords = [-2.9105859, 104.8536157]; // Default center

    //         try {
    //             $nmfPath = public_path('uploads/perjalanan/' . $dataItem->file_nmf);
    //             // Asumsi: parseNmfSinyal mengembalikan array sinyal dari satu file
    //             $dataSinyal = $this->parseNmfSinyal($nmfPath, $dataItem->id); 

    //             foreach ($dataSinyal as $sinyal) {
                    
    //                 if ($sinyal['latitude'] === null || $sinyal['longitude'] === null) {
    //                     continue; 
    //                 }

    //                 $visualData[] = [
    //                     'id'        => $dataItem->id,
    //                     'latitude'  => (float) $sinyal['latitude'],
    //                     'longitude' => (float) $sinyal['longitude'],
    //                     'rsrp'      => $sinyal['rsrp'] ?? null,
    //                     'rsrq'      => $sinyal['rsrq'] ?? null,
    //                     'sinr'      => $sinyal['sinr'] ?? null,
    //                     'pci'       => $sinyal['pci'] ?? null,
    //                 ];
    //             }

    //             // Tentukan center berdasarkan visualData yang ditemukan
    //             if (!empty($visualData)) {
    //                 $midIndex = intval(count($visualData) / 2);
    //                 $centerCoords = [
    //                     $visualData[$midIndex]['latitude'],
    //                     $visualData[$midIndex]['longitude'],
    //                 ];
    //             }
    //         } catch (\Exception $e) {
    //             Log::error("Gagal memproses file NMF untuk PerjalananData ID: " . $dataItem->id . ". Error: " . $e->getMessage());
    //             // Data visual untuk log ini akan kosong
    //         }
            
    //         // Tambahkan data log ini ke array utama $mapsData
    //         $mapsData[] = [
    //             'id' => $dataItem->id, // ID unik untuk elemen HTML
    //             'centerCoords' => $centerCoords,
    //             'visualData' => $visualData,
    //             'fileName' => $dataItem->file_nmf,
    //             'perangkat' => $dataItem->perangkat,
    //             'status' => $dataItem->status, // Pastikan status disertakan dalam mapsData
    //         ];
    //     }
        
    //     // 6. Kirim ke View: Mengirimkan array data peta (mapsData)
    //     return view('telkominfra-show', [
    //         'perjalananDetail' => $perjalanan,
    //         'mapsData' => $mapsData, 
    //     ]);
    // }

    // public function show(string $id) 
    // {
    //     // 1. Ambil Perjalanan (Induk)
    //     $perjalanan = Perjalanan::where('id', $id)->firstOrFail();
        
    //     // 2. Ambil Data Anak (PerjalananData)
    //     $perjalananDatas = PerjalananData::where('perjalanan_id', $perjalanan->id)->get(); 
        
    //     // PEMISAHAN DATA: Pisahkan data menjadi 'Before' dan 'After'
    //     $dataBefore = $perjalananDatas->filter(fn($item) => $item->status === 'Before');
    //     $dataAfter = $perjalananDatas->filter(fn($item) => $item->status === 'After');
        
    //     // Inisialisasi array untuk menampung hasil akhir. 
    //     // Struktur ini akan memegang data visual per status.
    //     $mapsData = [
    //         'visualDataBefore' => [],
    //         'visualDataAfter' => [],
    //         'perjalananDetail' => $perjalanan,
    //     ]; 
    //     $centerCoords = [-2.9105859, 104.8536157]; // Default center
        
    //     // --- 3. PEMROSESAN DATA 'BEFORE' ---
    //     foreach ($dataBefore as $dataItem) {
    //         $visualData = [];

    //         try {
    //             $nmfPath = public_path('uploads/perjalanan/' . $dataItem->file_nmf);
    //             // Asumsi: parseNmfSinyal mengembalikan array sinyal dari satu file
    //             $dataSinyal = $this->parseNmfSinyal($nmfPath, $dataItem->id); 

    //             foreach ($dataSinyal as $sinyal) {
    //                 if ($sinyal['latitude'] === null || $sinyal['longitude'] === null) {
    //                     continue; 
    //                 }

    //                 $visualData[] = [
    //                     'id'        => $dataItem->id,
    //                     'latitude'  => (float) $sinyal['latitude'],
    //                     'longitude' => (float) $sinyal['longitude'],
    //                     'rsrp'      => $sinyal['rsrp'] ?? null,
    //                     'rsrq'      => $sinyal['rsrq'] ?? null,
    //                     'sinr'      => $sinyal['sinr'] ?? null,
    //                     'pci'       => $sinyal['pci'] ?? null,
    //                 ];
    //             }

    //             // Tentukan center hanya dari log pertama yang berhasil diproses (opsional)
    //             if (empty($mapsData['visualDataBefore']) && !empty($visualData)) {
    //                 $midIndex = intval(count($visualData) / 2);
    //                 $centerCoords = [
    //                     $visualData[$midIndex]['latitude'],
    //                     $visualData[$midIndex]['longitude'],
    //                 ];
    //             }

    //         } catch (\Exception $e) {
    //             Log::error("Gagal memproses file NMF [Before] untuk ID: " . $dataItem->id . ". Error: " . $e->getMessage());
    //             continue;
    //         }
            
    //         // Tambahkan data log ini ke array visualDataBefore
    //         $mapsData['visualDataBefore'][] = [
    //             'id' => $dataItem->id,
    //             'visualData' => $visualData,
    //             'fileName' => $dataItem->file_nmf,
    //             'perangkat' => $dataItem->perangkat,
    //             'status' => $dataItem->status,
    //         ];
    //     }

    //     // --- 4. PEMROSESAN DATA 'AFTER' ---
    //     foreach ($dataAfter as $dataItem) {
    //         $visualData = [];

    //         try {
    //             $nmfPath = public_path('uploads/perjalanan/' . $dataItem->file_nmf);
    //             $dataSinyal = $this->parseNmfSinyal($nmfPath, $dataItem->id); 

    //             foreach ($dataSinyal as $sinyal) {
    //                 if ($sinyal['latitude'] === null || $sinyal['longitude'] === null) {
    //                     continue; 
    //                 }

    //                 $visualData[] = [
    //                     'id'        => $dataItem->id,
    //                     'latitude'  => (float) $sinyal['latitude'],
    //                     'longitude' => (float) $sinyal['longitude'],
    //                     'rsrp'      => $sinyal['rsrp'] ?? null,
    //                     'rsrq'      => $sinyal['rsrq'] ?? null,
    //                     'sinr'      => $sinyal['sinr'] ?? null,
    //                     'pci'       => $sinyal['pci'] ?? null,
    //                 ];
    //             }
    //         } catch (\Exception $e) {
    //             Log::error("Gagal memproses file NMF [After] untuk ID: " . $dataItem->id . ". Error: " . $e->getMessage());
    //             continue;
    //         }
            
    //         // Tambahkan data log ini ke array visualDataAfter
    //         $mapsData['visualDataAfter'][] = [
    //             'id' => $dataItem->id,
    //             'visualData' => $visualData,
    //             'fileName' => $dataItem->file_nmf,
    //             'perangkat' => $dataItem->perangkat,
    //             'status' => $dataItem->status,
    //         ];
    //     }

    //     // Tambahkan koordinat pusat ke mapsData
    //     $mapsData['centerCoords'] = $centerCoords;
        
    //     // 5. Kirim ke View: Sekarang mapsData berisi 3 kunci utama: visualDataBefore, visualDataAfter, dan centerCoords.
    //     return view('telkominfra-show', [
    //         'perjalananDetail' => $perjalanan,
    //         'mapsData' => $mapsData,
    //     ]);
    // }


    public function show(string $id) 
    {
        // 1. Ambil Perjalanan (Induk)
        $perjalanan = Perjalanan::where('id', $id)->firstOrFail();
        
        // 2. Ambil Data Anak (PerjalananData)
        $perjalananDatas = PerjalananData::where('perjalanan_id', $perjalanan->id)->get(); 
        
        // PEMISAHAN DATA: Pisahkan data menjadi 'Before' dan 'After'
        $dataBefore = $perjalananDatas->filter(fn($item) => $item->status === 'Before');
        $dataAfter = $perjalananDatas->filter(fn($item) => $item->status === 'After');
        
        // Inisialisasi array untuk menampung hasil akhir. 
        // Struktur ini akan memegang data visual per status.
        $mapsData = [
            'visualDataBefore' => [],
            'visualDataAfter' => [],
            'perjalananDetail' => $perjalanan,
        ]; 
        $centerCoords = [-2.9105859, 104.8536157]; // Default center
        
        // --- 3. PEMROSESAN DATA 'BEFORE' ---
        foreach ($dataBefore as $dataItem) {
            $visualData = [];

            try {
                $nmfPath = public_path('uploads/perjalanan/' . $dataItem->file_nmf);
                // Asumsi: parseNmfSinyal mengembalikan array sinyal dari satu file
                $dataSinyal = $this->parseNmfSinyal($nmfPath, $dataItem->id); 

                foreach ($dataSinyal as $sinyal) {
                    if ($sinyal['latitude'] === null || $sinyal['longitude'] === null) {
                        continue; 
                    }

                    $visualData[] = [
                        'id'        => $dataItem->id,
                        'latitude'  => (float) $sinyal['latitude'],
                        'longitude' => (float) $sinyal['longitude'],
                        'rsrp'      => $sinyal['rsrp'] ?? null,
                        'rsrq'      => $sinyal['rsrq'] ?? null,
                        'sinr'      => $sinyal['sinr'] ?? null,
                        'pci'       => $sinyal['pci'] ?? null,
                    ];
                }

                // Tentukan center hanya dari log pertama yang berhasil diproses (opsional)
                if (empty($mapsData['visualDataBefore']) && !empty($visualData)) {
                    $midIndex = intval(count($visualData) / 2);
                    $centerCoords = [
                        $visualData[$midIndex]['latitude'],
                        $visualData[$midIndex]['longitude'],
                    ];
                }

            } catch (\Exception $e) {
                Log::error("Gagal memproses file NMF [Before] untuk ID: " . $dataItem->id . ". Error: " . $e->getMessage());
                continue;
            }
            
            // Tambahkan data log ini ke array visualDataBefore
            $mapsData['visualDataBefore'][] = [
                'id' => $dataItem->id,
                'visualData' => $visualData,
                'fileName' => $dataItem->file_nmf,
                'perangkat' => $dataItem->perangkat,
                'status' => $dataItem->status,
            ];
        }

        // --- 4. PEMROSESAN DATA 'AFTER' ---
        foreach ($dataAfter as $dataItem) {
            $visualData = [];

            try {
                $nmfPath = public_path('uploads/perjalanan/' . $dataItem->file_nmf);
                $dataSinyal = $this->parseNmfSinyal($nmfPath, $dataItem->id); 

                foreach ($dataSinyal as $sinyal) {
                    if ($sinyal['latitude'] === null || $sinyal['longitude'] === null) {
                        continue; 
                    }

                    $visualData[] = [
                        'id'        => $dataItem->id,
                        'latitude'  => (float) $sinyal['latitude'],
                        'longitude' => (float) $sinyal['longitude'],
                        'rsrp'      => $sinyal['rsrp'] ?? null,
                        'rsrq'      => $sinyal['rsrq'] ?? null,
                        'sinr'      => $sinyal['sinr'] ?? null,
                        'pci'       => $sinyal['pci'] ?? null,
                    ];
                }
            } catch (\Exception $e) {
                Log::error("Gagal memproses file NMF [After] untuk ID: " . $dataItem->id . ". Error: " . $e->getMessage());
                continue;
            }
            
            // Tambahkan data log ini ke array visualDataAfter
            $mapsData['visualDataAfter'][] = [
                'id' => $dataItem->id,
                'visualData' => $visualData,
                'fileName' => $dataItem->file_nmf,
                'perangkat' => $dataItem->perangkat,
                'status' => $dataItem->status,
            ];
        }

        // Tambahkan koordinat pusat ke mapsData
        $mapsData['centerCoords'] = $centerCoords;
        
        // 5. Kirim ke View: Sekarang mapsData berisi 3 kunci utama: visualDataBefore, visualDataAfter, dan centerCoords.
        return view('telkominfra-show', [
            'perjalananDetail' => $perjalanan,
            'mapsData' => $mapsData,
        ]);
    }

    

    public function store(Request $request)
    {
        // --- 1. VALIDASI ---
        try {
            $validatedData = $request->validate([
                // Mengubah kembali validasi ke id_perjalanan (string UUID/Sesi) dan membuatnya nullable.
                'id_perjalanan' => 'nullable|string|max:255', 
                'nama_pengguna' => 'required|string|max:255', 
                'nama_tempat' => 'required|string|max:255', 
                'nmf_file' => 'required|file|mimes:txt,nmf|max:51200',
                'status' => 'required|in:Before,After',
            ]);
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        // --- 2. PREPARASI AWAL & PINDahkan FILE SEMENTARA ---
        $tempUniqueFileName = null; // Nama file sementara
        $idPerjalananInput = $validatedData['id_perjalanan'] ?? '';
        $folderPath = 'uploads/perjalanan';
        $destinationPath = public_path($folderPath);
        $fileExtension = $request->file('nmf_file')->getClientOriginalExtension();

        try {
            // A. Tentukan ID Sesi Awal (UUID jika input kosong)
            $idPerjalananStore = ($idPerjalananInput == '') ? Str::uuid()->toString() : $idPerjalananInput;

            // B. Pindah File ke nama unik SEMENTARA (UUID + ekstensi)
            $file = $request->file('nmf_file');
            $tempUniqueFileName = $idPerjalananStore . '.' . $fileExtension;
            
            $file->move($destinationPath, $tempUniqueFileName); 
            $oldPath = $destinationPath . DIRECTORY_SEPARATOR . $tempUniqueFileName; 
            
            // C. Parse Header NMF
            $nmfHeaderData = $this->parseNmfHeader($oldPath); // Menggunakan path sementara
            $perangkat = $nmfHeaderData['perangkat'] ?? 'Unknown Device';
            
            // D. FINAL ID SESI: Tentukan ID Induk yang akan digunakan untuk Find/Create.
            $finalIdPerjalanan = null;

            // 1. Cek Prioritas ID dari Form: Validasi apakah ID input ada di DB
            if (!empty($idPerjalananInput) && Perjalanan::where('id_perjalanan', $idPerjalananInput)->exists()) {
                // Kasus 1: User memasukkan ID yang valid (sudah ada di DB). Gunakan ID ini untuk Find.
                $finalIdPerjalanan = $idPerjalananInput;
            }

            // // 2. Cek Prioritas ID dari Header NMF: Hanya jika ID belum ditentukan di langkah 1
            // if (empty($finalIdPerjalanan) && isset($nmfHeaderData['id_perjalanan'])) {
            //     // Kasus 2: Input form kosong/tidak valid. Gunakan ID dari NMF header.
            //     $finalIdPerjalanan = $nmfHeaderData['id_perjalanan'];
            // }
            
            // 3. Fallback ke ID Baru: Hanya jika ID belum ditentukan di langkah 1 atau 2
            if (empty($finalIdPerjalanan)) {
                // Kasus 3: Input form kosong/tidak valid DAN NMF header tidak ada ID. 
                // Gunakan UUID baru yang sudah dibuat di langkah 2A (Create).
                $finalIdPerjalanan = $idPerjalananStore;
            }

            // --- 3. TRANSAKSI DATABASE (CARI ATAU BUAT INDUK & BUAT ANAK) ---
            $results = DB::transaction(function () use ($validatedData, $finalIdPerjalanan, $perangkat, $tempUniqueFileName) {
                
                // 1. CARI atau BUAT Record Perjalanan (Find or Create)
                $perjalanan = Perjalanan::firstOrNew(['id_perjalanan' => $finalIdPerjalanan]);

                if (!$perjalanan->exists) {
                    $perjalanan->nama_pengguna = $validatedData['nama_pengguna'];
                    $perjalanan->nama_tempat = $validatedData['nama_tempat'];
                    $perjalanan->save();
                }
                
                // 2. Buat Record PerjalananData (Anak)
                // Simpan dengan nama file SEMENTARA. ID (PK) sekarang tersedia di $perjalananData->id.
                $perjalananData = PerjalananData::create([
                    'perjalanan_id' => $perjalanan->id, 
                    'perangkat' => $perangkat,
                    'file_nmf' => $tempUniqueFileName,
                    'status' => $validatedData['status'],
                ]);

                return [
                    'perjalanan' => $perjalanan,
                    'perjalananData' => $perjalananData
                ];
            });

            $perjalanan = $results['perjalanan'];
            $perjalananData = $results['perjalananData'];
            $perjalananId = $perjalanan->id;
            
            // --- 4. GANTI NAMA FILE DISK & PERBARUI DATABASE (POST-COMMIT) ---
            
            // Format Nama File Final: [ID PerjalananData]_[ID Sesi Perjalanan].[ekstensi]
            $finalFileName = $perjalananData->id . '_' . $perjalanan->id_perjalanan . '.' . $fileExtension;
            $newPath = $destinationPath . DIRECTORY_SEPARATOR . $finalFileName;
            
            // Pindahkan/ganti nama file SEMENTARA menjadi file PERMANEN
            if (File::exists($oldPath)) {
                File::move($oldPath, $newPath);

                // Update record PerjalananData dengan nama file yang baru dan permanen
                $perjalananData->file_nmf = $finalFileName;
                $perjalananData->save();
            } else {
                // Jika file hilang setelah transaksi, log warning.
                Log::warning("File temporer hilang setelah commit DB: " . $oldPath);
            }

            // --- 5. REDIRECT SUKSES ---
            return redirect()->route('maintenance.show', $perjalananId)
                ->with('success', 'Data log berhasil ditambahkan. File: ' . $finalFileName . ' di Perjalanan ID Sesi: ' . $perjalanan->id_perjalanan);

        } catch (\Exception $e) {
            // --- 6. ROLLBACK FILE & ERROR LOG ---
            // Gunakan path file SEMENTARA untuk rollback.
            $fileToDeletePath = public_path($folderPath . DIRECTORY_SEPARATOR . $tempUniqueFileName);
            if ($tempUniqueFileName && File::exists($fileToDeletePath)) {
                File::delete($fileToDeletePath);
            }
            
            Log::error("Gagal memproses unggahan file:", [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);
            
            return redirect()->back()->with('error', 'Gagal memproses data. Pesan: ' . $e->getMessage())->withInput();
        }
    }



    private function parseNmfHeader(string $nmfPath): array
    {
        $perangkat = 'Unknown Device';
        $idPerjalanan = Str::uuid()->toString();

        if (($handle = fopen($nmfPath, "r")) !== FALSE) {
            while (($line = fgets($handle)) !== FALSE) {
                $line = trim($line);

                if (str_starts_with($line, '#DN')) {
                    $parts = str_getcsv($line); 
                    if (isset($parts[3])) {
                        $perangkat = trim($parts[3], '"');
                    }
                }

                if (str_starts_with($line, '#ID')) {
                    $parts = str_getcsv($line);
                    if (isset($parts[3])) {
                        $idPerjalanan = trim($parts[3], '"');
                    }
                }
            }
            fclose($handle);
        }

        return [
            'perangkat' => $perangkat,
            'id_perjalanan' => $idPerjalanan,
        ];
    }

    private function parseGpxAndNmfGps(string $gpxPath, string $nmfPath, int $perjalananId): array
    {
        $dataGPS = [];
        try {
            $xml = simplexml_load_file($gpxPath);
            if ($xml === false) {
                 \Log::error("Gagal memuat file GPX.");
                 return [];
            }

            foreach ($xml->trk->trkseg->trkpt as $trkpt) {
                $lat = (float) $trkpt['lat'];
                $lon = (float) $trkpt['lon'];
                $time = (string) $trkpt->time;

                if ($lat !== 0.0 && $lon !== 0.0) {
                    $dataGPS[] = [
                        'perjalanan_id' => $perjalananId,
                        'timestamp_waktu' => Carbon::parse($time)->toDateTimeString(), 
                        'latitude' => $lat,
                        'longitude' => $lon,
                        'altitude' => (float) $trkpt->ele,
                        'sumber' => 'GPX',
                    ];
                }
            }
        } catch (\Exception $e) {
            \Log::error("Error saat parsing GPX:", ['message' => $e->getMessage()]);
        }
        return $dataGPS; 
    }


    private function parseNmfSinyal(string $nmfPath, int $perjalananId): array
    {
        $dataSinyal = [];
        $currentGps = [
            'lat' => null,
            'lon' => null,
        ]; 
        $lineCount = 0;
        $logDate = null; 
        
        $delimiter = ','; 

        $convertTime = function (?string $rawTime, ?string $logDate): ?string {
             if (!$rawTime || !$logDate) return null;
             try {
                 $dateTimeString = $logDate . ' ' . $rawTime;
                 return Carbon::createFromFormat('Y-m-d H:i:s.v', $dateTimeString)->format('Y-m-d H:i:s');
             } catch (\Exception $e) {
                 return null;
             }
        };

        if (($handle = fopen($nmfPath, "r")) !== FALSE) {
            while (($line = fgets($handle)) !== FALSE) {
                $lineCount++;
                $line = trim($line);

                if ($line === '' || str_starts_with($line, '#')) {
                    if (str_starts_with($line, '#START')) {
                        $parts = str_getcsv($line, $delimiter); 
                        $rawDate = $parts[3] ?? null; 
                        if ($rawDate) {
                            try {
                                $dateString = trim($rawDate, '"');
                                $logDate = Carbon::createFromFormat('d.m.Y', $dateString)->format('Y-m-d');
                            } catch (\Exception $e) {
                                $logDate = Carbon::now()->format('Y-m-d'); 
                            }
                        }
                    }
                    continue;
                }

                if (!$logDate) {
                    $logDate = Carbon::now()->format('Y-m-d'); 
                }

                $parts = str_getcsv($line, $delimiter); 

                if (str_starts_with($line, 'GPS')) {
                    if (isset($parts[3]) && isset($parts[4]) && $parts[3] !== '' && $parts[4] !== '') {
                        $currentGps['lat'] = (float)$parts[4];
                        $currentGps['lon'] = (float)$parts[3];
                    } else {
                        $currentGps['lat'] = null;
                        $currentGps['lon'] = null;
                    }
                    
                    continue; 
                }

                if (str_starts_with($line, 'CELLMEAS')) {
                    try {
                        $rawTime = $parts[1] ?? null;

                        $lat = $currentGps['lat'];
                        $lon = $currentGps['lon'];

                        $timestampWaktu = $convertTime($rawTime, $logDate); 

                        if ($lat === null || $lon === null) {
                            Log::warning("CELLMEAS baris $lineCount tidak memiliki koordinat dari GPS sebelumnya.");
                        }

                        $dataSinyal[] = [
                            'perjalanan_id'    => $perjalananId,
                            'timestamp_waktu'  => $timestampWaktu,
                            'teknologi'        => 'LTE', 
                            'earfcn'           => (int)($parts[8] ?? null), 
                            'pci'              => (int)($parts[9] ?? null), 
                            'rsrp'             => (float)($parts[10] ?? null), 
                            'rsrq'             => (float)($parts[11] ?? null), 
                            'sinr'             => (float)($parts[12] ?? null), 
                            'latitude'         => $lat, 
                            'longitude'        => $lon, 
                            'cell_id'          => $parts[7] ?? null,
                        ];

                    } catch (\Exception $e) {
                        Log::warning("Gagal parsing baris CELLMEAS ke-$lineCount: " . $e->getMessage());
                    }
                }
            }

            fclose($handle);
        }
        return $dataSinyal;
    }




    public function edit(string $id) { /* ... */ }
     /**
     * Update data sesi perjalanan (nama_pengguna dan nama_tempat).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id ID dari Perjalanan yang akan diupdate.
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            // 1. Validasi Input
            $request->validate([
                'nama_pengguna' => 'required|string|max:255',
                'nama_tempat' => 'required|string|max:255',
            ], [
                'nama_pengguna.required' => 'Nama Pengguna wajib diisi.',
                'nama_tempat.required' => 'Nama Tempat/Lokasi wajib diisi.',
            ]);

            // 2. Temukan data perjalanan utama
            // Menggunakan findOrFail untuk memastikan data ada sebelum update
            $perjalanan = Perjalanan::findOrFail($id);

            // 3. Update data
            $perjalanan->nama_pengguna = $request->nama_pengguna;
            $perjalanan->nama_tempat = $request->nama_tempat;
            $perjalanan->save();

            Log::info("Perjalanan ID: {$id} berhasil diperbarui.");

            // 4. Redirect kembali ke halaman show dengan notifikasi sukses
            return redirect()->route('maintenance.show', $perjalanan->id)
                             ->with('success', 'Detail Perjalanan (Nama Pengguna & Lokasi) berhasil diperbarui.');

        } catch (ValidationException $e) {
            // Tangani kegagalan validasi
            return redirect()->back()
                             ->withErrors($e->errors())
                             ->withInput();
        } catch (\Exception $e) {
            // Tangani semua kesalahan lain
            Log::error('Gagal memperbarui perjalanan ID: ' . $id . '. Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui data. Terjadi kesalahan server.');
        }
    }



    /**
     * Hapus sesi perjalanan dan data terkait (PerjalananData dan file log).
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $perjalanan = null;
        try {
            // 1. Temukan data perjalanan utama
            $perjalanan = Perjalanan::findOrFail($id);
            Log::info("Mencoba menghapus Perjalanan ID: {$id}");

            // Menggunakan transaksi untuk memastikan operasi database atomic
            DB::transaction(function () use ($perjalanan) {
                $basePath = public_path('uploads/perjalanan/');

                // 2. Ambil semua data log terkait (PerjalananData)
                // Mengambil secara langsung dari model PerjalananData menggunakan perjalanan_id
                $perjalananDatas = PerjalananData::where('perjalanan_id', $perjalanan->id)->get(); 
                
                // 3. Hapus file fisik untuk SETIAP log data (file_nmf dan file_gpx)
                if ($perjalananDatas->isNotEmpty()) { 
                    foreach ($perjalananDatas as $dataItem) {
                        // Hapus file_nmf
                        if ($dataItem->file_nmf && File::exists($basePath . $dataItem->file_nmf)) {
                            File::delete($basePath . $dataItem->file_nmf);
                            Log::debug('File NMF dihapus: ' . $dataItem->file_nmf);
                        }
                        
                        // Hapus file_gpx (Saya lihat ini tidak ada di kode Anda sebelumnya, tapi penting untuk dihapus)
                        if ($dataItem->file_gpx && File::exists($basePath . $dataItem->file_gpx)) {
                            File::delete($basePath . $dataItem->file_gpx);
                            Log::debug('File GPX dihapus: ' . $dataItem->file_gpx);
                        }
                    }
                } else {
                    Log::warning('Tidak ada data log PerjalananData yang ditemukan untuk ID Perjalanan: ' . $perjalanan->id);
                }


                // 4. Hapus data log (PerjalananData) dari database secara eksplisit
                $deletedLogCount = PerjalananData::where('perjalanan_id', $perjalanan->id)->delete();
                Log::info("Data log PerjalananData berhasil dihapus. Jumlah baris: {$deletedLogCount}");
                // dd($deletedLogCount); // UNCOMMENT INI UNTUK DEBUG JUMLAH BARIS YANG DIHAPUS

                // 5. Hapus data sesi perjalanan utama (Perjalanan)
                $perjalanan->delete();
                Log::info('Sesi Perjalanan utama berhasil dihapus.');
            });

            // Beri notifikasi sukses dan arahkan kembali ke halaman index
            return redirect()->route('maintenance.index')->with('success', 'Sesi Drive Test dan semua data (termasuk file log) berhasil dihapus!');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Ditangani jika Perjalanan tidak ditemukan
            Log::warning("Percobaan hapus gagal: Perjalanan ID {$id} tidak ditemukan.");
            return redirect()->route('maintenance.index')->with('error', 'Data yang ingin dihapus tidak ditemukan.');
        } catch (\Exception $e) {
            // Tangani semua kesalahan lain (kegagalan file, relasi, dll.)
            Log::error('Gagal menghapus perjalanan ID: ' . $id . '. Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            
            // Beri notifikasi error dan arahkan kembali ke halaman index
            return redirect()->route('maintenance.index')->with('error', 'Gagal menghapus data. Terjadi kesalahan server. Detail error dicatat.');
        }
    }

    /**
     * Hapus satu data log (PerjalananData) dan file terkait.
     * Metode ini dipanggil oleh route 'perjalanan.dataDestroy'.
     *
     * @param  int  $id ID dari PerjalananData yang akan dihapus.
     * @return \Illuminate\Http\Response
     */
    public function destroyPerjalananData($id)
    {
        $perjalananData = null;
        try {
            // 1. Temukan data log (PerjalananData)
            $perjalananData = PerjalananData::findOrFail($id);
            Log::info("Mencoba menghapus satu log PerjalananData ID: {$id}");

            // Simpan ID perjalanan utama untuk redirect yang benar
            $perjalananId = $perjalananData->perjalanan_id;

            // Menggunakan transaksi untuk memastikan operasi database atomic
            DB::transaction(function () use ($perjalananData) {
                $basePath = public_path('uploads/perjalanan/');

                // 2. Hapus file fisik (file_nmf dan file_gpx)
                
                // Hapus file_nmf
                if ($perjalananData->file_nmf && File::exists($basePath . $perjalananData->file_nmf)) {
                    File::delete($basePath . $perjalananData->file_nmf);
                    Log::debug('File NMF dihapus: ' . $perjalananData->file_nmf);
                }
                
                // Hapus file_gpx
                if ($perjalananData->file_gpx && File::exists($basePath . $perjalananData->file_gpx)) {
                    File::delete($basePath . $perjalananData->file_gpx);
                    Log::debug('File GPX dihapus: ' . $perjalananData->file_gpx);
                }

                // 3. Hapus data log dari database
                $perjalananData->delete();
                Log::info('Satu data log PerjalananData berhasil dihapus.');
            });

            // Beri notifikasi sukses dan arahkan kembali ke halaman detail perjalanan
            return redirect()->route('maintenance.show', $perjalananId)->with('success', 'Satu log data dan file terkait berhasil dihapus!');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Ditangani jika PerjalananData tidak ditemukan
            Log::warning("Percobaan hapus log gagal: PerjalananData ID {$id} tidak ditemukan.");
            // Redirect ke halaman index jika ID perjalanan tidak diketahui
            return redirect()->route('maintenance.index')->with('error', 'Log data yang ingin dihapus tidak ditemukan.');
        } catch (\Exception $e) {
            // Tangani semua kesalahan lain
            Log::error('Gagal menghapus log data ID: ' . $id . '. Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            
            // Beri notifikasi error dan arahkan kembali. Coba menggunakan referer jika show route gagal.
            return redirect()->back()->with('error', 'Gagal menghapus log data. Terjadi kesalahan server. Detail error dicatat.');
        }
    }
}