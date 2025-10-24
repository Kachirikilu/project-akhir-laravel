<div class="mb-6 border p-6 rounded-xl bg-indigo-50 shadow-md">
    <h4 class="text-xl font-extrabold mb-4 text-indigo-700 border-b pb-2">
        <i class="fas fa-link mr-2"></i> Komentar Terhubung ke Perjalanan Ini ({{ $komentarTerhubung->count() }})
    </h4>

    @if($komentarTerhubung->isNotEmpty())
        <form id="unassignForm"> 
             @csrf
             <div class="space-y-3 max-h-48 overflow-y-auto pr-2">
                 @foreach($komentarTerhubung as $komentar)
                     <div class="p-3 bg-white rounded-lg shadow-sm border border-indigo-100 flex items-start justify-between" id="terhubung-{{ $komentar->id }}">
                         <div class="flex-1 mr-3">
                             <p class="font-semibold text-sm text-indigo-800">{{ $komentar->nama_pengguna }}</p>
                             <p class="text-gray-600 text-xs italic mb-1">{{ $komentar->nama_tempat }}</p>
                             <p class="text-gray-800 text-sm">"{{ $komentar->komentar }}"</p>
                         </div>
                         <button type="button" 
                                 data-id="{{ $komentar->id }}" 
                                 class="unassign-btn text-xs font-semibold px-2 py-1 rounded-full bg-red-100 text-red-700 hover:bg-red-200 transition duration-150"
                                 title="Lepaskan komentar ini dari perjalanan">
                             <i class="fas fa-times"></i> Hapus
                         </button>
                     </div>
                 @endforeach
             </div>
        </form>
    @else
        <p class="text-gray-500 text-sm italic">Belum ada komentar pengguna yang dihubungkan ke perjalanan ini.</p>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {

        document.querySelectorAll('.unassign-btn').forEach(button => {
            button.addEventListener('click', async function() {
                const keluhanId = this.dataset.id;
                const buttonElement = this;
                
                if (!confirm('Apakah Anda yakin ingin melepaskan komentar ini dari perjalanan?')) {
                    return;
                }

                buttonElement.disabled = true;
                const originalHtml = buttonElement.innerHTML;
                buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('keluhan_ids[]', keluhanId); 

                try {
                    const response = await fetch(`{{ route('keluh_pengguna.unassign') }}`, {
                        method: 'POST',
                        body: formData,
                    });
                    const result = await response.json();

                    if (result.success) {
                        alert(result.message);
                        const elementToRemove = document.getElementById(`terhubung-${keluhanId}`);
                        if (elementToRemove) {
                            elementToRemove.remove();
                        }
                        window.location.reload(); 
                    } else {
                        alert('Gagal melepaskan komentar: ' + (result.message || 'Terjadi kesalahan server.'));
                    }
                } catch (e) {
                    console.error('Unassign Error:', e);
                    alert('Terjadi kesalahan jaringan atau server.');
                } finally {
                    buttonElement.disabled = false;
                    buttonElement.innerHTML = originalHtml;
                }
            });
        });
        
    });
</script>