<?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Str;
// use App\Models\Perjalanan;
// use App\Models\PerjalananData;
// use Carbon\Carbon;
// use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\File; 

// class TelkominfraController extends Controller
// {
//     // public function index()
//     // {
//     //     $perjalanans = Perjalanan::all();

//     //     $allData = [];

//     //     foreach ($perjalanans as $perjalanan) {
//     //         $nmfPath = public_path('uploads/perjalanan/' . $perjalanan->file_nmf);
//     //         $gpxPath = public_path('uploads/perjalanan/' . $perjalanan->file_gpx);

//     //         if (!file_exists($nmfPath) || !file_exists($gpxPath)) {
//     //             continue;
//     //         }

//     //         $dataGPS = $this->parseGpxAndNmfGps($gpxPath, $nmfPath, $perjalanan->id);
//     //         $dataSinyal = $this->parseNmfSinyal($nmfPath, $perjalanan->id);

//     //         $visualData = [];
//     //         foreach ($dataSinyal as $i => $sinyal) {
//     //             $gps = $dataGPS[$i % max(1, count($dataGPS))] ?? null;
//     //             if (!$gps) continue;

//     //             $visualData[] = [
//     //                 'latitude'  => $gps['latitude'],
//     //                 'longitude' => $gps['longitude'],
//     //                 'rsrp'      => $sinyal['rsrp'],
//     //                 'rsrq'      => $sinyal['rsrq'],
//     //                 'sinr'      => $sinyal['sinr'],
//     //                 'pci'       => $sinyal['pci'],
//     //             ];
//     //         }

//     //         $midIndex = intval(count($visualData) / 2);
//     //         $centerCoords = [
//     //             $visualData[$midIndex]['latitude'],
//     //             $visualData[$midIndex]['longitude'],
//     //         ];

//     //     }
//     //     return view('telkominfra', [
//     //         'id_perjalanan' => null,
//     //         'centerCoords' => $centerCoords ?? [-2.9105859, 104.8536157],
//     //         'visualData' => $visualData ?? [],
//     //         'perjalananDetail' => $perjalanan ?? null,
//     //     ]);
//     // }

//     public function index(Request $request)
//     {
//         $search = $request->input('search');
//         $query = Perjalanan::query();

//         if ($search) {
//             $parsedDate = null;
            
//             try {
//                 $parsedDate = Carbon::parse($search);
//             } catch (\Exception $e) {
//                 $parsedDate = null;
//             }

//             $query->where(function ($q) use ($search, $parsedDate) {
//                 $q->where('id', 'like', '%' . $search . '%')
//                   ->where('id_perjalanan', 'like', '%' . $search . '%')
//                   ->orWhere('nama_pengguna', 'like', '%' . $search . '%')
//                   ->orWhere('nama_tempat', 'like', '%' . $search . '%');
//                 if ($parsedDate) {
//                     $q->orWhereBetween('created_at', [
//                         $parsedDate->copy()->startOfDay(), 
//                         $parsedDate->copy()->endOfDay(),
//                     ]);
//                 }
//             });
//         }

//         $perjalanans = $query->paginate(10);
//         $perjalanans->appends(['search' => $search]);
//         return view('telkominfra', [
//             'perjalanans' => $perjalanans, 
//             'search' => $search,
//         ]);
//     }


//   public function show(string $id) 
//     {
//         $perjalanan = Perjalanan::where('id', $id)->firstOrFail();
//         $perjalananDatas = PerjalananData::where('perjalanan_id', $perjalanan->id)->get(); 
        
//         $mapsData = []; 

//         $avgAccumulators = [
//             'Before' => [
//                 'rsrp_sum' => null, 'rsrp_count' => null,
//                 'rsrq_sum' => null, 'rsrq_count' => null,
//                 'sinr_sum' => null, 'sinr_count' => null,
//             ],
//             'After'  => [
//                 'rsrp_sum' => null, 'rsrp_count' => null,
//                 'rsrq_sum' => null, 'rsrq_count' => null,
//                 'sinr_sum' => null, 'sinr_count' => null,
//             ],
//         ];

//         foreach ($perjalananDatas as $dataItem) {
//             $visualData = [];
//             $centerCoords = [-2.9105859, 104.8536157];

//             try {
//                 $nmfPath = public_path('uploads/perjalanan/' . $dataItem->file_nmf);
//                 $dataSinyal = $this->parseNmfSinyal($nmfPath, $dataItem->id); 

//                 foreach ($dataSinyal as $sinyal) {
                    
//                     if ($sinyal['latitude'] === null || $sinyal['longitude'] === null) {
//                         continue; 
//                     }
                    
//                     $status = $dataItem->status;
//                     if (isset($avgAccumulators[$status])) {
//                         if (isset($sinyal['rsrp'])) {
//                             $avgAccumulators[$status]['rsrp_sum'] += (float) $sinyal['rsrp'];
//                             $avgAccumulators[$status]['rsrp_count']++;
//                         }
//                         if (isset($sinyal['rsrq'])) {
//                             $avgAccumulators[$status]['rsrq_sum'] += (float) $sinyal['rsrq'];
//                             $avgAccumulators[$status]['rsrq_count']++;
//                         }
//                         if (isset($sinyal['sinr'])) {
//                             $avgAccumulators[$status]['sinr_sum'] += (float) $sinyal['sinr'];
//                             $avgAccumulators[$status]['sinr_count']++;
//                         }
//                     }
//                     // --- Akhir Akumulasi ---

