<!-- Sticky Navbar with Dropdown -->
@php
    $appName = env('APP_NAME');
@endphp
<nav class="bg-white shadow-lg fixed w-full z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="/" class="flex-shrink-0 flex items-center">
                    @if ($appName == 'Al-Aqobah 1')
                        <img class="h-12 w-auto" src="/images/masjid/Logo PT PUSRI.png" alt="Logo">
                        <span class="ml-2 text-xl font-bold text-gray-800">{{ $appName }}</span>
                    @elseif ($appName == 'PT. Telkominfra')
                        <img class="h-12 w-auto" src="/images/telkominfra/Logo PT Telkominfra.png" alt="Logo">
                    @endif
                </a>
                <div class="hidden md:ml-6 md:flex md:space-x-8">
                    <a href="/" id="scroll-ke-home"
                        class="hover:text-blue-600 hover:border-blue-500 transition-colors duration-300 border-transparent text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Home
                    </a>

                    @if ($appName == 'Al-Aqobah 1')
                        <a href="/#grup-jadwal" id="scroll-ke-grup-jadwal"
                            class="hover:text-blue-600 hover:border-blue-500 transition-colors duration-300 border-transparent text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Kegiatan
                        </a>
                        <a href="/#scroll-galery" id="scroll-ke-galery"
                            class="hover:text-blue-600 hover:border-blue-500 transition-colors duration-300 border-transparent text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Galery
                        </a>
                    @elseif ($appName == 'PT. Telkominfra')
                        <a href="/keluh-pengguna"
                            class="hover:text-blue-600 hover:border-blue-500 transition-colors duration-300 border-transparent text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Keluh Pengguna
                        </a>
                        <a href="/maintenance"
                            class="hover:text-blue-600 hover:border-blue-500 transition-colors duration-300 border-transparent text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Maintenance
                        </a>
                    @endif
                    <a href="/#scroll-jadwal-sholat" id="scroll-ke-jadwal-sholat"
                        class="hover:text-blue-600 hover:border-blue-500 transition-colors duration-300 border-transparent text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Waktu Sholat
                    </a>

                    <a href="#scroll-footer" id="scroll-ke-footer"
                        class="hover:text-blue-600 hover:border-blue-500 transition-colors duration-300 border-transparent text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Contact
                    </a>
                </div>
            </div>
            <div class="flex items-center">
                <div class="hidden md:ml-4 md:flex md:items-center">
                    @auth
                        {{-- Pengguna sudah login --}}
                        @if (Auth::user()?->admin)
                            <a href="/dashboard"
                                class="bg-indigo-600 px-4 py-2 rounded-md text-white text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Dashboard
                            </a>
                        @else
                            {{-- Pengguna bukan Admin (Pengguna Biasa) --}}
                            <a href="{{ route('profile.show') }}"
                                class="bg-indigo-600 px-4 py-2 rounded-md text-white text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ Auth::user()->name ?? 'Profil' }}
                            </a>
                        @endif
                    @endauth

                    @guest
                        {{-- Pengguna belum login (Guest) --}}
                        <a href="/login"
                            class="bg-indigo-600 px-4 py-2 rounded-md text-white text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Login
                        </a>
                    @endguest
                </div>
                <div class="flex items-center md:hidden">
                    <button type="button"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                        aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile menu, show/hide based on menu state -->
    <div class="md:hidden hidden" id="mobile-menu">
        <div class="pt-2 pb-3 space-y-1">
            <a href="/" id="scroll-ke-home-2"
                class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                Home
            </a>


            @if ($appName == 'Al-Aqobah 1')
                <a id="scroll-ke-grup-jadwal-2" href="/#grup-jadwal"
                    class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                    Kegiatan
                </a>

                <a id="scroll-ke-galery-2" href="/#scroll-galery"
                    class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                    Galery
                </a>
            @elseif ($appName == 'PT. Telkominfra')
                <a href="/keluh-pengguna"
                    class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                    Keluh Pengguna
                </a>
                <a href="/maintenance"
                    class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                    Maintenance
                </a>
            @endif
            <a id="scroll-ke-jadwal-sholat-2" href="/#scroll-jadwal-sholat"
                class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                Waktu Sholat
            </a>
            <a id="scroll-ke-footer-2" href="#scroll-footer"
                class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                Contact
            </a>
        </div>
        <div class="pt-4 pb-3 border-t border-gray-200">
            <div class="flex items-center px-4">
                @auth
                    {{-- Pengguna sudah login --}}
                    @if (Auth::user()?->admin)
                        <a href="/dashboard"
                            class="w-full bg-indigo-600 px-4 py-2 rounded-md text-white text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Dashboard
                        </a>
                    @else
                        {{-- Pengguna bukan Admin (Pengguna Biasa) --}}
                        <a href="{{ route('profile.show') }}"
                            class="w-full bg-indigo-600 px-4 py-2 rounded-md text-white text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ Auth::user()->name ?? 'Profil' }}
                        </a>
                    @endif
                @endauth

                @guest
                    {{-- Pengguna belum login (Guest) --}}
                    <a href="/login"
                        class="w-full bg-indigo-600 px-4 py-2 rounded-md text-white text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Login
                    </a>
                @endguest
            </div>
        </div>
    </div>
</nav>

<script>
    // Simple toggle for mobile menu
    document.querySelector('button[aria-expanded]').addEventListener('click', function() {
        const mobileMenu = document.getElementById('mobile-menu');
        const isExpanded = this.getAttribute('aria-expanded') === 'true';
        this.setAttribute('aria-expanded', !isExpanded);
        mobileMenu.classList.toggle('hidden');
    });
</script>
