@props(['mapItem'])

@php
    $isBefore = $mapItem['status'] === 'Before';
    $colorClass = $isBefore
        ? 'text-orange-600 border-orange-600'
        : 'text-emerald-600 border-emerald-600';

    $filePath = isset($mapItem['fileName']) ? public_path('uploads/perjalanan/' . $mapItem['fileName']) : null;
    $fileExists = $filePath && file_exists($filePath);
@endphp

<div class="p-2 bg-white rounded-xl shadow-lg border border-gray-100 flex flex-col">
    <div class="flex items-center justify-between px-2 flex-shrink-0">
        <div>
            <h4 class="text-md font-bold px-3 inline-block border-b-4 {{ $colorClass }}">
                {{ $mapItem['status'] }}
            </h4>
        </div>

        <div class="flex items-center space-x-2">
            @if ($fileExists)
                <a href="{{ asset('uploads/perjalanan/' . $mapItem['fileName']) }}"
                    download="{{ $mapItem['fileName'] }}"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-1.5 px-3 rounded-lg text-xs uppercase transition ease-in-out duration-150 shadow-sm">
                    Download
                </a>
            @endif

            @if (is_int($mapItem['id']))
                <form action="{{ route('perjalanan.dataDestroy', $mapItem['id']) }}" method="POST"
                    class="inline-block"
                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus data log ini? Ini tidak dapat dibatalkan.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="bg-red-500 ml-1 hover:bg-red-600 text-white font-semibold py-1.5 px-3 rounded-lg text-xs uppercase transition ease-in-out duration-150 shadow-sm">
                        Hapus Log
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Div Peta --}}
    <div id="map-{{ $mapItem['id'] }}"
        class="h-[400px] w-full border border-gray-300 rounded-lg shadow-inner mt-2 flex-grow z-0">
    </div>

    {{-- Detail File --}}
    <div class="mt-2">
        <h6 class="text-center text-md font-bold text-gray-800 break-words">
            {{ $mapItem['fileName'] }}
        </h6>
        <h6 class="text-center text-sm text-gray-600">{{ $mapItem['perangkat'] }}</h6>
    </div>
</div>
