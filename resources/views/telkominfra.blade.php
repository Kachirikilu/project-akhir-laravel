<x-app-layout>

    @auth
        <x-admin.menu />
    @else
        <x-home.navbar />
    @endauth

    @auth
        <div class="mx-auto flex-1 p-1 sm:p-3 md:p-6 lg:p-8 overflow-y-auto">
    @else
        <div class="mx-auto flex-1 pt-24 px-1 sm:px-18 md:px-24 lg:px-32 overflow-y-auto">
    @endauth
      
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