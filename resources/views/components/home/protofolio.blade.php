{{-- <div class="relative flex min-h-screen flex-col justify-center overflow-hidden bg-gray-50 py-6 sm:py-12">
    <div
        class="group relative cursor-pointer overflow-hidden bg-white px-6 pt-10 pb-8 shadow-xl ring-1 ring-gray-900/5 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl sm:mx-auto sm:max-w-sm sm:rounded-lg sm:px-10">
        <span class="absolute top-10 z-0 h-20 w-20 rounded-full bg-sky-500 transition-all duration-300 group-hover:scale-[10]"></span>
        <div class="relative z-10 mx-auto max-w-md">
            <span class="grid h-20 w-20 place-items-center rounded-full bg-sky-500 transition-all duration-300 group-hover:bg-sky-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-10 w-10 text-white transition-all">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.778-.332 48.294 48.294 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                </svg>
            </span>
            <div
                class="space-y-6 pt-5 text-base leading-7 text-gray-600 transition-all duration-300 group-hover:text-white/90">
                <p>Perfect for learning how the framework works, prototyping a new idea, or creating a demo to share
                    online.</p>
            </div>
            <div class="pt-5 text-base font-semibold leading-7">
                <p>
                    <a href="#" class="text-sky-500 transition-all duration-300 group-hover:text-white">Read the docs
                        &rarr;
                    </a>
                </p>
            </div>
        </div>
    </div>
</div> --}}

@php
    $appName = env('APP_NAME');
@endphp

<div id="scroll-home" class="scroll-mt-20 bg-gray-200 mt-16">
    <div
        class="container mx-auto py-10 px-4 sm:px-6 md:px-0 lg:px-20 xl:px-40 md:flex md:justify-center md:items-center">
        <div class="md:w-1/2 md:mr-8">

            @if ($appName == 'Al-Aqobah 1')
                <h1 class="text-3xl font-bold mb-4">Al-Aqobah 1</h1>
                <p class="text-lg mb-4 text-justify mr-3">
                    Masjid Al-Aqobah 1 merupakan salah satu rumah ibadah yang terletak di Palembang.
                    Sebagai bagian dari komunitas muslim setempat, masjid ini menjadi pusat kegiatan
                    keagamaan, pendidikan, dan sosial bagi warga sekitar. Dengan arsitektur yang
                    mungkin memiliki ciri khas tersendiri, Al-Aqobah 1 hadir sebagai tempat yang
                    khusyuk untuk beribadah dan mempererat tali silaturahmi antar umat.
                    Keberadaannya memiliki peran penting dalam kehidupan spiritual dan kebersamaan
                    masyarakat di lingkungannya.
                </p>
                <div class="flex col-span-1 gap-4">
                    <a href="/#grup-jadwal" id="scroll-ke-grup-jadwal-3"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Lihat Kegiatan
                    </a>
                    <a href="#scroll-map" id="scroll-ke-map-2" rel="noopener noreferrer">
                        <img src="/images/icons/gmaps.png" class="w-9" />
                    </a>
                    <a href="https://wa.me/628985655826" target="_blank" rel="noopener noreferrer">
                        <img src="/images/icons/whatsapp.png" class="w-9" />
                    </a>
                </div>
        </div>
        <div class="md:w-1/2 mt-8 md:mt-0">
            <img src="/images/masjid/Pic 1_Al-Aqobah 1.jpg" alt="Responsive Design"
                class="w-full h-full object-cover rounded-md shadow-md">
        </div>
    @elseif ($appName == 'PT. Telkominfra')
        <h1 class="text-3xl font-bold mb-4">PT. Telkominfra</h1>
        <p class="text-lg mb-4 text-justify mr-3">
            Aset ini adalah infrastruktur transmisi kritis yang terletak di Palembang. Berfungsi sebagai pusat
            operasional jaringan, aset ini mendukung konektivitas data dan telekomunikasi bagi komunitas setempat.
            Dengan teknologi andal, ia memastikan transmisi bandwidth tinggi serta ketersediaan layanan jaringan yang
            prima. Keberadaannya esensial untuk mendukung aktivitas digital, bisnis, dan memperkuat inklusi digital bagi
            seluruh masyarakat di wilayah operasionalnya.
        </p>
        <div class="flex col-span-1 gap-4">
            <a  href="/keluh-pengguna/create"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Form Masukkan
            </a>
            <a href="#scroll-map" id="scroll-ke-map-2" rel="noopener noreferrer">
                <img src="/images/icons/gmaps.png" class="w-9" />
            </a>
            <a href="https://wa.me/628985655826" target="_blank" rel="noopener noreferrer">
                <img src="/images/icons/whatsapp.png" class="w-9" />
            </a>
        </div>
    </div>
    <div class="md:w-1/2 mt-8 md:mt-0">
        <img src="/images/telkominfra/Pic 1_Telkominfra.jpg" alt="Responsive Design"
            class="w-full h-full object-cover rounded-md shadow-md">
    </div>
    @endif

