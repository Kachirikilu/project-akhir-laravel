<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Perjalanan;
use App\Models\PengukuranSinyal; 
use Carbon\Carbon; // Digunakan untuk manipulasi timestamp
use Illuminate\Support\Facades\Log;

class TelkominfraController extends Controller
{
    /**
     * Tampilkan view input data.
     */

    // public function index()
    // {
    //     $visualData = DB::table('pengukuran_sinyal')
    //         ->join('titik_gps', function ($join) {
    //             $join->on('pengukuran_sinyal.perjalanan_id', '=', 'titik_gps.perjalanan_id')
    //                  ->on('pengukuran_sinyal.timestamp_waktu', '=', 'titik_gps.timestamp_waktu');
    //         })
    //         ->select(
    //             'titik_gps.latitude',
    //             'titik_gps.longitude',
    //             'pengukuran_sinyal.rsrp',
    //             'pengukuran_sinyal.rsrq',
    //             'pengukuran_sinyal.sinr',
    //             'pengukuran_sinyal.pci'
    //         )
    //         ->whereNotNull('pengukuran_sinyal.rsrq') 
    //         ->whereNotNull('pengukuran_sinyal.rsrp')
    //         ->get();
            
    //     return view('telkominfra', [
    //         'visualData' => $visualData,
    //     ]);
    // }


    public function index()
    {
        $perjalanans = Perjalanan::all();

        $allData = [];

        foreach ($perjalanans as $perjalanan) {
            $nmfPath = public_path('uploads/perjalanan/' . $perjalanan->file_nmf);
            $gpxPath = public_path('uploads/perjalanan/' . $perjalanan->file_gpx);

            if (!file_exists($nmfPath) || !file_exists($gpxPath)) {
                continue;
            }

            $dataGPS = $this->parseGpxAndNmfGps($gpxPath, $nmfPath, $perjalanan->id);
            $dataSinyal = $this->parseNmfSinyal($nmfPath, $perjalanan->id);

            $visualData = [];
            foreach ($dataSinyal as $i => $sinyal) {
                $gps = $dataGPS[$i % max(1, count($dataGPS))] ?? null;
                if (!$gps) continue;

                $visualData[] = [
                    'latitude'  => $gps['latitude'],
                    'longitude' => $gps['longitude'],
                    'rsrp'      => $sinyal['rsrp'],
                    'rsrq'      => $sinyal['rsrq'],
                    'sinr'      => $sinyal['sinr'],
                    'pci'       => $sinyal['pci'],
                ];
            }

            $midIndex = intval(count($visualData) / 2);
            $centerCoords = [
                $visualData[$midIndex]['latitude'] ?? -2.9105859,
                $visualData[$midIndex]['longitude'] ?? 104.8536157,
            ];

        }
        return view('telkominfra', [
            'centerCoords' => $centerCoords,
            'visualData' => $visualData,
            'perjalananDetail' => $perjalanan
        ]);
    }

    // public function show(string $id) 
    // {
    //     $perjalanan = Perjalanan::where('id', $id)->firstOrFail();

    //     $nmfPath = public_path('uploads/perjalanan/' . $perjalanan->file_nmf);
    //     $gpxPath = public_path('uploads/perjalanan/' . $perjalanan->file_gpx);

    //     $dataGPS = $this->parseGpxAndNmfGps($gpxPath, $nmfPath, $perjalanan->id);
    //     $dataSinyal = $this->parseNmfSinyal($nmfPath, $perjalanan->id);

    //     $visualData = [];
    //     foreach ($dataSinyal as $i => $sinyal) {
    //         $gps = $dataGPS[$i % max(1, count($dataGPS))] ?? null;
    //         if (!$gps) continue;

    //         $visualData[] = [
    //             'latitude'  => $sinyal['latitude'],
    //             'longitude' => $sinyal['longitude'],
    //             'rsrp'      => $sinyal['rsrp'],
    //             'rsrq'      => $sinyal['rsrq'],
    //             'sinr'      => $sinyal['sinr'],
    //             'pci'       => $sinyal['pci'],
    //         ];
    //     }


