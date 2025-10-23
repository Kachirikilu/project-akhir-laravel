    <div class="mb-6 border p-4 rounded-lg bg-gray-50">
        <div class="flex items-center justify-between mb-4">
            <h4 class="text-md font-bold mb-3">Unggah Data Perjalanan (NMF) Baru</h4>
            <form action="{{ route('perjalanan.destroy', $perjalananDetail->id) }}" method="POST" class="inline-block"
                onsubmit="return confirm('Apakah Anda yakin ingin menghapus data perjalanan ini? Data log yang terkait juga akan terhapus.');">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-1.5 px-3 rounded text-xs uppercase transition ease-in-out duration-150">
                    Hapus Semua</button>
            </form>
        </div>
        <form action="{{ route('perjalanan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <input type="hidden" name="nama_pengguna" value="{{ Auth::user()->name ?? 'User Default' }}">
                <input type="hidden" name="nama_tempat"
                    value="{{ $perjalananDetail->nama_tempat ?? 'Lokasi Default' }}">

                {{-- File NMF Input --}}
                <div>
                    <label for="nmf_file" class="block text-sm font-medium text-gray-700">
                        File Nemo (.nmf atau .txt log):
                    </label>
                    <input type="file" id="nmf_file" name="nmf_file[]" accept=".nmf,.txt" multiple required
                        class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-white p-2">
                </div>

                {{-- NEW: Status Input (Select) --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status Pengujian:</label>
                    <select id="status" name="status" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="Before">Before</option>
                        <option value="After" selected>After</option>
                    </select>
                </div>

                <div>
                    <label for="id_perjalanan_display" class="block text-sm font-medium text-gray-700">Nama
                        ID:</label>
                    <input name="id_perjalanan" type="text" id="id_perjalanan_display" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border bg-gray-200 cursor-not-allowed"
                        value="{{ $perjalananDetail->id_perjalanan ?? null }}" readonly>
                </div>

            </div>

            {{-- Handling Errors --}}
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                    role="alert">
                    <strong>Error!</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring focus:ring-indigo-300 transition ease-in-out duration-150 mt-2">
                Unggah Data Baru
            </button>
        </form>


    </div>