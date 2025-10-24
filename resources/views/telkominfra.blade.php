<x-app-layout>

    {{-- @if(Auth::user()?->admin) --}}
        <x-admin.menu />
    {{-- @else
        <x-home.navbar />
    @endif --}}

    {{-- @if(Auth::user()?->admin) --}}
        <div class="mx-auto flex-1 p-1 sm:p-3 md:p-6 lg:p-8 overflow-y-auto">
    {{-- @else
        <div class="mx-auto flex-1 pt-24 px-1 sm:px-18 md:px-24 lg:px-32 overflow-y-auto">
    @endif --}}
      
        <x-telkominfra.maintenance.view
            :perjalanans="$perjalanans ?? []"
            :search="$search"
            :searchMode="$searchMode"
            :totalPerjalanan="$totalPerjalanan"
            :perjalananSelesai="$perjalananSelesai"
            :perjalananBelumSelesai="$perjalananBelumSelesai"
        />
     <x-home.footer />
    </div>
</x-app-layout>