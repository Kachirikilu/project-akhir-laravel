@php
    $appName = env('APP_NAME');
@endphp
<div id="scroll-map" class="scroll-mt-20 mt-8 mb-4">
    @if ($appName == 'Al-Aqobah 1')
        <h2 class="text-xl font-semibold mb-4 mx-2 sm:mx-0 border-b border-gray-200 pb-2">Lokasi Masjid</h2>
        <div class="overflow-hidden rounded-md shadow-md">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3984.4424348373436!2d104.79979469999999!3d-2.974643!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e3b77b54752dde9%3A0xa476856998a2a3b2!2sMasjid%20Al%20-%20Aqobah%201!5e0!3m2!1sid!2sid!4v1746677602550!5m2!1sid!2sid"
                width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    @elseif ($appName == 'PT. Telkominfra')
        <h2 class="text-xl font-semibold mb-4 mx-2 sm:mx-0 border-b border-gray-200 pb-2">Lokasi Telkominfra</h2>
        <div class="overflow-hidden rounded-md shadow-md">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3984.46204093488!2d104.74998199999999!3d-2.9692124999999994!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e3b75002463890f%3A0x2728638bd157caa9!2sGraPARI%20Palembang%201!5e0!3m2!1sid!2sid!4v1761205847859!5m2!1sid!2sid"
                width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    @endif
</div>
