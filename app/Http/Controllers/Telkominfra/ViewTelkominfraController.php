<?php

namespace App\Http\Controllers\Telkominfra;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Perjalanan;
use App\Models\DataPerjalanan;
use App\Models\PengukuranSinyal;
use App\Models\KeluhPengguna;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File; 
use App\Http\Controllers\Telkominfra\FileTelkominfraController;
use Illuminate\Support\LazyCollection;
use Illuminate\Http\JsonResponse;

class ViewTelkominfraController extends Controller
{

    public function perjalanan(Request $request)
    {
        $search = $request->input('search');
        $searchMode = $request->input('mode', ''); 
        $query = Perjalanan::query();

        // 1. LOGIKA FILTER MODE
        if (isset($searchMode) && $searchMode !== '') {
            $query->where('selesai', (int)$searchMode);
        }
        
        // 2. LOGIKA PENCARIAN
        if ($search) {
            $parsedDate = null;
            try {
                $parsedDate = Carbon::parse($search);
            } catch (\Exception $e) {
                $parsedDate = null;
            }

            $query->where(function ($q) use ($search, $parsedDate) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('id_perjalanan', 'like', "%{$search}%")
                    ->orWhere('nama_pengguna', 'like', "%{$search}%")
                    ->orWhere('nama_tempat', 'like', "%{$search}%");

                if ($parsedDate) {
                    $q->orWhereBetween('created_at', [
                        $parsedDate->copy()->startOfDay(),
                        $parsedDate->copy()->endOfDay(),
                    ]);
                }
            });
        }

        // 3. PAGINASI DAN DATA STATISTIK
        $perjalanans = $query->orderBy('created_at', 'desc')->paginate(20);
        $perjalanans->appends(['search' => $search, 'mode' => $searchMode]);

        $totalPerjalanan = Perjalanan::count();
        $perjalananSelesai = Perjalanan::where('selesai', true)->count();
        $perjalananBelumSelesai = Perjalanan::where('selesai', false)->count();

