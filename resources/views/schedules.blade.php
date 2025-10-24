<x-app-layout>

    @if(Auth::user()?->admin)
        <x-admin.menu />
    @else
        <x-home.navbar />
    @endif

    <div class="max-w-[1080px] mx-auto flex-1 p-1 sm:p-3 md:p-6 lg:p-8 overflow-y-auto">
        @if (request()->is('schedules'))
            <x-schedules.view :jadwalCeramahs="$jadwalCeramahs" />
        @elseif (preg_match('#^schedules/[^/]+/edit$#', request()->path()))
            <x-schedules.update :jadwalCeramah="$jadwalCeramah" />
        @elseif (request()->is('schedules/create'))
            <x-schedules.create />
        @elseif (request()->is('schedules/show/*'))
            <x-schedules.show :jadwalCeramah="$jadwalCeramah" />
        @elseif (request()->is('iot/all-data/*'))
            @if(Auth::user()?->admin)
                <div class="lg:mt-16">
            @else
                <div class="mt-24 sm:mt-20 lg:mt-16">
            @endif
                <x-home.mingguan :jadwalMingguan="$iotCamera" :name="$x='IoT Camera'" />
            </div>
        @endif

        <x-home.footer />
    </div>


</x-app-layout>