</div>

</div>



{{-- <br><br><br><br>


<main class="dark:bg-gray-800 bg-white relative overflow-hidden h-screen">
    <header class="h-24 sm:h-32 flex items-center z-30 w-full">
        <div class="container mx-auto px-6 flex items-center justify-between">
            <div class="uppercase text-gray-800 dark:text-white font-black text-3xl">
                Watch.ME
            </div>
            <div class="flex items-center">
                <nav class="font-sen text-gray-800 dark:text-white uppercase text-lg lg:flex items-center hidden">
                    <a href="#" class="py-2 px-6 flex">
                        Home
                    </a>
                    <a href="#" class="py-2 px-6 flex">
                        Watch
                    </a>
                    <a href="#" class="py-2 px-6 flex">
                        Product
                    </a>
                    <a href="#" class="py-2 px-6 flex">
                        Contact
                    </a>
                    <a href="#" class="py-2 px-6 flex">
                        Carrer
                    </a>
                </nav>
                <button class="lg:hidden flex flex-col ml-4">
                    <span class="w-6 h-1 bg-gray-800 dark:bg-white mb-1">
                    </span>
                    <span class="w-6 h-1 bg-gray-800 dark:bg-white mb-1">
                    </span>
                    <span class="w-6 h-1 bg-gray-800 dark:bg-white mb-1">
                    </span>
                </button>
            </div>
        </div>
    </header>
    <div class="bg-white dark:bg-gray-800 flex relative z-20 items-center overflow-hidden">
        <div class="container mx-auto px-6 flex relative py-16">
            <div class="sm:w-2/3 lg:w-2/5 flex flex-col relative z-20">
                <span class="w-20 h-2 bg-gray-800 dark:bg-white mb-12">
                </span>
                <h1 class="font-bebas-neue uppercase text-6xl sm:text-8xl font-black flex flex-col leading-none dark:text-white text-gray-800">
                    Be on
                    <span class="text-5xl sm:text-7xl">
                        Time
                    </span>
                </h1>
                <p class="text-sm sm:text-base text-gray-700 dark:text-white">
                    Dimension of reality that makes change possible and understandable. An indefinite and homogeneous environment in which natural events and human existence take place.
                </p>
                <div class="flex mt-8">
                    <a href="#" class="uppercase py-2 px-4 rounded-lg bg-pink-500 border-2 border-transparent text-white text-md mr-4 hover:bg-pink-400">
                        Get started
                    </a>
                    <a href="#" class="uppercase py-2 px-4 rounded-lg bg-transparent border-2 border-pink-500 text-pink-500 dark:text-white hover:bg-pink-500 hover:text-white text-md">
                        Read more
                    </a>
                </div>
            </div>
            <div class="hidden sm:block sm:w-1/3 lg:w-3/5 relative">
                <img src="https://www.tailwind-kit.com/images/object/10.png" class="max-w-xs md:max-w-sm m-auto"/>
            </div>
        </div>
    </div>
</main> --}}