//                     $rawTimestamp = $sinyal['timestamp_waktu'] ?? null;
//                     $formattedTimestamp = null;

//                     if ($rawTimestamp) {
//                         try {
//                             $carbonTime = Carbon::parse($rawTimestamp);
//                             $formattedTimestamp = $carbonTime->format('H:i:s d M Y'); 
                            
//                         } catch (\Exception $e) {
//                             $formattedTimestamp = 'Invalid Time';
//                         }
//                     }

//                     $visualData[] = [
//                         'id'        => $dataItem->id,
//                         'latitude'  => (float) $sinyal['latitude'],
//                         'longitude' => (float) $sinyal['longitude'],
//                         'rsrp'      => $sinyal['rsrp'] ?? null,
//                         'rsrq'      => $sinyal['rsrq'] ?? null,
//                         'sinr'      => $sinyal['sinr'] ?? null,
//                         'pci'       => $sinyal['pci'] ?? null,
//                         'earfcn'   => $sinyal['earfcn'] ?? null,
//                         'band'     => $sinyal['band'] ?? null,
//                         'frekuensi' => $sinyal['frekuensi'] ?? null,
//                         'timestamp_waktu' => $formattedTimestamp,
//                     ];
//                 }
//                 if (!empty($visualData)) {
//                     $midIndex = intval(count($visualData) / 2);
//                     $centerCoords = [
//                         $visualData[$midIndex]['latitude'],
//                         $visualData[$midIndex]['longitude'],
//                     ];
//                 }
//             } catch (\Exception $e) {
//                 Log::error("Gagal memproses file NMF untuk PerjalananData ID: " . $dataItem->id . ". Error: " . $e->getMessage());
//             }

//             $mapsData[] = [
//                 'id' => $dataItem->id,
//                 'centerCoords' => $centerCoords,
//                 'visualData' => $visualData,
//                 'fileName' => $dataItem->file_nmf,
//                 'perangkat' => $dataItem->perangkat,
//                 'status' => $dataItem->status,
//             ];
//         }

//         $signalAverages = [];
//         foreach ($avgAccumulators as $status => $data) {
//             $signalAverages[$status] = [
//                 'rsrp_avg' => $data['rsrp_count'] > 0 ? round($data['rsrp_sum'] / $data['rsrp_count'], 2) : 0,
//                 'rsrq_avg' => $data['rsrq_count'] > 0 ? round($data['rsrq_sum'] / $data['rsrq_count'], 2) : 0,
//                 'sinr_avg' => $data['sinr_count'] > 0 ? round($data['sinr_sum'] / $data['sinr_count'], 2) : 0,
//             ];
//         }

//         $isBeforeEmpty = $avgAccumulators['Before']['rsrp_count'] === null &&
//                          $avgAccumulators['Before']['rsrq_count'] === null &&
//                          $avgAccumulators['Before']['sinr_count'] === null;

//         $isAfterEmpty = $avgAccumulators['After']['rsrp_count'] === null &&
//                         $avgAccumulators['After']['rsrq_count'] === null &&
//                         $avgAccumulators['After']['sinr_count'] === null;

//         if ($isBeforeEmpty && !$isAfterEmpty) {
//             $signalAverages['Before'] = $signalAverages['After'];
//             Log::info("Data 'Before' kosong, menggunakan data 'After' sebagai fallback.");

//         } elseif ($isAfterEmpty && !$isBeforeEmpty) {
//             $signalAverages['After'] = $signalAverages['Before'];
//             Log::info("Data 'After' kosong, menggunakan data 'Before' sebagai fallback.");
            
//         } elseif ($isAfterEmpty && $isBeforeEmpty) {
//             Log::warning("Data 'Before' dan 'After' kosong untuk perbandingan sinyal.");
//         }
        
//         // --- END: LOGIKA FALLBACK UNTUK DATA YANG HILANG ---
        
//         return view('telkominfra-show', [
//             'perjalananDetail' => $perjalanan,
//             'mapsData' => $mapsData, 
//             'signalAverages' => $signalAverages,
//         ]);
//     }

    

//     // public function store(Request $request)
//     // {
//     //     try {
//     //         $validatedData = $request->validate([
//     //             'id_perjalanan' => 'nullable|string|max:255', 
//     //             'nama_pengguna' => 'required|string|max:255', 
//     //             'nama_tempat' => 'required|string|max:255', 
//     //             'nmf_file' => 'required|file|mimes:txt,nmf|max:51200',
//     //             'status' => 'required|in:Before,After',
//     //         ]);
//     //     } catch (ValidationException $e) {
//     //         return redirect()->back()->withErrors($e->errors())->withInput();
//     //     }

//     //     // --- 2. PREPARASI AWAL & PINDahkan FILE SEMENTARA ---
//     //     $tempUniqueFileName = null; // Nama file sementara
//     //     $idPerjalananInput = $validatedData['id_perjalanan'] ?? '';
//     //     $folderPath = 'uploads/perjalanan';
//     //     $destinationPath = public_path($folderPath);
//     //     $fileExtension = $request->file('nmf_file')->getClientOriginalExtension();

