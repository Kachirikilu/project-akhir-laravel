<x-app-layout>

    <x-admin.menu />

    <div class="mx-auto flex-1 p-1 sm:p-3 md:p-6 lg:p-8 overflow-y-auto">

        <x-telkominfra.maintenance.view :perjalanans="$perjalanans ?? []" :search="$search" :searchMode="$searchMode" :totalPerjalanan="$totalPerjalanan"
            :perjalananSelesai="$perjalananSelesai" :perjalananBelumSelesai="$perjalananBelumSelesai" />
        <x-home.footer />
    </div>
</x-app-layout>
