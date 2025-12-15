 <!-- Settings Dropdown -->
 <div class="ms-3 relative">
     <x-dropdown align="right" width="48">
         <x-slot name="trigger">
             @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                 <button
                     class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                     <img class="size-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}"
                         alt="{{ Auth::user()->admin->name ?? (Auth::user()->dosen->name ?? Auth::user()->mahasiswa->name) }}" />
                 </button>
             @else
                 <span class="inline-flex rounded-md">
                     <button type="button"
                         class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                         {{ Auth::user()->admin->name ?? (Auth::user()->dosen->name ?? Auth::user()->mahasiswa->name) }}

                         <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                             <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                         </svg>
                     </button>
                 </span>
             @endif
         </x-slot>

         <x-slot name="content">
             <!-- Account Management -->
             <div class="block px-4 py-2 text-xs text-gray-400">
                 {{ __('Manage Account') }}
             </div>

             <x-dropdown-link href="{{ route('profile.show') }}">
                 {{ __('Profile') }}
             </x-dropdown-link>

             @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                 <x-dropdown-link href="{{ route('api-tokens.index') }}">
                     {{ __('API Tokens') }}
                 </x-dropdown-link>
             @endif

             <div class="border-t border-gray-200"></div>

             <!-- Authentication -->
             <form method="POST" action="{{ route('logout') }}" x-data>
                 @csrf

                 <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                     {{ __('Log Out') }}
                 </x-dropdown-link>
             </form>
         </x-slot>
     </x-dropdown>
 </div>
 </div>

 <!-- Hamburger -->
 <div class="-me-2 flex items-center sm:hidden">
     <button @click="open = ! open"
         class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
         <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
             <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round"
                 stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
             <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                 stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
         </svg>
     </button>
 </div>
 </div>
 </div>