//     //     try {
//     //         // A. Tentukan ID Sesi Awal (UUID jika input kosong)
//     //         $idPerjalananStore = ($idPerjalananInput == '') ? Str::uuid()->toString() : $idPerjalananInput;

//     //         // B. Pindah File ke nama unik SEMENTARA (UUID + ekstensi)
//     //         $file = $request->file('nmf_file');
//     //         $tempUniqueFileName = $idPerjalananStore . '.' . $fileExtension;
            
//     //         $file->move($destinationPath, $tempUniqueFileName); 
//     //         $oldPath = $destinationPath . DIRECTORY_SEPARATOR . $tempUniqueFileName; 
            
//     //         // C. Parse Header NMF
//     //         $nmfHeaderData = $this->parseNmfHeader($oldPath); // Menggunakan path sementara
//     //         $perangkat = $nmfHeaderData['perangkat'] ?? 'Unknown Device';
            
//     //         $finalIdPerjalanan = null;

//     //         if (!empty($idPerjalananInput) && Perjalanan::where('id_perjalanan', $idPerjalananInput)->exists()) {
//     //             $finalIdPerjalanan = $idPerjalananInput;
//     //         }

//     //         if (empty($finalIdPerjalanan)) {
//     //             $finalIdPerjalanan = $idPerjalananStore;
//     //         }

//     //         $results = DB::transaction(function () use ($validatedData, $finalIdPerjalanan, $perangkat, $tempUniqueFileName) {
//     //             $perjalanan = Perjalanan::firstOrNew(['id_perjalanan' => $finalIdPerjalanan]);

//     //             if (!$perjalanan->exists) {
//     //                 $perjalanan->nama_pengguna = $validatedData['nama_pengguna'];
//     //                 $perjalanan->nama_tempat = $validatedData['nama_tempat'];
//     //                 $perjalanan->save();
//     //             }
                
//     //             $perjalananData = PerjalananData::create([
//     //                 'perjalanan_id' => $perjalanan->id, 
//     //                 'perangkat' => $perangkat,
//     //                 'file_nmf' => $tempUniqueFileName,
//     //                 'status' => $validatedData['status'],
//     //             ]);

//     //             return [
//     //                 'perjalanan' => $perjalanan,
//     //                 'perjalananData' => $perjalananData
//     //             ];
//     //         });

//     //         $perjalanan = $results['perjalanan'];
//     //         $perjalananData = $results['perjalananData'];
//     //         $perjalananId = $perjalanan->id;

//     //         $finalFileName = $perjalananData->id . '_' . $perjalanan->id_perjalanan . '.' . $fileExtension;
//     //         $newPath = $destinationPath . DIRECTORY_SEPARATOR . $finalFileName;
            
//     //         if (File::exists($oldPath)) {
//     //             File::move($oldPath, $newPath);
//     //             $perjalananData->file_nmf = $finalFileName;
//     //             $perjalananData->save();
//     //         } else {
//     //             Log::warning("File temporer hilang setelah commit DB: " . $oldPath);
//     //         }

//     //         return redirect()->route('maintenance.show', $perjalananId)
//     //             ->with('success', 'Data log berhasil ditambahkan. File: ' . $finalFileName . ' di Perjalanan ID Sesi: ' . $perjalanan->id_perjalanan);

//     //     } catch (\Exception $e) {
//     //         $fileToDeletePath = public_path($folderPath . DIRECTORY_SEPARATOR . $tempUniqueFileName);
//     //         if ($tempUniqueFileName && File::exists($fileToDeletePath)) {
//     //             File::delete($fileToDeletePath);
//     //         }
            
//     //         Log::error("Gagal memproses unggahan file:", [
//     //             'error' => $e->getMessage(),
//     //             'line' => $e->getLine(),
//     //         ]);
            
//     //         return redirect()->back()->with('error', 'Gagal memproses data. Pesan: ' . $e->getMessage())->withInput();
//     //     }
//     // }

//      public function store(Request $request)
//     {
//         try {
//             $validatedData = $request->validate([
//                 'id_perjalanan' => 'nullable|string|max:255', 
//                 'nama_pengguna' => 'required|string|max:255', 
//                 'nama_tempat' => 'required|string|max:255', 
//                 'nmf_file' => 'required|file|mimes:txt,nmf|max:51200',
//                 'status' => 'required|in:Before,After',
//             ]);
//         } catch (ValidationException $e) {
//             return redirect()->back()->withErrors($e->errors())->withInput();
//         }

//         $tempUniqueFileName = null;
//         $idPerjalananInput = $validatedData['id_perjalanan'] ?? '';
//         $folderPath = 'uploads/perjalanan';
//         $destinationPath = public_path($folderPath);
//         $fileExtension = $request->file('nmf_file')->getClientOriginalExtension();

//         try {
//             $idPerjalananStore = ($idPerjalananInput == '') ? Str::uuid()->toString() : $idPerjalananInput;

//             $file = $request->file('nmf_file');
//             $tempUniqueFileName = $idPerjalananStore . '.' . $fileExtension;
//             $file->move($destinationPath, $tempUniqueFileName); 
//             $oldPath = $destinationPath . DIRECTORY_SEPARATOR . $tempUniqueFileName; 
            
