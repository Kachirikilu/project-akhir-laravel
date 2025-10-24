@php
    $appName = env('APP_NAME');
@endphp

<footer id="scroll-footer" class="flex flex-col space-y-10 justify-center m-10 mt-40">

    <nav class="flex justify-center flex-wrap gap-6 text-gray-500 font-medium">
        <a class="hover:text-gray-900" href="/" id="scroll-ke-home-3">Home</a>
        @if ($appName == 'Al-Aqobah 1')
            <a class="hover:text-gray-900" href="/#group-jadwal" id="scroll-ke-grup-jadwal-4">Kegiatan</a>
            <a class="hover:text-gray-900" href="/#scroll-galery" id="scroll-ke-galery-3">Gallery</a>
        @elseif ($appName == 'PT. Telkominfra')
            <a class="hover:text-gray-900" href="/keluh-pengguna">Keluh Pengguna</a>
            <a class="hover:text-gray-900" href="/maintenance">Maintenance</a>
        @endif
        <a class="hover:text-gray-900" href="/#scroll-jadwal-sholat" id="scroll-ke-jadwal-sholat-3">Waktu Sholat</a>
        @auth
            <form method="POST" action="{{ route('logout') }}" id="logout-form" x-data>
                @csrf
                <a href="#" class="hover:text-gray-900"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Logout
                </a>
            </form>
        @else
            <a class="hover:text-gray-900" href="/login">
                Login
            </a>
        @endauth

    </nav>

    <div class="flex justify-center space-x-5">
        <a href="https://www.facebook.com/share/19SofgsQFs/" target="_blank" rel="noopener noreferrer">
            <img src="/images/icons/facebook.png" class="w-9" />
        </a>
        <a href="https://www.instagram.com/athif_kyuziera/profilecard/?igsh=NHFsazN2a2diM3Rp" target="_blank"
            rel="noopener noreferrer">
            <img src="/images/icons/instagram.png" class="w-9" />
        </a>
        <a href="https://wa.me/628985655826" target="_blank" rel="noopener noreferrer">
            <img src="/images/icons/whatsapp.png" class="w-9" />
        </a>
        <a href="https://x.com/WildanAthif12" target="_blank" rel="noopener noreferrer">
            <img src="/images/icons/x.png" class="w-9" />
        </a>
        <a href="https://www.linkedin.com/in/wildan-athif-muttaqien-89b327297" target="_blank"
            rel="noopener noreferrer">
            <img src="/images/icons/linkedin.png" class="w-9" />
        </a>
    </div>
    <p class="text-center text-gray-700 font-medium">&copy; 2025
        @if ($appName == 'Al-Aqobah 1')
            PT. PUSRI
        @elseif ($appName == 'PT. Telkominfra')
            PT. Telkomsel
        @endif

    </p>
</footer>
