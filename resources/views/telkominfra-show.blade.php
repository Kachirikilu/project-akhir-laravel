<x-app-layout>

        <x-admin.menu />
        <div class="mx-auto flex-1 p-1 sm:p-3 md:p-6 lg:p-8 overflow-y-auto">
            <div class="bg-gray-90 font-sans pt-2 pb-1 px-4 mb-20">

            @if(Auth::user()?->admin)
                <x-telkominfra.maintenance.show.form-show
                    :perjalanan-detail="$perjalananDetail ?? null"
                />
                <x-telkominfra.maintenance.show.keluh-pengguna.unassign 
                    :komentarTerhubung="$komentarTerhubung ?? null"
                    :komentarBelumTerhubung="$komentarBelumTerhubung ?? null"
                />
            @endif
            @if(Auth::user()?->admin)
                <x-telkominfra.maintenance.show.keluh-pengguna.assign 
                    :perjalanan-detail="$perjalananDetail ?? null"  
                />
            @endif

            @if(Auth::user()?->admin)
                <x-telkominfra.maintenance.show.update-show
                    :perjalanan-detail="$perjalananDetail ?? null"  
                />
            @endif

            <x-telkominfra.maintenance.show.signal-show
                :signal-averages="$signalAverages ?? []"
            />
            <x-telkominfra.maintenance.show.maps.leaflet
                :perjalanan-detail="$perjalananDetail ?? null"
                :mapsData="$mapsData ?? []"
            />
        </div>
    <x-home.footer />

    </div>

</x-app-layout>