//             $nmfHeaderData = $this->parseNmfHeader($oldPath); 
//             $nmfTimes = $this->extractNmfTimes($oldPath); 
//             $perangkat = $nmfHeaderData['perangkat'] ?? 'Unknown Device';
            
//             $finalIdPerjalanan = null;

//             if (!empty($idPerjalananInput) && Perjalanan::where('id_perjalanan', $idPerjalananInput)->exists()) {
//                 $finalIdPerjalanan = $idPerjalananInput;
//             }

//             if (empty($finalIdPerjalanan)) {
//                 $finalIdPerjalanan = $idPerjalananStore;
//             }

//             $results = DB::transaction(function () use ($validatedData, $finalIdPerjalanan, $perangkat, $tempUniqueFileName, $nmfTimes) {
//                 $perjalanan = Perjalanan::firstOrNew(['id_perjalanan' => $finalIdPerjalanan]);

//                 if (!$perjalanan->exists) {
//                     $perjalanan->nama_pengguna = $validatedData['nama_pengguna'];
//                     $perjalanan->nama_tempat = $validatedData['nama_tempat'];
//                     $perjalanan->save();
//                 }
                
//                 $perjalananData = PerjalananData::create([
//                     'perjalanan_id' => $perjalanan->id, 
//                     'perangkat' => $perangkat,
//                     'file_nmf' => $tempUniqueFileName,
//                     'status' => $validatedData['status'],
//                     'timestamp_mulai' => $nmfTimes['timestamp_mulai'],
//                     'timestamp_selesai' => $nmfTimes['timestamp_selesai'],
//                 ]);

//                 return [
//                     'perjalanan' => $perjalanan,
//                     'perjalananData' => $perjalananData
//                 ];
//             });

//             $perjalanan = $results['perjalanan'];
//             $perjalananData = $results['perjalananData'];
//             $perjalananId = $perjalanan->id;

//             $finalFileName = $perjalananData->id . '_' . $perjalanan->id_perjalanan . '.' . $fileExtension;
//             $newPath = $destinationPath . DIRECTORY_SEPARATOR . $finalFileName;
            
//             if (File::exists($oldPath)) {
//                 File::move($oldPath, $newPath);
//                 $perjalananData->file_nmf = $finalFileName;
//                 $perjalananData->save();
//             } else {
//                 Log::warning("File temporer hilang setelah commit DB: " . $oldPath);
//             }

//             return redirect()->route('maintenance.show', $perjalananId)
//                 ->with('success', 'Data log berhasil ditambahkan. File: ' . $finalFileName . ' di Perjalanan ID Sesi: ' . $perjalanan->id_perjalanan);

//         } catch (\Exception $e) {
//             $fileToDeletePath = public_path($folderPath . DIRECTORY_SEPARATOR . $tempUniqueFileName);
//             if ($tempUniqueFileName && File::exists($fileToDeletePath)) {
//                 File::delete($fileToDeletePath);
//             }
            
//             Log::error("Gagal memproses unggahan file:", [
//                 'error' => $e->getMessage(),
//                 'line' => $e->getLine(),
//             ]);
            
//             return redirect()->back()->with('error', 'Gagal memproses data. Pesan: ' . $e->getMessage())->withInput();
//         }
//     }



//     private function parseNmfHeader(string $nmfPath): array
//     {
//         $perangkat = 'Unknown Device';
//         $idPerjalanan = Str::uuid()->toString();

//         if (($handle = fopen($nmfPath, "r")) !== FALSE) {
//             while (($line = fgets($handle)) !== FALSE) {
//                 $line = trim($line);

//                 if (str_starts_with($line, '#DN')) {
//                     $parts = str_getcsv($line); 
//                     if (isset($parts[3])) {
//                         $perangkat = trim($parts[3], '"');
//                     }
//                 }

//                 if (str_starts_with($line, '#ID')) {
//                     $parts = str_getcsv($line);
//                     if (isset($parts[3])) {
//                         $idPerjalanan = trim($parts[3], '"');
//                     }
//                 }
//             }
//             fclose($handle);
//         }

//         return [
//             'perangkat' => $perangkat,
//             'id_perjalanan' => $idPerjalanan,
//         ];
//     }

//     private function parseGpxAndNmfGps(string $gpxPath, string $nmfPath, int $perjalananId): array
//     {
//         $dataGPS = [];
//         try {
//             $xml = simplexml_load_file($gpxPath);
//             if ($xml === false) {
//                  \Log::error("Gagal memuat file GPX.");
//                  return [];
//             }

//             foreach ($xml->trk->trkseg->trkpt as $trkpt) {
//                 $lat = (float) $trkpt['lat'];
//                 $lon = (float) $trkpt['lon'];
//                 $time = (string) $trkpt->time;

//                 if ($lat !== 0.0 && $lon !== 0.0) {
//                     $dataGPS[] = [
//                         'perjalanan_id' => $perjalananId,
//                         'timestamp_waktu' => Carbon::parse($time)->toDateTimeString(), 
//                         'latitude' => $lat,
//                         'longitude' => $lon,
//                         'altitude' => (float) $trkpt->ele,
//                         'sumber' => 'GPX',
//                     ];
//                 }
//             }
//         } catch (\Exception $e) {
//             \Log::error("Error saat parsing GPX:", ['message' => $e->getMessage()]);
//         }
//         return $dataGPS; 
//     }




