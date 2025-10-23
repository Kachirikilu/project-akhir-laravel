<x-app-layout>
    
    <x-home.navbar />

    <div>
        <x-home.protofolio />

        <div class="max-w-[1360px] mx-auto flex-1 px-1 py-3 sm:p-3 md:p-6 lg:p-8 overflow-y-auto">
            <x-home.welcome-home />

            <x-home.info-home 
                :jadwalHariIni="$jadwalHariIni"
                :jadwalBelumTerlaksanaCount="$jadwalBelumTerlaksanaCount"
                :jadwalSudahTerlaksanaCount="$jadwalSudahTerlaksanaCount"
                :totalJadwalCount="$totalJadwalCount"
            />
            {{-- <x-mqtt.camera /> --}}
            {{-- @livewire('data-device.camera') --}}

            <x-home.galery-home />

            <div id="grup-jadwal" class="scroll-mt-20">
                @if(!$jadwalHariIni->isEmpty())
                    <x-home.hari-ini :jadwalHariIni="$jadwalHariIni" />
                @endif
                @if(!$jadwalMingguIni->isEmpty())
                    {{-- <x-home.minggu-ini :jadwalMingguIni="$jadwalMingguIni" /> --}}
                    <x-home.mingguan :jadwalMingguan="$jadwalMingguIni" :name="$x='Minggu Ini'" />
                @endif
                @if(!$jadwalMingguDepan->isEmpty())
                    <x-home.mingguan :jadwalMingguan="$jadwalMingguDepan" :name="$x='Minggu Depan'" />
                @endif
                
                <x-home.mingguan :jadwalMingguan="$jadwalSudahTerlaksana" :name="$x='Sudah Terlaksana'" />
                {{-- <x-home.terlaksana-user :jadwalSudahTerlaksana="$jadwalSudahTerlaksana" /> --}}
            </div>

            <x-home.maps />

            <x-home.footer />


        </div>
    </div>

</x-app-layout>