         return [
            'perjalanans' => $perjalanans,
            'search' => $search,
            'searchMode' => $searchMode,
            'totalPerjalanan' => $totalPerjalanan,
            'perjalananSelesai' => $perjalananSelesai,
            'perjalananBelumSelesai' => $perjalananBelumSelesai,
        ];
    }
    public function index(Request $request)
    {
        $data = $this->perjalanan($request);
        return view('telkominfra', $data);
    }

    public function ajaxSearch(Request $request)
    {
        $search = $request->input('search');
        $mode = $request->input('mode');
        $query = Perjalanan::query();
        
        if (isset($mode) && $mode !== '') {
            $query->where('selesai', (int)$mode);
        }
        if ($search) {
            $parsedDate = null;
            try {
                $parsedDate = Carbon::parse($search);
            } catch (\Exception $e) {
                $parsedDate = null;
            }

            $query->where(function ($q) use ($search, $parsedDate) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('id_perjalanan', 'like', "%{$search}%")
                    ->orWhere('nama_pengguna', 'like', "%{$search}%")
                    ->orWhere('nama_tempat', 'like', "%{$search}%");

                if ($parsedDate) {
                    $q->orWhereBetween('created_at', [
                        $parsedDate->copy()->startOfDay(),
                        $parsedDate->copy()->endOfDay(),
                    ]);
                }
            });
        }
        $perjalanans = $query->orderBy('created_at', 'desc')->take(10)->get([
            'id', 'id_perjalanan', 'selesai', 'nama_tempat', 'nama_pengguna', 'created_at'
        ]);

        return response()->json($perjalanans);
    }

    public function comentSearch(Request $request): JsonResponse
    {
        $keyword = $request->get('q', '');

        $results = KeluhPengguna::whereNull('perjalanan_id') 
            ->where(function ($query) use ($keyword) {
                $query->where('nama_tempat', 'LIKE', "%{$keyword}%")
                    ->orWhere('komentar', 'LIKE', "%{$keyword}%")
                    ->orWhere('nama_pengguna', 'LIKE', "%{$keyword}%")
                    ->orWhere('id', 'LIKE', "%{$keyword}%");
            })
            ->limit(10)
            ->get(['id', 'nama_pengguna', 'nama_tempat', 'komentar', 'created_at']); 

        return response()->json($results);
    }


    public function show(Request $request, string $id)
    {
        $perjalanan = Perjalanan::findOrFail($id);
        $dataPerjalanans = DataPerjalanan::where('perjalanan_id', $perjalanan->id)->get();

        $mapsData = [];
        $mergedData = [
            'Before' => [],
            'After'  => [],
        ];

        $avgAccumulators = [
            'Before' => [
                'rsrp_sum' => 0, 'rsrp_count' => 0,
                'rssi_sum' => 0, 'rssi_count' => 0,
                'rsrq_sum' => 0, 'rsrq_count' => 0,
                'sinr_sum' => 0, 'sinr_count' => 0,
            ],
            'After'  => [
                'rsrp_sum' => 0, 'rsrp_count' => 0,
                'rssi_sum' => 0, 'rssi_count' => 0,
                'rsrq_sum' => 0, 'rsrq_count' => 0,
                'sinr_sum' => 0, 'sinr_count' => 0,
            ],
        ];

        $individualMaps = [];

        // === LOOP UNTUK SETIAP DATA PERJALANAN ===
        foreach ($dataPerjalanans as $dataItem) {
            $visualData = [];
            $centerCoords = [-2.9105859, 104.8536157];

            try {
                $dataSinyal = PengukuranSinyal::where('data_perjalanan_id', $dataItem->id)->lazy(1000);
                // $nmfPath = public_path('uploads/perjalanan/' . $dataItem->file_nmf);

                if (!empty($nmfPath) && File::exists($nmfPath)) {
                    $parser = new FileTelkominfraController();
                    $dataSinyal = $parser->parseNmfSinyal($nmfPath, $dataItem->id);
                }

                foreach ($dataSinyal as $sinyal) {
                    $latitudeData  = $sinyal['latitude'] ?? $sinyal->latitude ?? null;
                    $longitudeData = $sinyal['longitude'] ?? $sinyal->longitude ?? null;

                    if ($latitudeData === null || $longitudeData === null) {
                        continue;
                    }

                    $status = $dataItem->status;       // hanya dari perjalanan_data
                    $device = $dataItem->perangkat;    // hanya dari perjalanan_data

                    // --- Akumulasi rata-rata ---
                    if (isset($avgAccumulators[$status])) {
                        foreach (['rsrp', 'rssi', 'rsrq', 'sinr'] as $metric) {
                            $value = $sinyal[$metric] ?? $sinyal->$metric ?? null;
                            if ($value !== null) {
                                $avgAccumulators[$status]["{$metric}_sum"] += (float)$value;
                                $avgAccumulators[$status]["{$metric}_count"]++;
                            }
                        }
                    }

                    // --- Format waktu ---
                    $rawTimestamp = $sinyal['timestamp_waktu'] ?? $sinyal->timestamp_waktu ?? null;
                    $formattedTimestamp = null;
                    if ($rawTimestamp) {
                        try {
                            $carbonTime = Carbon::parse($rawTimestamp);
                            $formattedTimestamp = $carbonTime->format('H:i:s d/m/y');
                        } catch (\Exception $e) {
                            $formattedTimestamp = 'Invalid Time';
                        }
                    }


                    // --- Susun data titik untuk peta ---
                    $point = [
                        'id'        => $dataItem->id,
                        'latitude'  => (float)$latitudeData,
                        'longitude' => (float)$longitudeData,
                        'rsrp'      => $sinyal['rsrp'] ?? $sinyal->rsrp ?? null,
                        'rssi'      => $sinyal['rssi'] ?? $sinyal->rssi ?? null,
                        'rsrq'      => $sinyal['rsrq'] ?? $sinyal->rsrq ?? null,
                        'sinr'      => $sinyal['sinr'] ?? $sinyal->sinr ?? null,
                        'cell_id'   => $sinyal['cell_id'] ?? $sinyal->cell_id ?? null,
                        'pci'       => $sinyal['pci'] ?? $sinyal->pci ?? null,
                        'earfcn'    => $sinyal['earfcn'] ?? $sinyal->earfcn ?? null,
                        'band'      => $sinyal['band'] ?? $sinyal->band ?? null,
                        'frekuensi' => $sinyal['frekuensi'] ?? $sinyal->frekuensi ?? null,
                        'bandwidth' => $sinyal['bandwidth'] ?? $sinyal->bandwidth ?? null,
                        'n_value'   => $sinyal['n_value'] ?? $sinyal->n_value ?? null,
                        'timestamp_waktu' => $formattedTimestamp,
                        'device'    => $device,
                        'status'    => $status,
                    ];

                    $visualData[] = $point;
                    $mergedData[$status][] = $point;
                }

                // 🔹 Hitung koordinat tengah
                if (!empty($visualData)) {
                    $latitudes = array_column($visualData, 'latitude');
                    $longitudes = array_column($visualData, 'longitude');
                    $centerCoords = [
                        (min($latitudes) + max($latitudes)) / 2,
                        (min($longitudes) + max($longitudes)) / 2,
                    ];
                }
            } catch (\Exception $e) {
                Log::error("Gagal mengambil data sinyal dari DB untuk dataPerjalanan ID: {$dataItem->id}. Error: " . $e->getMessage());
            }

            // 🔹 Simpan hasil setiap status (Before / After)
            $individualMaps[] = [
                'id' => $dataItem->id,
                'centerCoords' => $centerCoords,
                'visualData' => $visualData,
                'fileName' => $dataItem->file_nmf,
                'perangkat' => $dataItem->perangkat,
                'status' => $dataItem->status,
            ];
        }

        // === TAMBAHKAN DATA GABUNGAN (BEFORE & AFTER) ===
        $mergedMaps = [];
        foreach (['Before', 'After'] as $status) {
            if (!empty($mergedData[$status])) {
                $latitudes = array_column($mergedData[$status], 'latitude');
                $longitudes = array_column($mergedData[$status], 'longitude');
                $centerCoords = [
                    (min($latitudes) + max($latitudes)) / 2,
                    (min($longitudes) + max($longitudes)) / 2,
                ];

                $fileName = $status === 'Before' ? 'Sebelum' : 'Sesudah';
                $mergedMaps[] = [
                    'id' => "Merged_{$status}",
                    'centerCoords' => $centerCoords,
                    'visualData' => $mergedData[$status],
                    'fileName' => "Gabungan Data {$fileName} Maintenance",
                    'perangkat' => 'Semua Perangkat',
                    'status' => $status,
                ];
            }
        }

        // === GABUNGKAN SEMUA DATA UNTUK VIEW ===
        $mapsData = array_merge($mergedMaps, $individualMaps);

        // === HITUNG RATA-RATA SIGNAL ===
        $signalAverages = [];
        foreach ($avgAccumulators as $status => $data) {
            $signalAverages[$status] = [
                'rsrp_avg' => $data['rsrp_count'] > null ? round($data['rsrp_sum'] / $data['rsrp_count'], 2) : null,
                'rssi_avg' => $data['rssi_count'] > null ? round($data['rssi_sum'] / $data['rssi_count'], 2) : null,
                'rsrq_avg' => $data['rsrq_count'] > null ? round($data['rsrq_sum'] / $data['rsrq_count'], 2) : null,
                'sinr_avg' => $data['sinr_count'] > null ? round($data['sinr_sum'] / $data['sinr_count'], 2) : null,
            ];
        }
        
        // === API Response (jika JSON) ===
        if ($request->wantsJson() || $request->is('api/*')) {
            $payload = [
                'status' => 'success',
                'perjalanan' => $perjalanan,
                'maps_data' => $mapsData,
                'signal_averages' => $signalAverages,
            ];
            return response()->json($payload);
        }

        $komentarTerhubung = KeluhPengguna::where('perjalanan_id', $perjalanan->id)
        ->orderBy('created_at', 'desc')
        ->get();
    
        $komentarBelumTerhubung = KeluhPengguna::whereNull('perjalanan_id')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        // === View Blade ===
        return view('telkominfra-show', [
            'perjalananDetail' => $perjalanan,
            'mapsData' => $mapsData,
            'signalAverages' => $signalAverages,
            'komentarTerhubung' => $komentarTerhubung,
            'komentarBelumTerhubung' => $komentarBelumTerhubung,
        ]);
    }

}