//      private function convertRawHeaderTime(string $rawTime, string $rawDate): ?string
//     {
//         try {
//             // Hilangkan tanda kutip ganda dari tanggal jika ada
//             $dateString = trim($rawDate, '"');
            
//             // Gabungkan tanggal dan waktu mentah
//             $dateTimeString = $dateString . ' ' . $rawTime;
            
//             // Format input: 'd.m.Y H:i:s.v' -> Format output: 'Y-m-d H:i:s'
//             return Carbon::createFromFormat('d.m.Y H:i:s.v', $dateTimeString)->format('Y-m-d H:i:s');
//         } catch (\Exception $e) {
//             // Gagal parsing, kembalikan null
//             return null;
//         }
//     }

//     /**
//      * Membaca file NMF untuk mencari tag #START dan #STOP.
//      * * @param string $nmfPath Path ke file NMF.
//      * @return array Mengembalikan array ['timestamp_mulai', 'timestamp_selesai'].
//      */
//     private function extractNmfTimes(string $nmfPath): array
//     {
//         $times = [
//             'timestamp_mulai' => null,
//             'timestamp_selesai' => null,
//         ];
        
//         if (!File::exists($nmfPath)) {
//             Log::warning("File NMF tidak ditemukan untuk ekstraksi waktu: $nmfPath");
//             return $times;
//         }

//         if (($handle = fopen($nmfPath, "r")) !== FALSE) {
//             $delimiter = ',';
//             while (($line = fgets($handle)) !== FALSE) {
//                 $line = trim($line);

//                 if (str_starts_with($line, '#START') || str_starts_with($line, '#STOP')) {
//                     $parts = str_getcsv($line, $delimiter);

//                     $rawTime = $parts[1] ?? null; // Waktu (e.g., 10:01:54.955)
//                     $rawDate = $parts[3] ?? null; // Tanggal (e.g., "27.01.2006")

//                     if ($rawTime && $rawDate) {
//                         $mysqlTimestamp = $this->convertRawHeaderTime($rawTime, $rawDate);
                        
//                         if (str_starts_with($line, '#START')) {
//                             $times['timestamp_mulai'] = $mysqlTimestamp;
//                         } elseif (str_starts_with($line, '#STOP')) {
//                             $times['timestamp_selesai'] = $mysqlTimestamp;
//                         }
//                     }
//                 }
                
//                 // Hentikan pembacaan segera setelah kedua waktu ditemukan (asumsi di header)
//                 if ($times['timestamp_mulai'] !== null && $times['timestamp_selesai'] !== null) {
//                     break;
//                 }
//             }
//             fclose($handle);
//         }
        
//         return $times;
//     }


//     // private function parseNmfSinyal(string $nmfPath, int $perjalananId): array
//     // {
//     //     $dataSinyal = [];
//     //     $currentGps = [
//     //         'lat' => null,
//     //         'lon' => null,
//     //     ]; 
//     //     $lineCount = 0;
//     //     $logDate = null; 
        
//     //     $delimiter = ','; 

//     //     $convertTime = function (?string $rawTime, ?string $logDate): ?string {
//     //          if (!$rawTime || !$logDate) return null;
//     //          try {
//     //              $dateTimeString = $logDate . ' ' . $rawTime;
//     //              return Carbon::createFromFormat('Y-m-d H:i:s.v', $dateTimeString)->format('Y-m-d H:i:s');
//     //          } catch (\Exception $e) {
//     //              return null;
//     //          }
//     //     };

//     //     if (($handle = fopen($nmfPath, "r")) !== FALSE) {
//     //         while (($line = fgets($handle)) !== FALSE) {
//     //             $lineCount++;
//     //             $line = trim($line);

//     //             if ($line === '' || str_starts_with($line, '#')) {
//     //                 if (str_starts_with($line, '#START')) {
//     //                     $parts = str_getcsv($line, $delimiter); 
//     //                     $rawDate = $parts[3] ?? null; 
//     //                     if ($rawDate) {
//     //                         try {
//     //                             $dateString = trim($rawDate, '"');
//     //                             $logDate = Carbon::createFromFormat('d.m.Y', $dateString)->format('Y-m-d');
//     //                         } catch (\Exception $e) {
//     //                             $logDate = Carbon::now()->format('Y-m-d'); 
//     //                         }
//     //                     }
//     //                 }
//     //                 continue;
//     //             }

//     //             if (!$logDate) {
//     //                 $logDate = Carbon::now()->format('Y-m-d'); 
//     //             }

//     //             $parts = str_getcsv($line, $delimiter); 

//     //             if (str_starts_with($line, 'GPS')) {
//     //                 if (isset($parts[3]) && isset($parts[4]) && $parts[3] !== '' && $parts[4] !== '') {
//     //                     $currentGps['lat'] = (float)$parts[4];
//     //                     $currentGps['lon'] = (float)$parts[3];
//     //                 } else {
//     //                     $currentGps['lat'] = null;
//     //                     $currentGps['lon'] = null;
//     //                 }
                    
