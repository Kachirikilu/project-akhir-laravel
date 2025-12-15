<x-app-layout>

    {{-- @if(Auth::user()?->admin) --}}
        <x-admin.menu />
    {{-- @else
        <x-home.navbar />
    @endif --}}


    {{-- @if(Auth::user()?->admin) --}}
       <div class="max-w-[1080px] mx-auto flex-1 p-1 sm:p-3 md:p-6 lg:p-8 overflow-y-auto">
    {{-- @else
        <div class="max-w-[1080px] mx-auto flex-1 pt-24 px-1 sm:px-3 md:px-6 lg:px-8 overflow-y-auto">
    @endif --}}
        @if (request()->is('keluh-pengguna'))
            <x-telkominfra.keluh-pengguna.view 
                :totalKeluhan="$totalKeluhan"
                :keluhanSelesai="$keluhanSelesai"
                :keluhanBelumSelesai="$keluhanBelumSelesai"
                :keluhanSelesaiList="$keluhanSelesaiList"
                :keluhanBelumSelesaiList="$keluhanBelumSelesaiList"
                :keluhanSayaBelumSelesaiList="$keluhanSayaBelumSelesaiList"
                :keluhanDiproses="$keluhanDiproses"
                :keluhanDiprosesList="$keluhanDiprosesList"
            />


        @elseif (request()->is('keluh-pengguna/create'))
            <x-telkominfra.keluh-pengguna.create />

        @elseif (preg_match('#^keluh-pengguna/\d+$#', request()->path()))
            <x-telkominfra.keluh-pengguna.show :keluhPengguna="$keluhPengguna" />
        @endif

        <x-home.footer />
    </div>

</x-app-layout>