    //     // dd($visualData);

    //     $midIndex = intval(count($visualData) / 2);

    //     $centerCoords = [
    //         $visualData[$midIndex]['latitude'] ?? -2.9105859,
    //         $visualData[$midIndex]['longitude'] ?? 104.8536157,
    //     ];

    //     return view('telkominfra', [
    //         'centerCoords' => $centerCoords,
    //         'visualData' => $visualData,
    //         'perjalananDetail' => $perjalanan
    //     ]);
    // }
    public function show(string $id) 
    {
        $perjalanan = Perjalanan::where('id', $id)->firstOrFail();
        $nmfPath = public_path('uploads/perjalanan/' . $perjalanan->file_nmf);
        $dataSinyal = $this->parseNmfSinyal($nmfPath, $perjalanan->id); // <--- KOORDINAT SUDAH DI SINI
        $visualData = [];
        
        foreach ($dataSinyal as $sinyal) {
            
            if ($sinyal['latitude'] === null || $sinyal['longitude'] === null) {
                continue; 
            }

            $visualData[] = [
                'latitude'  => $sinyal['latitude'],
                'longitude' => $sinyal['longitude'],
                'rsrp'      => $sinyal['rsrp'],
                'rsrq'      => $sinyal['rsrq'],
                'sinr'      => $sinyal['sinr'],
                'pci'       => $sinyal['pci'],
            ];
        }

        if (empty($visualData)) {
            $centerCoords = [-2.9105859, 104.8536157]; 
        } else {
            $midIndex = intval(count($visualData) / 2);

            $centerCoords = [
                $visualData[$midIndex]['latitude'],
                $visualData[$midIndex]['longitude'],
            ];
        }
        
        // dd($visualData); // Sekarang dd() ini seharusnya menunjukkan koordinat!

        return view('telkominfra', [
            'centerCoords' => $centerCoords,
            'visualData' => $visualData,
            'perjalananDetail' => $perjalanan
        ]);
    }




