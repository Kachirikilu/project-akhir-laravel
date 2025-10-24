@php
    $appName = env('APP_NAME');
@endphp
<x-app-layout>

    <x-admin.menu />

    <div class="max-w-[1080px] mx-auto flex-1 p-1 sm:p-3 md:p-6 lg:p-8 overflow-y-auto">

        {{-- <x-telkominfra.keluh-pengguna.view 
             :totalKeluhan="$totalKeluhan"
             :keluhanSelesai="$keluhanSelesai"
             :keluhanBelumSelesai="$keluhanBelumSelesai"
             :keluhanSelesaiList="$keluhanSelesaiList"
             :keluhanBelumSelesaiList="$keluhanBelumSelesaiList"
             :keluhanSayaBelumSelesaiList="$keluhanSayaBelumSelesaiList"
             :keluhanDiproses="$keluhanDiproses"
             :keluhanDiprosesList="$keluhanDiprosesList"
         /> --}}

        <x-home.welcome-home />
        <x-home.info-home :jadwalHariIni="$jadwalHariIni" :jadwalBelumTerlaksanaCount="$jadwalBelumTerlaksanaCount" :jadwalSudahTerlaksanaCount="$jadwalSudahTerlaksanaCount" :totalJadwalCount="$totalJadwalCount" />

        <x-admin.user
            :users="$users" 
            :totalUsers="$totalUsers"
            :totalAdmins="$totalAdmins"
            :totalNormalUsers="$totalNormalUsers"
            :search="$search"
            :searchMode="$searchMode"
        />
        {{-- <x-mqtt.camera /> --}}
        {{-- @livewire('data-device.camera') --}}

        @if ($appName == 'Al-Aqobah 1')
            <x-home.galery-home />
            <x-home.hari-ini :jadwalHariIni="$jadwalHariIni" />

            <x-home.mingguan :jadwalMingguan="$jadwalMingguIni" :name="$x = 'Minggu Ini'" />
            <x-home.mingguan :jadwalMingguan="$jadwalMingguDepan" :name="$x = 'Minggu Depan'" />
            <x-home.mingguan :jadwalMingguan="$jadwalMingguSelanjutnya" :name="$x = 'Minggu Selanjutnya'" />

            <x-home.maps />
            <x-home.terlaksana :jadwalSudahTerlaksana="$jadwalSudahTerlaksana" />
        @endif
        <x-home.footer />
    </div>

</x-app-layout>