{{-- <div class="px-2 py-20 w-full flex justify-center">
    <div class="bg-white lg:mx-8 lg:flex lg:max-w-5xl lg:shadow-lg rounded-lg">
        <div class="lg:w-1/2">
            <div class="lg:scale-110 h-80 bg-cover lg:h-full rounded-b-none border lg:rounded-lg"
                style="background-image:url('https://images.unsplash.com/photo-1517694712202-14dd9538aa97')">
            </div>
        </div>
        <div class="py-12 px-6 lg:px-12 max-w-xl lg:max-w-5xl lg:w-1/2 rounded-t-none border lg:rounded-lg">
            <h2 class="text-3xl text-gray-800 font-bold">
                Promoting Sustainable Lifestyle Choices
                <span class="text-indigo-600">Choices</span>
            </h2>
            <p class="mt-4 text-gray-600">
                The "Eco-Tracker" project aims to create a web-based platform that encourages individuals to adopt
                sustainable lifestyle choices and actively contribute to environmental conservation. The platform will
                provide users with personalized tracking, education, and engagement features to empower them to make
                eco-friendly decisions in various aspects of their lives.
            </p>
            <div class="mt-8">
                <a href="#" class="bg-gray-900 text-gray-100 px-5 py-3 font-semibold rounded">Start Now</a>
            </div>
        </div>
    </div>
</div>

<div class="min-h-screen flex flex-col p-8 sm:p-16 md:p-24 justify-center bg-white">
    <!-- Themes: blue, purple and teal -->
    <div data-theme="teal" class="mx-auto max-w-6xl">
      <h2 class="sr-only">Featured case study</h2>
      <section class="font-sans text-black">
        <div class="[ lg:flex lg:items-center ] [ fancy-corners fancy-corners--large fancy-corners--top-left fancy-corners--bottom-right ]">
          <div class="flex-shrink-0 self-stretch sm:flex-basis-40 md:flex-basis-50 xl:flex-basis-60">
            <div class="h-full">
              <article class="h-full">
                <div class="h-full">
                  <img class="h-full object-cover" src="https://inviqa.com/sites/default/files/styles/pullout/public/2020-08/XD-1.jpeg?h=f75d236a&itok=PBoXPDmW" width="733" height="412" alt='""' typeof="foaf:Image" />
                </div>
              </article>
            </div>
          </div>
          <div class="p-6 bg-grey">
            <div class="leading-relaxed">
              <h2 class="leading-tight text-4xl font-bold">CXcon: Experience Transformation</h2>
              <p class="mt-4">Our second CXcon in October was dedicated to experience transformation. The free one-day virtual event&nbsp;brought together 230+ heads of digital, thought leaders, and UX practitioners to discuss all aspects of experience design..</p>
              <p class="mt-4">In a jam-packed day filled with keynote sessions, panels, and virtual networking we explored topics including design leadership, UX ethics, designing for emotion and innovation at scale.</p>
              <p><a class="mt-4 button button--secondary" href="https://inviqa.com/cxcon-experience-transformation">Explore this event</a></p>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>

  

<section class="container bg-red-600 mx-auto">
    <section class="relative transform duration-500 hover:shadow-2xl cursor-pointer hover:-translate-y-1 ">
        <img class="xl:max-w-6xl" src="/images/masjid/Pic 1_Al-Aqobah 1.jpg" alt="">
        <div class="content bg-white p-2 pt-8 md:p-12 pb-12 lg:max-w-lg w-full lg:absolute top-48 right-5">
            <div class="flex justify-between font-bold text-sm">
                <p>Product Review</p>
                <p class="text-gray-400">17th March, 2021</p>
            </div>
            <h2 class="text-3xl font-semibold mt-4 md:mt-10">Coffee From Heaven</h2>
            <p class="my-3 text-justify font-medium text-gray-700 leading-relaxed">Lorem ipsum dolor sit amet
                consectetur adipisicing elit. Autem aperiam nulla cupiditate saepe sed quis veritatis minus rem adipisci
                aliquid.</p>
            <button class="mt-2 md:mt-5 p-3 px-5 bg-black text-white font-bold text-sm hover:bg-purple-800">Read
          More</button>
        </div>
    </section>
</section> --}}
