<?php

namespace App\Http\Controllers\Telkominfra;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Perjalanan;
use App\Models\DataPerjalanan;
use App\Models\PengukuranSinyal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File; 
use App\Http\Controllers\Telkominfra\FileTelkominfraController;
use Illuminate\Support\LazyCollection; 

class DataTelkominfraController extends Controller
{

    // public function store(Request $request)
    // {
    //     try {
    //         $validatedData = $request->validate([
    //             'id_perjalanan' => 'nullable|string|max:255',
    //             'nama_pengguna' => 'required|string|max:255',
    //             'nama_tempat'   => 'required|string|max:255',
    //             'nmf_file'      => 'required',
    //             'nmf_file.*'    => 'file|mimes:txt,nmf|max:51200',
    //             'status'        => 'required|in:Before,After',
    //         ]);
    //     } catch (ValidationException $e) {
    //         return redirect()->back()->withErrors($e->errors())->withInput();
    //     }

    //     $files = $request->file('nmf_file');
    //     $idPerjalananInput = $validatedData['id_perjalanan'] ?? '';
    //     $folderPath = 'uploads/perjalanan';
    //     $destinationPath = public_path($folderPath);
    //     $parser = new FileTelkominfraController();

    //     // Buat perjalanan utama (hanya sekali)
    //     $idPerjalananStore = ($idPerjalananInput == '') ? Str::uuid()->toString() : $idPerjalananInput;
    //     $perjalanan = Perjalanan::firstOrCreate(
    //         ['id_perjalanan' => $idPerjalananStore],
    //         [
    //             'nama_pengguna' => $validatedData['nama_pengguna'],
    //             'nama_tempat' => $validatedData['nama_tempat'],
    //         ]
    //     );

    //     foreach ($files as $file) {
    //         try {
    //             $fileExtension = $file->getClientOriginalExtension();
    //             $tempUniqueFileName = $idPerjalananStore . '_' . Str::random(6) . '.' . $fileExtension;
    //             $file->move($destinationPath, $tempUniqueFileName);
    //             $oldPath = $destinationPath . DIRECTORY_SEPARATOR . $tempUniqueFileName;

    //             $nmfHeaderData = $parser->parseNmfHeader($oldPath);
    //             $nmfTimes = $parser->extractNmfTimes($oldPath);
    //             $perangkat = $nmfHeaderData['perangkat'] ?? 'Unknown Device';

    //             // Simpan data perjalanan_data
    //             $dataPerjalanan = DataPerjalanan::create([
    //                 'perjalanan_id' => $perjalanan->id,
    //                 'perangkat' => $perangkat,
    //                 'file_nmf' => $tempUniqueFileName,
    //                 'status' => $validatedData['status'],
    //                 'timestamp_mulai' => $nmfTimes['timestamp_mulai'],
    //                 'timestamp_selesai' => $nmfTimes['timestamp_selesai'],
    //             ]);

    //             $finalFileName = $dataPerjalanan->id . '_' . $perjalanan->id_perjalanan . '.' . $fileExtension;
    //             $newPath = $destinationPath . DIRECTORY_SEPARATOR . $finalFileName;

    //             if (File::exists($oldPath)) {
    //                 File::move($oldPath, $newPath);
    //                 $dataPerjalanan->file_nmf = $finalFileName;
    //                 $dataPerjalanan->save();
    //             }

    //             // Simpan data sinyal
    //             try {
    //                 $dataSinyal = $parser->parseNmfSinyal($newPath, $perjalanan->id);
    //                 if (!empty($dataSinyal)) {
    //                     foreach ($dataSinyal as &$item) {
    //                         $item['data_perjalanan_id'] = $dataPerjalanan->id;
    //                         unset($item['perjalanan_id']);
    //                     }

