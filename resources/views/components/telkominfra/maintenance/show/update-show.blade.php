<div class="mb-6 border p-6 rounded-xl bg-white shadow-lg">
    <h4 class="text-xl font-extrabold mb-4 text-indigo-700 border-b pb-2">
        <i class="fas fa-edit mr-2"></i> Edit Detail Sesi Perjalanan
    </h4>

    <form action="{{ route('perjalanan.update', $perjalananDetail->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">

            <div>
                <label for="nama_pengguna" class="block text-sm font-medium text-gray-700">Nama Pengguna:</label>
                <input type="text" id="nama_pengguna" name="nama_pengguna" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    value="{{ old('nama_pengguna', $perjalananDetail->nama_pengguna ?? (Auth::user()->name ?? 'User Default')) }}">
            </div>

            <div>
                <label for="nama_tempat" class="block text-sm font-medium text-gray-700">Nama Tempat /
                    Lokasi:</label>
                <input type="text" id="nama_tempat" name="nama_tempat" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    value="{{ old('nama_tempat', $perjalananDetail->nama_tempat ?? null) }}">
            </div>

            <div>
                <label for="display_id_perjalanan" class="block text-sm font-medium text-gray-700">ID
                    Perjalanan:</label>
                <input type="text" id="display_id_perjalanan"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border bg-gray-200 cursor-not-allowed"
                    value="{{ $perjalananDetail->id ?? 'ID Not Found' }}" readonly>
            </div>
        </div>

        @if ($errors->any())
            @endif

        <button type="submit"
            class="inline-flex items-center px-4 py-2 bg-orange-400 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-500 active:bg-orange-500 focus:outline-none focus:border-orange-900 focus:ring focus:ring-orange-300 disabled:opacity-25 transition ease-in-out duration-150 mr-2">
            Simpan Perubahan Detail
        </button>
    </form>
    
    <hr class="my-6 border-gray-200">

    <div class="flex items-center justify-between">
        <h5 class="text-lg font-semibold text-gray-800">Status Perjalanan:</h5>

        <form action="{{ route('perjalanan.update', $perjalananDetail->id) }}" method="POST"
            onsubmit="return confirm('Apakah Anda yakin ingin menandai perjalanan ini sebagai {{ $perjalananDetail->selesai ? 'BELUM' : 'SUDAH' }} Selesai?');">
            @csrf
            @method('PATCH') 
            
            <input type="hidden" name="selesai" value="{{ $perjalananDetail->selesai ? '0' : '1' }}">

            <button type="submit"
                class="inline-flex items-center px-4 py-2 rounded-md font-semibold text-sm transition ease-in-out duration-150 shadow-md
                {{ $perjalananDetail->selesai 
                    ? 'bg-red-500 hover:bg-red-600 text-white' 
                    : 'bg-green-600 hover:bg-green-700 text-white' }}">
                
                @if ($perjalananDetail->selesai)
                    <i class="fas fa-undo mr-2"></i> Batalkan Selesai
                @else
                    <i class="fas fa-check-circle mr-2"></i> Tandai Selesai (Maintenance Berhasil)
                @endif
            </button>
        </form>
    </div>
    
    <p class="mt-2 text-sm {{ $perjalananDetail->selesai ? 'text-green-600' : 'text-yellow-600' }} font-bold">
        Status Saat Ini: {{ $perjalananDetail->selesai ? 'Selesai (Maintenance Berhasil)' : 'Belum Selesai (Perlu Maintenance)' }}
    </p>

</div>