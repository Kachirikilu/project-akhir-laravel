{{-- <div class="p-6 bg-white shadow-md rounded-lg">
    <h2 class="text-xl font-bold mb-4">Edit Keluhan Pengguna</h2>

    <form action="{{ route('keluh_pengguna.update', $keluhPengguna->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold">Nama Pengguna</label>
            <input type="text" name="nama_pengguna" value="{{ $keluhPengguna->nama_pengguna }}" class="w-full border rounded p-2" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold">Nama Tempat</label>
            <input type="text" name="nama_tempat" value="{{ $keluhPengguna->nama_tempat }}" class="w-full border rounded p-2" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold">Komentar</label>
            <textarea name="komentar" class="w-full border rounded p-2">{{ $keluhPengguna->komentar }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold">Foto Bukti (opsional)</label>
            <input type="file" name="foto" accept="image/*" class="w-full border rounded p-2">
        </div>

        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Perbarui</button>
    </form>
</div> --}}