    //                     \App\Models\PengukuranSinyal::insert($dataSinyal);
    //                     Log::info("Berhasil menyimpan " . count($dataSinyal) . " data sinyal untuk file: " . $finalFileName);
    //                 } else {
    //                     Log::warning("Tidak ada data sinyal yang diparsing dari file: " . $finalFileName);
    //                 }
    //             } catch (\Exception $signalEx) {
    //                 Log::error("Gagal menyimpan data sinyal dari $finalFileName: " . $signalEx->getMessage());
    //             }
    //         } catch (\Exception $ex) {
    //             Log::error("Gagal memproses file: " . $file->getClientOriginalName(), [
    //                 'error' => $ex->getMessage(),
    //                 'line' => $ex->getLine(),
    //             ]);
    //             continue;
    //         }
    //     }

    //     return redirect()->route('maintenance.show', $perjalanan->id)
    //         ->with('success', 'Semua file berhasil diproses untuk perjalanan ID: ' . $perjalanan->id_perjalanan);
    // }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id_perjalanan' => 'nullable|string|max:255',
                'nama_pengguna' => 'required|string|max:255',
                'nama_tempat'   => 'required|string|max:255',
                'nmf_file'      => 'required',
                'nmf_file.*'    => 'file|mimes:txt,nmf|max:51200',
                'status'        => 'required|in:Before,After',
            ]);
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        $files = $request->file('nmf_file');
        $idPerjalananInput = $validatedData['id_perjalanan'] ?? '';
        $folderPath = 'uploads/perjalanan';
        $destinationPath = public_path($folderPath);
        $parser = new FileTelkominfraController();

        // Buat perjalanan utama (hanya sekali)
        $idPerjalananStore = ($idPerjalananInput == '') ? Str::uuid()->toString() : $idPerjalananInput;
        $perjalanan = Perjalanan::firstOrCreate(
            ['id_perjalanan' => $idPerjalananStore],
            [
                'nama_pengguna' => $validatedData['nama_pengguna'],
                'nama_tempat' => $validatedData['nama_tempat'],
            ]
        );

        foreach ($files as $file) {
            try {
                $fileExtension = $file->getClientOriginalExtension();
                $tempUniqueFileName = $idPerjalananStore . '_' . Str::random(6) . '.' . $fileExtension;
                $file->move($destinationPath, $tempUniqueFileName);
                $oldPath = $destinationPath . DIRECTORY_SEPARATOR . $tempUniqueFileName;

                $nmfHeaderData = $parser->parseNmfHeader($oldPath);
                $nmfTimes = $parser->extractNmfTimes($oldPath);
                $perangkat = $nmfHeaderData['perangkat'] ?? 'Unknown Device';

                // Simpan data perjalanan_data
                $dataPerjalanan = DataPerjalanan::create([
                    'perjalanan_id' => $perjalanan->id,
                    'perangkat' => $perangkat,
                    'file_nmf' => $tempUniqueFileName,
                    'status' => $validatedData['status'],
                    'timestamp_mulai' => $nmfTimes['timestamp_mulai'],
                    'timestamp_selesai' => $nmfTimes['timestamp_selesai'],
                ]);

                $finalFileName = $dataPerjalanan->id . '_' . $perjalanan->id_perjalanan . '.' . $fileExtension;
                $newPath = $destinationPath . DIRECTORY_SEPARATOR . $finalFileName;

                if (File::exists($oldPath)) {
                    File::move($oldPath, $newPath);
                    $dataPerjalanan->file_nmf = $finalFileName;
                    $dataPerjalanan->save();
                }

                // 🔹 Gunakan LazyCollection untuk parsing file besar
                try {
                    $lazySignals = $parser->parseNmfSinyal($newPath, $perjalanan->id);
                    $lazySignals
                        ->map(function ($item) use ($dataPerjalanan) {
                            $item['data_perjalanan_id'] = $dataPerjalanan->id;
                            unset($item['perjalanan_id']);
                            return $item;
                        })
                        ->chunk(1000)
                        ->each(function ($chunk) use ($finalFileName) {
                            PengukuranSinyal::insert($chunk->toArray());
                            Log::info("Menyimpan batch " . count($chunk) . " data sinyal dari: $finalFileName");
                        });

                    Log::info("Berhasil menyimpan data sinyal (lazy) dari file: $finalFileName");
                } catch (\Exception $signalEx) {
                    Log::error("Gagal menyimpan data sinyal dari $finalFileName: " . $signalEx->getMessage());
                }
            } catch (\Exception $ex) {
                Log::error("Gagal memproses file: " . $file->getClientOriginalName(), [
                    'error' => $ex->getMessage(),
                    'line' => $ex->getLine(),
                ]);
                continue;
            }
        }

        return redirect()->route('maintenance.show', $perjalanan->id)
            ->with('success', 'Semua file berhasil diproses untuk perjalanan ID: ' . $perjalanan->id_perjalanan);
    }



     /**
     * Update data sesi perjalanan (nama_pengguna dan nama_tempat).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id ID dari Perjalanan yang akan diupdate.
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, $id)
    // {
    //     try {
    //         $request->validate([
    //             'nama_pengguna' => 'required|string|max:255',
    //             'nama_tempat' => 'required|string|max:255',
    //         ], [
    //             'nama_pengguna.required' => 'Nama Pengguna wajib diisi.',
    //             'nama_tempat.required' => 'Nama Tempat/Lokasi wajib diisi.',
    //         ]);
    //         $perjalanan = Perjalanan::findOrFail($id);

    //         $perjalanan->nama_pengguna = $request->nama_pengguna;
    //         $perjalanan->nama_tempat = $request->nama_tempat;
    //         $perjalanan->save();
    //         Log::info("Perjalanan ID: {$id} berhasil diperbarui.");

    //         return redirect()->route('maintenance.show', $perjalanan->id)
    //                          ->with('success', 'Detail Perjalanan (Nama Pengguna & Lokasi) berhasil diperbarui.');

    //     } catch (ValidationException $e) {
    //         return redirect()->back()
    //                          ->withErrors($e->errors())
    //                          ->withInput();
    //     } catch (\Exception $e) {
    //         Log::error('Gagal memperbarui perjalanan ID: ' . $id . '. Error: ' . $e->getMessage());
    //         return redirect()->back()->with('error', 'Gagal memperbarui data. Terjadi kesalahan server.');
    //     }
    // }
    public function update(Request $request, $id)
    {
        try {
            $perjalanan = Perjalanan::findOrFail($id);

            // KASUS 1: Hanya memperbarui status 'selesai' (dari form PATCH/PUT status)
            if ($request->has('selesai')) {
                // Kita bisa menggunakan 'PATCH' untuk ini, Laravel tetap menerima kedua method
                // Anda bisa tambahkan validasi untuk memastikan nilai adalah 0 atau 1
                $request->validate(['selesai' => 'required|boolean']); 
                
                $perjalanan->selesai = $request->selesai;
                $perjalanan->save();
                
                $statusMsg = $perjalanan->selesai ? 'Selesai' : 'Belum Selesai';
                
                Log::info("Perjalanan ID: {$id} status diubah menjadi {$statusMsg}.");
                
                return redirect()->route('maintenance.show', $perjalanan->id)
                                ->with('success', "Status Perjalanan berhasil diubah menjadi: {$statusMsg}.");
            }

            // KASUS 2: Memperbarui Detail Teks (dari form PUT detail)
            $request->validate([
                'nama_pengguna' => 'required|string|max:255',
                'nama_tempat' => 'required|string|max:255',
            ], [
                'nama_pengguna.required' => 'Nama Pengguna wajib diisi.',
                'nama_tempat.required' => 'Nama Tempat/Lokasi wajib diisi.',
            ]);

            $perjalanan->nama_pengguna = $request->nama_pengguna;
            $perjalanan->nama_tempat = $request->nama_tempat;
            $perjalanan->save();
            
            Log::info("Perjalanan ID: {$id} detail berhasil diperbarui.");

            return redirect()->route('maintenance.show', $perjalanan->id)
                            ->with('success', 'Detail Perjalanan (Nama Pengguna & Lokasi) berhasil diperbarui.');

        } catch (ValidationException $e) {
            return redirect()->back()
                            ->withErrors($e->errors())
                            ->withInput();
        } catch (\Exception $e) {
            Log::error('Gagal memperbarui perjalanan ID: ' . $id . '. Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui data. Terjadi kesalahan server.');
        }
    }

    /**
     * Hapus sesi perjalanan dan data terkait (DataPerjalanan dan file log).
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $perjalanan = null;
        try {
            $perjalanan = Perjalanan::findOrFail($id);
            Log::info("Mencoba menghapus Perjalanan ID: {$id}");

            DB::transaction(function () use ($perjalanan) {
                $basePath = public_path('uploads/perjalanan/');
                $dataPerjalanans = DataPerjalanan::where('perjalanan_id', $perjalanan->id)->get(); 
                
                if ($dataPerjalanans->isNotEmpty()) { 
                    foreach ($dataPerjalanans as $dataItem) {
                        if ($dataItem->file_nmf && File::exists($basePath . $dataItem->file_nmf)) {
                            File::delete($basePath . $dataItem->file_nmf);
                            Log::debug('File NMF dihapus: ' . $dataItem->file_nmf);
                        }
                        
                        if ($dataItem->file_gpx && File::exists($basePath . $dataItem->file_gpx)) {
                            File::delete($basePath . $dataItem->file_gpx);
                            Log::debug('File GPX dihapus: ' . $dataItem->file_gpx);
                        }
                    }
                } else {
                    Log::warning('Tidak ada data log DataPerjalanan yang ditemukan untuk ID Perjalanan: ' . $perjalanan->id);
                }

                $deletedLogCount = DataPerjalanan::where('perjalanan_id', $perjalanan->id)->delete();
                Log::info("Data log DataPerjalanan berhasil dihapus. Jumlah baris: {$deletedLogCount}");

                $perjalanan->delete();
                Log::info('Sesi Perjalanan utama berhasil dihapus.');
            });

            return redirect()->route('maintenance.index')->with('success', 'Sesi Drive Test dan semua data (termasuk file log) berhasil dihapus!');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning("Percobaan hapus gagal: Perjalanan ID {$id} tidak ditemukan.");
            return redirect()->route('maintenance.index')->with('error', 'Data yang ingin dihapus tidak ditemukan.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus perjalanan ID: ' . $id . '. Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('maintenance.index')->with('error', 'Gagal menghapus data. Terjadi kesalahan server. Detail error dicatat.');
        }
    }

    /**
     * Hapus satu data log (DataPerjalanan) dan file terkait.
     * Metode ini dipanggil oleh route 'dataPerjalanan.destroy'.
     *
     * @param  int  $id ID dari DataPerjalanan yang akan dihapus.
     * @return \Illuminate\Http\Response
     */
    public function destroyPerjalananData($id)
    {
        $dataPerjalanan = null;
        try {
            $dataPerjalanan = DataPerjalanan::findOrFail($id);
            Log::info("Mencoba menghapus satu log DataPerjalanan ID: {$id}");

            $perjalananId = $dataPerjalanan->perjalanan_id;
            DB::transaction(function () use ($dataPerjalanan) {
                $basePath = public_path('uploads/perjalanan/');

                if ($dataPerjalanan->file_nmf && File::exists($basePath . $dataPerjalanan->file_nmf)) {
                    File::delete($basePath . $dataPerjalanan->file_nmf);
                    Log::debug('File NMF dihapus: ' . $dataPerjalanan->file_nmf);
                }
                
                if ($dataPerjalanan->file_gpx && File::exists($basePath . $dataPerjalanan->file_gpx)) {
                    File::delete($basePath . $dataPerjalanan->file_gpx);
                    Log::debug('File GPX dihapus: ' . $dataPerjalanan->file_gpx);
                }

                $dataPerjalanan->delete();
                Log::info('Satu data log DataPerjalanan berhasil dihapus.');
            });

            return redirect()->route('maintenance.show', $perjalananId)->with('success', 'Satu log data dan file terkait berhasil dihapus!');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning("Percobaan hapus log gagal: DataPerjalanan ID {$id} tidak ditemukan.");
            return redirect()->route('maintenance.index')->with('error', 'Log data yang ingin dihapus tidak ditemukan.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus log data ID: ' . $id . '. Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Gagal menghapus log data. Terjadi kesalahan server. Detail error dicatat.');
        }
    }
}