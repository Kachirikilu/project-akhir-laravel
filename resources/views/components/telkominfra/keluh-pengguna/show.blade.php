<div class="p-6 bg-white shadow-md rounded-lg">
    <h2 class="text-xl font-bold mb-4">Detail Keluhan Pengguna</h2>

    <p><strong>Nama Pengguna:</strong> {{ $keluhPengguna->nama_pengguna }}</p>
    <p><strong>Nama Tempat:</strong> {{ $keluhPengguna->nama_tempat }}</p>
    <p><strong>Komentar:</strong> {{ $keluhPengguna->komentar ?? '-' }}</p>

    @if ($keluhPengguna->foto)
        <p><strong>Foto:</strong></p>
        <img src="{{ asset('images/keluh/' . $keluhPengguna->foto) }}" alt="Bukti Keluhan" class="mt-2 w-64 rounded-lg">
    @endif

    <div class="mt-4">
        <a href="{{ route('keluh_pengguna.index') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Komentar Lain
        </a>
    </div>
</div>
