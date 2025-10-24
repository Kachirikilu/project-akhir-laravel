<div class="bg-white shadow-md rounded-lg p-6 border border-gray-200">
    <h2 class="text-lg font-semibold mb-4 text-gray-700">Tambah Keluhan Pengguna</h2>

    <form action="{{ route('keluh_pengguna.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700">Nama Pengguna</label>
            <input type="text" name="nama_pengguna" value="{{ Auth::user()->name ?? 'Anonim' }}" required
                class="mt-1 block w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Nama Tempat</label>
            <input type="text" name="nama_tempat" required
                class="mt-1 block w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Komentar / Keluhan</label>
            <textarea name="komentar" rows="3" required
                class="mt-1 block w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-400 focus:outline-none"></textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Unggah Foto (opsional)</label>
            <input type="file" name="foto" accept="image/*"
                class="mt-1 block w-full border border-gray-300 rounded-lg p-2 bg-white cursor-pointer">
        </div>

        <button type="submit"
            class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg transition">
            Simpan Keluhan
        </button>
    </form>
</div>