//     //                 continue; 
//     //             }

//     //             if (str_starts_with($line, 'CELLMEAS')) {
//     //                 try {
//     //                     $rawTime = $parts[1] ?? null;

//     //                     $lat = $currentGps['lat'];
//     //                     $lon = $currentGps['lon'];

//     //                     $timestampWaktu = $convertTime($rawTime, $logDate); 

//     //                     if ($lat === null || $lon === null) {
//     //                         Log::warning("CELLMEAS baris $lineCount tidak memiliki koordinat dari GPS sebelumnya.");
//     //                     }

//     //                     $dataSinyal[] = [
//     //                         'perjalanan_id'    => $perjalananId,
//     //                         'timestamp_waktu'  => $timestampWaktu,
//     //                         'teknologi'        => 'LTE', 
//     //                         'earfcn'           => (int)($parts[9] ?? null), 
//     //                         'pci'              => (int)($parts[10] ?? null), 
//     //                         'rsrq'             => (float)($parts[11] ?? null), 
//     //                         'rsrp'             => (float)($parts[12] ?? null), 
//     //                         'sinr'             => (float)($parts[13] ?? null), 
//     //                         'latitude'         => $lat, 
//     //                         'longitude'        => $lon, 
//     //                         'cell_id'          => $parts[7] ?? null,
//     //                     ];

//     //                 } catch (\Exception $e) {
//     //                     Log::warning("Gagal parsing baris CELLMEAS ke-$lineCount: " . $e->getMessage());
//     //                 }
//     //             }
//     //         }

//     //         fclose($handle);
//     //     }
//     //     return $dataSinyal;
//     // }

//      private function parseNmfSinyal(string $nmfPath, int $perjalananId): array
//     {
//         $dataSinyal = [];
//         $currentGps = [
//             'lat' => null,
//             'lon' => null,
//         ]; 
//         $lineCount = 0;
//         $logDate = null; 
        
//         $delimiter = ','; 

//         $convertTime = function (?string $rawTime, ?string $logDate): ?string {
//              if (!$rawTime || !$logDate) return null;
//              try {
//                 // $logDate sudah dalam format 'Y-m-d' dari #START
//                 $dateTimeString = $logDate . ' ' . $rawTime;
//                 // Menggunakan format input 'Y-m-d H:i:s.v' karena $logDate sudah diformat
//                 return Carbon::createFromFormat('Y-m-d H:i:s.v', $dateTimeString)->format('Y-m-d H:i:s');
//              } catch (\Exception $e) {
//                  return null;
//              }
//         };

//         // --- Logika Band/Frekuensi (Diintegrasikan dari respon sebelumnya) ---
//         $resolveBandFrequency = function (?int $earfcn) {
//             $band = null;
//             $frekuensi = null;

//             if ($earfcn !== null) {
//                 switch ($earfcn) {
//                     case 38750:
//                     case 38948: $band = 40; $frekuensi = 2300; break;
//                     case 19850: $band = 3;  $frekuensi = 1800; break;
//                     case 18500: $band = 1;  $frekuensi = 2100; break;
//                     case 900:   $band = 8;  $frekuensi = 900;  break;
//                     case 3500:  $band = 42; $frekuensi = 3500; break; // Band 42 asumsi untuk 3500
//                     case 1850:  $band = 10; $frekuensi = 2100; break; // Band 10
                    
//                     case 500: 
//                         $band = 13; 
//                         $frekuensi = 700; 
//                         break; 

//                     default:
//                         if ($earfcn >= 41590 && $earfcn <= 43589) { // Band 42 Range
//                             $band = 42;
//                             $frekuensi = 3500;
//                         } elseif ($earfcn >= 0 && $earfcn <= 599) { // Band 13 Range
//                             $band = 13;
//                             $frekuensi = 700;
//                         } else {
//                             $band = 'Unknown';
//                             $frekuensi = 'Unknown';
//                         }
//                         break;
//                 }
//             }
//             return ['band' => $band, 'frekuensi' => $frekuensi];
//         };
//         // --- Akhir Logika Band/Frekuensi ---

//         if (($handle = fopen($nmfPath, "r")) !== FALSE) {
//             while (($line = fgets($handle)) !== FALSE) {
//                 $lineCount++;
//                 $line = trim($line);

//                 if ($line === '' || str_starts_with($line, '#')) {
//                     if (str_starts_with($line, '#START')) {
//                         $parts = str_getcsv($line, $delimiter); 
//                         $rawDate = $parts[3] ?? null; 
//                         if ($rawDate) {
//                             try {
//                                 $dateString = trim($rawDate, '"');
//                                 $logDate = Carbon::createFromFormat('d.m.Y', $dateString)->format('Y-m-d');
//                             } catch (\Exception $e) {
//                                 $logDate = Carbon::now()->format('Y-m-d'); 
//                             }
//                         }
//                     }
//                     continue;
//                 }

//                 if (!$logDate) {
//                     $logDate = Carbon::now()->format('Y-m-d'); 
//                 }

//                 $parts = str_getcsv($line, $delimiter); 

