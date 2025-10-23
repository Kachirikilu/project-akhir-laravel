<div class="mb-6 border p-6 rounded-xl bg-gray-50 shadow-md">
     <h4 class="text-xl font-extrabold mb-4 text-indigo-700 border-b pb-2">
         <i class="fas fa-comments mr-2"></i> Hubungkan Komentar Pengguna
     </h4>

     <div class="mb-3">
         <label for="searchKomentar" class="block text-sm font-medium text-gray-700">
             Cari Komentar Belum Terhubung (Nama Tempat/Komentar):
         </label>
         <input type="text" id="searchKomentar" placeholder="Ketik nama tempat atau isi komentar..."
             class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border focus:ring-indigo-500 focus:border-indigo-500">
     </div>

     <form id="assignForm">
         @csrf
         <input type="hidden" name="perjalanan_id" value="{{ $perjalananDetail->id }}">

         <div id="hasilKomentar" class="space-y-2 max-h-60 overflow-y-auto border rounded-md p-3 bg-white">
             <p class="text-gray-500 text-sm italic">Ketik untuk menampilkan komentar...</p>
         </div>

         <button type="submit" id="assignButton"
             class="mt-4 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md font-semibold text-sm transition">
             <i class="fas fa-save mr-1"></i> Hubungkan ke Perjalanan Ini
         </button>
     </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('searchKomentar');
        const hasilKomentar = document.getElementById('hasilKomentar');
        const assignForm = document.getElementById('assignForm');
        const assignButton = document.getElementById('assignButton'); 

        let controller; 

        searchInput.addEventListener('input', async function() {
            const query = this.value.trim();
            if (!query) {
                hasilKomentar.innerHTML =
                    '<p class="text-gray-500 text-sm italic">Ketik untuk menampilkan komentar...</p>';
                return;
            }

            if (controller) controller.abort();
            controller = new AbortController();
            hasilKomentar.innerHTML = '<p class="text-center text-gray-500 text-sm italic p-2"><i class="fas fa-spinner fa-spin mr-1"></i> Mencari...</p>';


            try {
                const response = await fetch(
                    `{{ route('maintenance.comentSearch') }}?q=${encodeURIComponent(query)}`, {
                        signal: controller.signal
                    });
                const data = await response.json();

                if (data.length === 0) {
                    hasilKomentar.innerHTML =
                        '<p class="text-center text-gray-500 text-sm italic p-2">Tidak ada komentar belum terhubung ditemukan.</p>';
                    return;
                }

                hasilKomentar.innerHTML = data.map(item => `
                    <div class="flex items-start space-x-2 border-b last:border-b-0 p-2 hover:bg-indigo-50 transition" 
                         data-keluhan-id="${item.id}" id="keluhan-${item.id}">
                        
                        <input type="checkbox" name="keluhan_ids[]" value="${item.id}" 
                               class="mt-1.5 h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        
                        <div class="flex-1">
                            <p class="font-semibold text-sm">${item.nama_pengguna}</p>
                            <p class="text-gray-600 text-xs italic">${item.nama_tempat}</p>
                            <p class="text-gray-800 text-sm">${item.komentar}</p>
                        </div>
                    </div>
                `).join('');

            } catch (e) {
                if (e.name !== 'AbortError') console.error("Fetch error:", e);
            }
        });

        assignForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const checkedBoxes = Array.from(assignForm.querySelectorAll('input[name="keluhan_ids[]"]:checked'));
            const keluhanIdsToAssign = checkedBoxes.map(cb => cb.value);

            if (keluhanIdsToAssign.length === 0) {
                alert('Pilih setidaknya satu komentar untuk dihubungkan.');
                return;
            }

            assignButton.disabled = true;
            assignButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
            
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('perjalanan_id', assignForm.querySelector('input[name="perjalanan_id"]').value);
            keluhanIdsToAssign.forEach(id => formData.append('keluhan_ids[]', id));
            
            try {
                const response = await fetch(`{{ route('keluh_pengguna.assign') }}`, {
                    method: 'POST',
                    body: formData,
                });
                const result = await response.json();

                if (result.success) {
                    alert(result.message);
                    
                    keluhanIdsToAssign.forEach(id => {
                        const element = document.getElementById(`keluhan-${id}`);
                        if (element) {
                            element.remove();
                        }
                    });

                    searchInput.value = '';
                    if (hasilKomentar.children.length === 0) {
                        hasilKomentar.innerHTML = '<p class="text-center text-gray-500 text-sm italic p-2">Ketik untuk menampilkan komentar...</p>';
                    }
                    window.location.reload(); 
                    
                } else {
                    alert('Gagal menghubungkan komentar: ' + (result.message || 'Terjadi kesalahan server.'));
                }
            } catch (e) {
                console.error('Submit Error:', e);
                alert('Terjadi kesalahan jaringan atau server.');
            } finally {
                assignButton.disabled = false;
                assignButton.innerHTML = '<i class="fas fa-save mr-1"></i> Hubungkan ke Perjalanan Ini';
            }
        });
    });
</script>