    public function store(Request $request)
    {
        $request->validate([
            'nama_pengguna' => 'required|string|max:255',
            'gpx_file' => 'required|file|mimes:gpx,xml|max:10240',
            'nmf_file' => 'required|file|mimes:txt,nmf|max:51200',
        ]);

        $namaPengguna = $request->nama_pengguna;

        DB::beginTransaction();

        try {
            $idPerjalanan = Str::uuid()->toString();

            $nmfName = $idPerjalanan . '.nmf';
            $gpxName = $idPerjalanan . '.gpx';

            $nmfFilePath = $request->file('nmf_file')->move(public_path('uploads/perjalanan'), $nmfName);
            $gpxFilePath = $request->file('gpx_file')->move(public_path('uploads/perjalanan'), $gpxName);

            $nmfHeaderData = $this->parseNmfHeader($nmfFilePath);

            $perangkat = $nmfHeaderData['perangkat'] ?? 'Unknown Device';
            $idPerjalanan = $nmfHeaderData['id_perjalanan'] ?? $idPerjalanan;

            $perjalanan = Perjalanan::create([
                'id_perjalanan' => $idPerjalanan,
                'nama_pengguna' => $namaPengguna,
                'perangkat' => $perangkat,
                'file_nmf' => $nmfName,
                'file_gpx' => $gpxName,
                'timestamp_mulai' => now(), 
            ]);
            
            $perjalananId = $perjalanan->id;

            // --- Parsing file dengan path permanen ---
            $dataGPS = $this->parseGpxAndNmfGps($gpxFilePath, $nmfFilePath, $perjalananId);
            $dataSinyal = $this->parseNmfSinyal($nmfFilePath, $perjalananId);
            
            if (!empty($dataGPS)) {
                DB::table('titik_gps')->insert($dataGPS);
            } else {
                \Log::warning("Batch Insert Titik GPS diabaikan: Array kosong.");
            }

            if (!empty($dataSinyal)) {
                DB::table('pengukuran_sinyal')->insert($dataSinyal);
            } else {
                \Log::warning("Batch Insert Pengukuran Sinyal diabaikan: Array kosong.");
            }

            DB::commit();

            return redirect()->route('maintenance.show', $perjalananId)
                ->with('success', 'Data berhasil disimpan. Total GPS: ' . count($dataGPS) . ', Total Sinyal: ' . count($dataSinyal));

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Gagal memproses unggahan file:", ['error' => $e->getMessage()]);
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

//     private function parseNmfSinyal(string $nmfPath, int $perjalananId): array
//     {
//         $dataSinyal = [];
//         $lineCount = 0;

//         if (($handle = fopen($nmfPath, "r")) !== FALSE) {
//             while (($line = fgets($handle)) !== FALSE) {
//             $lineCount++;
//             $line = trim($line);

//             if ($line === '' || str_starts_with($line, '#')) {
//                 continue;
//             }

//             if (str_starts_with($line, 'CELLMEAS') && substr_count($line, 'GPS')) {
//                 $parts = str_getcsv($line);

//                 try {
//                 $rawTime = $parts[1] ?? null;
//                 $timestamp = null;

//                 if ($rawTime && strlen($rawTime) === 14) { 
//                     $timestamp = Carbon::createFromFormat('YmdHis', $rawTime)->toDateTimeString();
//                 } elseif ($rawTime && is_numeric($rawTime)) {
//                     $timestamp = Carbon::createFromTimestampMs((int)$rawTime)->toDateTimeString();
//                 } elseif ($rawTime) {
//                     try {
//                         $timestamp = Carbon::createFromFormat('H:i:s.v', $rawTime)->toDateTimeString();
//                     } catch (\Exception $e) {
//                         \Log::warning("Format waktu tidak dikenali di baris $lineCount: ".$rawTime);
//                     }
//                 }
//                     $dataSinyal[] = [
//                         'perjalanan_id'    => $perjalananId,
//                         'timestamp_waktu'  => $timestamp,
//                         'teknologi'        => 'LTE',
//                         'earfcn'           => (int)($parts[2] ?? null),
//                         'pci'              => (int)($parts[3] ?? null),
//                         'rsrp'             => (float)($parts[10] ?? null),
//                         'rsrq'             => (float)($parts[11] ?? null),
//                         'sinr'             => (float)($parts[12] ?? null),
//                         'latitude'         => isset($parts[5]) ? (float)$parts[5] : null,
//                         'longitude'        => isset($parts[6]) ? (float)$parts[6] : null,
//                         'cqi'              => $parts[13] ?? null,
//                         'cell_id'          => $parts[7] ?? null,
//                     ];
//                 } catch (\Exception $e) {
//                     \Log::warning("Gagal parsing baris NMF ke-$lineCount", [
//                         'line' => $line,
//                         'error' => $e->getMessage()
//                     ]);
//                 }
//             }
//         }
//         fclose($handle);
//     }

//     return $dataSinyal;
// }


// private function parseNmfSinyal(string $nmfPath, int $perjalananId): array
// {
//     $dataSinyal = [];
//     $gpsData = []; // simpan GPS berdasarkan timestamp
//     $lineCount = 0;

//     if (($handle = fopen($nmfPath, "r")) !== FALSE) {
//         while (($line = fgets($handle)) !== FALSE) {
//             $lineCount++;
//             $line = trim($line);

//             if ($line === '' || str_starts_with($line, '#')) {
//                 continue;
//             }

//             $parts = str_getcsv($line);

//             // --- Parsing GPS line ---
//             if (str_starts_with($line, 'GPS')) {
//                 $rawTime = $parts[1] ?? null;
//                 $timestamp = null;

//                 if ($rawTime && strlen($rawTime) === 14) { 
//                     $timestamp = Carbon::createFromFormat('YmdHis', $rawTime)->toDateTimeString();
//                 } elseif ($rawTime && is_numeric($rawTime)) {
//                     $timestamp = Carbon::createFromTimestampMs((int)$rawTime)->toDateTimeString();
//                 } elseif ($rawTime) {
//                     try {
//                         $timestamp = Carbon::createFromFormat('H:i:s.v', $rawTime)->toDateTimeString();
//                     } catch (\Exception $e) {
//                         \Log::warning("Format waktu GPS tidak dikenali di baris $lineCount: ".$rawTime);
//                     }
//                 }

//                 if ($timestamp) {
//                     $gpsData[$timestamp] = [
//                         'lat' => isset($parts[4]) ? (float)$parts[4] : null,
//                         'lon' => isset($parts[3]) ? (float)$parts[3] : null,
//                         'alt' => isset($parts[5]) ? (float)$parts[5] : null,
//                     ];
//                 }
//                 continue;
//             }

//             // --- Parsing CELLMEAS line ---
//             if (str_starts_with($line, 'CELLMEAS')) {
//                 try {
//                     $rawTime = $parts[1] ?? null;
//                     $timestamp = null;

//                     if ($rawTime && strlen($rawTime) === 14) { 
//                         $timestamp = Carbon::createFromFormat('YmdHis', $rawTime)->toDateTimeString();
//                     } elseif ($rawTime && is_numeric($rawTime)) {
//                         $timestamp = Carbon::createFromTimestampMs((int)$rawTime)->toDateTimeString();
//                     } elseif ($rawTime) {
//                         try {
//                             $timestamp = Carbon::createFromFormat('H:i:s.v', $rawTime)->toDateTimeString();
//                         } catch (\Exception $e) {
//                             \Log::warning("Format waktu CELLMEAS tidak dikenali di baris $lineCount: ".$rawTime);
//                         }
//                     }

//                     $lat = $gpsData[$timestamp]['lat'] ?? null;
//                     $lon = $gpsData[$timestamp]['lon'] ?? null;

//                     $dataSinyal[] = [
//                         'perjalanan_id'    => $perjalananId,
//                         'timestamp_waktu'  => $timestamp,
//                         'teknologi'        => 'LTE',
//                         'earfcn'           => (int)($parts[2] ?? null),
//                         'pci'              => (int)($parts[3] ?? null),
//                         'rsrp'             => (float)($parts[10] ?? null),
//                         'rsrq'             => (float)($parts[11] ?? null),
//                         'sinr'             => (float)($parts[12] ?? null),
//                         'latitude'         => $lat,
//                         'longitude'        => $lon,
//                         // 'cqi'              => $parts[13] ?? null,
//                         'cell_id'          => $parts[7] ?? null,
//                     ];
//                     dd($dataSinyal);
//                 } catch (\Exception $e) {
//                     \Log::warning("Gagal parsing baris CELLMEAS ke-$lineCount", [
//                         'line' => $line,
//                         'error' => $e->getMessage()
//                     ]);
//                 }
//             }
//         }
//         fclose($handle);
//     }

//     return $dataSinyal;
// }



 private function parseNmfSinyal(string $nmfPath, int $perjalananId): array
    {
        $dataSinyal = [];
        // Variabel untuk menyimpan data GPS yang paling baru ditemukan
        $currentGps = [
            'lat' => null,
            'lon' => null,
        ]; 
        $lineCount = 0;
        $logDate = null; 
        
        // Delimiter yang digunakan di file NMF Anda adalah koma (',') berdasarkan data sampel
        $delimiter = ','; 

        // Fungsi Pembantu untuk Konversi Waktu (Hanya digunakan untuk kolom 'timestamp_waktu' hasil)
        $convertTime = function (?string $rawTime, ?string $logDate): ?string {
             if (!$rawTime || !$logDate) return null;
             try {
                 $dateTimeString = $logDate . ' ' . $rawTime;
                 // Ambil objek Carbon lalu format ke Y-m-d H:i:s (standar DB)
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
    public function update(Request $request, string $id) { /* ... */ }
    public function destroy(string $id) { /* ... */ }
}