//                 if (str_starts_with($line, 'GPS')) {
//                     if (isset($parts[3]) && isset($parts[4]) && $parts[3] !== '' && $parts[4] !== '') {
//                         $currentGps['lat'] = (float)$parts[4];
//                         $currentGps['lon'] = (float)$parts[3];
//                     } else {
//                         $currentGps['lat'] = null;
//                         $currentGps['lon'] = null;
//                     }
                    
//                     continue; 
//                 }

//                 if (str_starts_with($line, 'CELLMEAS')) {
//                     try {
//                         $rawTime = $parts[1] ?? null;
//                         $lat = $currentGps['lat'];
//                         $lon = $currentGps['lon'];
//                         $timestampWaktu = $convertTime($rawTime, $logDate); 
//                         $earfcn = (int)($parts[9] ?? null);
                        
//                         $bandFreq = $resolveBandFrequency($earfcn); // Panggil fungsi Band/Freq

//                         if ($lat === null || $lon === null) {
//                             Log::warning("CELLMEAS baris $lineCount tidak memiliki koordinat dari GPS sebelumnya.");
//                         }

//                         $dataSinyal[] = [
//                             'perjalanan_id'     => $perjalananId,
//                             'timestamp_waktu'   => $timestampWaktu,
//                             'teknologi'         => 'LTE', 
//                             'earfcn'            => $earfcn,
//                             'band'               => $bandFreq['band'],      // <<< BARU
//                             'frekuensi'         => $bandFreq['frekuensi'],// <<< BARU
//                             'pci'               => (int)($parts[10] ?? null), 
//                             'rsrq'              => (float)($parts[11] ?? null), 
//                             'rsrp'              => (float)($parts[12] ?? null), 
//                             'sinr'              => (float)($parts[13] ?? null), 
//                             'latitude'          => $lat, 
//                             'longitude'         => $lon, 
//                             'cell_id'           => $parts[7] ?? null,
//                         ];

//                     } catch (\Exception $e) {
//                         Log::warning("Gagal parsing baris CELLMEAS ke-$lineCount: " . $e->getMessage());
//                     }
//                 }
//             }

//             fclose($handle);
//         }
//         return $dataSinyal;
//     }




//     public function edit(string $id) { /* ... */ }
//      /**
//      * Update data sesi perjalanan (nama_pengguna dan nama_tempat).
//      *
//      * @param  \Illuminate\Http\Request  $request
//      * @param  int  $id ID dari Perjalanan yang akan diupdate.
//      * @return \Illuminate\Http\Response
//      */
//     public function update(Request $request, $id)
//     {
//         try {
//             // 1. Validasi Input
//             $request->validate([
//                 'nama_pengguna' => 'required|string|max:255',
//                 'nama_tempat' => 'required|string|max:255',
//             ], [
//                 'nama_pengguna.required' => 'Nama Pengguna wajib diisi.',
//                 'nama_tempat.required' => 'Nama Tempat/Lokasi wajib diisi.',
//             ]);

//             // 2. Temukan data perjalanan utama
//             // Menggunakan findOrFail untuk memastikan data ada sebelum update
//             $perjalanan = Perjalanan::findOrFail($id);

//             // 3. Update data
//             $perjalanan->nama_pengguna = $request->nama_pengguna;
//             $perjalanan->nama_tempat = $request->nama_tempat;
//             $perjalanan->save();

//             Log::info("Perjalanan ID: {$id} berhasil diperbarui.");

//             // 4. Redirect kembali ke halaman show dengan notifikasi sukses
//             return redirect()->route('maintenance.show', $perjalanan->id)
//                              ->with('success', 'Detail Perjalanan (Nama Pengguna & Lokasi) berhasil diperbarui.');

//         } catch (ValidationException $e) {
//             // Tangani kegagalan validasi
//             return redirect()->back()
//                              ->withErrors($e->errors())
//                              ->withInput();
//         } catch (\Exception $e) {
//             // Tangani semua kesalahan lain
//             Log::error('Gagal memperbarui perjalanan ID: ' . $id . '. Error: ' . $e->getMessage());
//             return redirect()->back()->with('error', 'Gagal memperbarui data. Terjadi kesalahan server.');
//         }
//     }



//     /**
//      * Hapus sesi perjalanan dan data terkait (PerjalananData dan file log).
//      *
//      * @param  int  $id
//      * @return \Illuminate\Http\Response
//      */
//     public function destroy($id)
//     {
//         $perjalanan = null;
//         try {
//             // 1. Temukan data perjalanan utama
//             $perjalanan = Perjalanan::findOrFail($id);
//             Log::info("Mencoba menghapus Perjalanan ID: {$id}");

//             // Menggunakan transaksi untuk memastikan operasi database atomic
//             DB::transaction(function () use ($perjalanan) {
//                 $basePath = public_path('uploads/perjalanan/');

//                 // 2. Ambil semua data log terkait (PerjalananData)
//                 // Mengambil secara langsung dari model PerjalananData menggunakan perjalanan_id
//                 $perjalananDatas = PerjalananData::where('perjalanan_id', $perjalanan->id)->get(); 
                
//                 // 3. Hapus file fisik untuk SETIAP log data (file_nmf dan file_gpx)
//                 if ($perjalananDatas->isNotEmpty()) { 
//                     foreach ($perjalananDatas as $dataItem) {
//                         // Hapus file_nmf
//                         if ($dataItem->file_nmf && File::exists($basePath . $dataItem->file_nmf)) {
//                             File::delete($basePath . $dataItem->file_nmf);
//                             Log::debug('File NMF dihapus: ' . $dataItem->file_nmf);
//                         }
                        
//                         // Hapus file_gpx (Saya lihat ini tidak ada di kode Anda sebelumnya, tapi penting untuk dihapus)
//                         if ($dataItem->file_gpx && File::exists($basePath . $dataItem->file_gpx)) {
//                             File::delete($basePath . $dataItem->file_gpx);
//                             Log::debug('File GPX dihapus: ' . $dataItem->file_gpx);
//                         }
//                     }
//                 } else {
//                     Log::warning('Tidak ada data log PerjalananData yang ditemukan untuk ID Perjalanan: ' . $perjalanan->id);
//                 }


//                 // 4. Hapus data log (PerjalananData) dari database secara eksplisit
//                 $deletedLogCount = PerjalananData::where('perjalanan_id', $perjalanan->id)->delete();
//                 Log::info("Data log PerjalananData berhasil dihapus. Jumlah baris: {$deletedLogCount}");
//                 // dd($deletedLogCount); // UNCOMMENT INI UNTUK DEBUG JUMLAH BARIS YANG DIHAPUS

//                 // 5. Hapus data sesi perjalanan utama (Perjalanan)
//                 $perjalanan->delete();
//                 Log::info('Sesi Perjalanan utama berhasil dihapus.');
//             });

//             // Beri notifikasi sukses dan arahkan kembali ke halaman index
//             return redirect()->route('maintenance.index')->with('success', 'Sesi Drive Test dan semua data (termasuk file log) berhasil dihapus!');

//         } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
//             // Ditangani jika Perjalanan tidak ditemukan
//             Log::warning("Percobaan hapus gagal: Perjalanan ID {$id} tidak ditemukan.");
//             return redirect()->route('maintenance.index')->with('error', 'Data yang ingin dihapus tidak ditemukan.');
//         } catch (\Exception $e) {
//             // Tangani semua kesalahan lain (kegagalan file, relasi, dll.)
//             Log::error('Gagal menghapus perjalanan ID: ' . $id . '. Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            
//             // Beri notifikasi error dan arahkan kembali ke halaman index
//             return redirect()->route('maintenance.index')->with('error', 'Gagal menghapus data. Terjadi kesalahan server. Detail error dicatat.');
//         }
//     }

//     /**
//      * Hapus satu data log (PerjalananData) dan file terkait.
//      * Metode ini dipanggil oleh route 'perjalanan.dataDestroy'.
//      *
//      * @param  int  $id ID dari PerjalananData yang akan dihapus.
//      * @return \Illuminate\Http\Response
//      */
//     public function destroyPerjalananData($id)
//     {
//         $perjalananData = null;
//         try {
//             // 1. Temukan data log (PerjalananData)
//             $perjalananData = PerjalananData::findOrFail($id);
//             Log::info("Mencoba menghapus satu log PerjalananData ID: {$id}");

//             // Simpan ID perjalanan utama untuk redirect yang benar
//             $perjalananId = $perjalananData->perjalanan_id;

//             // Menggunakan transaksi untuk memastikan operasi database atomic
//             DB::transaction(function () use ($perjalananData) {
//                 $basePath = public_path('uploads/perjalanan/');

//                 // 2. Hapus file fisik (file_nmf dan file_gpx)
                
//                 // Hapus file_nmf
//                 if ($perjalananData->file_nmf && File::exists($basePath . $perjalananData->file_nmf)) {
//                     File::delete($basePath . $perjalananData->file_nmf);
//                     Log::debug('File NMF dihapus: ' . $perjalananData->file_nmf);
//                 }
                
//                 // Hapus file_gpx
//                 if ($perjalananData->file_gpx && File::exists($basePath . $perjalananData->file_gpx)) {
//                     File::delete($basePath . $perjalananData->file_gpx);
//                     Log::debug('File GPX dihapus: ' . $perjalananData->file_gpx);
//                 }

//                 // 3. Hapus data log dari database
//                 $perjalananData->delete();
//                 Log::info('Satu data log PerjalananData berhasil dihapus.');
//             });

//             // Beri notifikasi sukses dan arahkan kembali ke halaman detail perjalanan
//             return redirect()->route('maintenance.show', $perjalananId)->with('success', 'Satu log data dan file terkait berhasil dihapus!');

//         } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
//             // Ditangani jika PerjalananData tidak ditemukan
//             Log::warning("Percobaan hapus log gagal: PerjalananData ID {$id} tidak ditemukan.");
//             // Redirect ke halaman index jika ID perjalanan tidak diketahui
//             return redirect()->route('maintenance.index')->with('error', 'Log data yang ingin dihapus tidak ditemukan.');
//         } catch (\Exception $e) {
//             // Tangani semua kesalahan lain
//             Log::error('Gagal menghapus log data ID: ' . $id . '. Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            
//             // Beri notifikasi error dan arahkan kembali. Coba menggunakan referer jika show route gagal.
//             return redirect()->back()->with('error', 'Gagal menghapus log data. Terjadi kesalahan server. Detail error dicatat.');
//         }
//     }
// }