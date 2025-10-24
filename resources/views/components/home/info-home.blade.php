@php
    $appName = env('APP_NAME');
@endphp

<div
    class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 {{ request()->is('dashboard') ? 'lg:grid-cols-3' : 'lg:grid-cols-4' }} xl:grid-cols-4 gap-3 mx-2 sm:mx-0 mb-6">
    @if ($appName == 'Al-Aqobah 1')

        <a href="#jadwal-hari-ini" id="scroll-ke-hari-ini"
            class="bg-white hover:bg-green-200 hover:shadow-lg transition duration-300 aspect-auto sm:aspect-square md:aspect-auto lg:aspect-[4/3] xl:aspect-[3/2] shadow-md rounded-md p-6">
            <h3 class="text-lg font-semibold mb-2">Jadwal Hari Ini</h3>
            @if ($jadwalHariIni->isEmpty())
                <p class="text-gray-600 text-xs md:text-sm">Tidak ada jadwal hari ini.</p>
            @else
                <div class="text-2xl font-bold text-red-500">{{ $jadwalHariIni->count() }}</div>
                @if ($jadwalHariIni->count() > 0)
                    @php
                        $jadwalTerdekat = $jadwalHariIni->sortBy('jam_mulai')->first();
                        $jadwalMingguSelanjutnyaTerdekat = $jadwalHariIni->sortBy('jam_mulai')->skip(1)->first();
                    @endphp
                    <p class="text-gray-600 text-xs sm:text-sm md:text-xs mt-1">
                        Terdekat: {{ \Carbon\Carbon::parse($jadwalTerdekat->jam_mulai)->format('H:i') }} WIB
                        @if ($jadwalMingguSelanjutnyaTerdekat)
                            <br>Next:
                            {{ \Carbon\Carbon::parse($jadwalMingguSelanjutnyaTerdekat->jam_mulai)->format('H:i') }} WIB
                        @endif
                    </p>
                @endif
            @endif
        </a>

        <a href="#jadwal-minggu-ini" id="scroll-ke-minggu-ini"
            class="bg-white hover:bg-orange-200 hover:shadow-lg transition duration-300 aspect-auto sm:aspect-square md:aspect-auto lg:aspect-[4/3] xl:aspect-[3/2] shadow-md rounded-md p-6">
            <h3 class="text-lg font-semibold mb-2">Jadwal Belum Terlaksana</h3>
            <div class="text-2xl font-bold text-blue-500">{{ $jadwalBelumTerlaksanaCount }}</div>
        </a>
        <a href="#jadwal-sudah-terlaksana" id="scroll-ke-sudah-terlaksana"
            class="bg-white hover:bg-blue-200 hover:shadow-lg transition duration-300 aspect-auto sm:aspect-square md:aspect-auto lg:aspect-[4/3] xl:aspect-[3/2] shadow-md rounded-md p-6">
            <h3 class="text-lg font-semibold mb-2">Jadwal Sudah Terlaksana</h3>
            <div class="text-2xl font-bold text-green-500">{{ $jadwalSudahTerlaksanaCount }}</div>
        </a>
        <a href="#jadwal-sudah-terlaksana" id="scroll-ke-sudah-terlaksana-2"
            class="bg-white hover:bg-gray-300 hover:shadow-lg transition duration-300 aspect-auto sm:aspect-square md:aspect-auto lg:aspect-[4/3] xl:aspect-[3/2] shadow-md rounded-md p-6">
            <h3 class="text-lg font-semibold mb-2">Total Jadwal</h3>
            <div class="text-2xl font-bold text-gray-700">{{ $totalJadwalCount }}</div>
        </a>

    @endif


    <div id="scroll-jadwal-sholat"
        class="scroll-mt-48 bg-white hover:bg-yellow-100 hover:shadow-lg transition duration-300 shadow-md rounded-md p-6
        col-span-2 aspect-[24/9] md:aspect-[48/9] xl:aspect-[56/9] sm:col-span-2 sm:aspect-auto md:col-span-4 {{ request()->is('dashboard') ? 'lg:col-span-2' : 'lg:col-span-4' }} lg:aspect-auto xl:col-span-4
        ">
        <h3 id="jadwal-sholat-hari-ini" class="text-lg font-semibold mb-2">Jadwal Sholat</h3>
        <div class="flex justify-between">
            <div id="jadwal-sholat">
                <p class="text-gray-600 text-sm">Sedang memuat jadwal sholat...</p>
            </div>
            <div id="jadwal-sholat-next" class="text-right"></div>
        </div>
    </div>

</div>

<script>
    let year,
        month,
        day,
        yearNext,
        monthNext,
        dayNext,
        monthName,
        dayOfWeek,
        tomorow,
        hours,
        minutes,
        seconds,
        today,
        todayNext,
        clock;

    function updateClock() {
        const date = new Date();
        // date.setHours(date.getHours() - 6);
        year = date.getFullYear();
        month = String(date.getMonth() + 1).padStart(2, "0");
        day = String(date.getDate()).padStart(2, "0");

        const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
            "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
        ];
        const daysOfWeek = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
        monthName = monthNames[date.getMonth()];
        dayOfWeek = daysOfWeek[date.getDay()];

        tomorow = new Date(date.getTime() + 24 * 60 * 60 * 1000);
        yearNext = tomorow.getFullYear();
        monthNext = String(tomorow.getMonth() + 1).padStart(2, "0");
        dayNext = String(tomorow.getDate()).padStart(2, "0");
        // console.log(todayNext);

        hours = String(date.getHours()).padStart(2, "0");
        minutes = String(date.getMinutes()).padStart(2, "0");
        seconds = String(date.getSeconds()).padStart(2, "0");

        today = `${year}-${month}-${day}`;
        todayNext = `${yearNext}-${monthNext}-${dayNext}`;
        clock = `${hours}:${minutes}`;

        const clockElement = document.getElementById("live-clock");
        if (clockElement) {
            clockElement.textContent = clock;
        }
        const jadwalSholatNext = document.getElementById("jadwal-sholat-next");
        if (jadwalSholatNext && jadwalSholatNext.querySelector(".live-time")) {
            jadwalSholatNext.querySelector(".live-time").textContent =
                clock + ":" + seconds;
        }
    }

    setInterval(updateClock, 1000);
    document.addEventListener("DOMContentLoaded", () => {
        updateClock();
        fetchJadwalSholat();
    });

    async function fetchJadwalSholat() {
        try {
            const idKota = "0816"; // Kode Kota Palembang
            // const apiUrl = `/proxy/jadwal-sholat?idKota=${idKota}&today=${today}`;
            const apiUrl = `https://api.myquran.com/v2/sholat/jadwal/${idKota}/${today}`;

            const response = await fetch(apiUrl);
            const data = await response.json();
            const ds = data.data.jadwal;

            const jadwalSholatHariIni = document.getElementById(
                "jadwal-sholat-hari-ini"
            );
            const jadwalSholatDiv = document.getElementById("jadwal-sholat");
            const jadwalSholatNext = document.getElementById("jadwal-sholat-next");

            let jadwalHTML = "";
            let nextPrayer = null;

            const prayerTimes = {
                Subuh: ds.subuh,
                Dzuhur: ds.dzuhur,
                Ashar: ds.ashar,
                Maghrib: ds.maghrib,
                Isya: ds.isya,
            };

            for (const waktu in prayerTimes) {
                const prayerTime = prayerTimes[waktu];
                const [prayerHour, prayerMinute] = prayerTime
                    .split(":")
                    .map(Number);
                const [currentHour, currentMinute] = clock.split(":").map(Number);

                let isNext = false;

                if (!nextPrayer) {
                    if (
                        currentHour < prayerHour ||
                        (currentHour === prayerHour && currentMinute < prayerMinute)
                    ) {
                        nextPrayer = waktu;
                        isNext = true;
                    }
                }

                jadwalHTML += `<p class="text-sm ${
                    isNext ? `text-blue-600 font-bold` : `text-gray-700`
                }">${(dayOfWeek == "Jumat" && waktu == "Dzuhur") ? "Jumat" : waktu}: ${prayerTime}</p>`;
                console.log(dayOfWeek);
            }

            let apiUrlNext;
            let responseNext;
            let dataNext;
            if (!nextPrayer) {
                apiUrlNext = `https://api.myquran.com/v2/sholat/jadwal/${idKota}/${todayNext}`;
                responseNext = await fetch(apiUrl);
                dataNext = await responseNext.json();
                jadwalHTML +=
                    `<p class="text-sm text-blue-600 font-bold">Subuh Besok: ${dataNext.data.jadwal.subuh}</p>`;
            }

            jadwalSholatHariIni.innerHTML = `Hari ${dayOfWeek}, ${day} ${monthName} ${year}`;
            jadwalSholatDiv.innerHTML = jadwalHTML;


            if (jadwalSholatNext && nextPrayer) {
                jadwalSholatNext.innerHTML = `
                        <h1 class="text-4xl font-bold text-blue-600">${prayerTimes[nextPrayer]}</h1>
                        <p class="text-green-600 text-sm">Waktu Sholat ${(dayOfWeek == "Jumat" && nextPrayer == "Dzuhur") ? "Jumat" : nextPrayer}</p>
                        <p class="text-gray-600 text-sm live-time"></p>
                    `;
            } else {
                jadwalSholatNext.innerHTML = `
                        <h1 class="text-4xl font-bold text-blue-600">${dataNext.data.jadwal.subuh}</h1>
                        <p class="text-green-600 text-sm">Waktu Sholat Subuh, Besok</p>
                        <p class="text-gray-600 text-sm live-time"></p>
                    `;
            }
        } catch (error) {
            console.error("Gagal mengambil data jadwal sholat:", error);
            document.getElementById("jadwal-sholat").innerHTML =
                '<p class="text-red-500 text-sm">Gagal memuat jadwal sholat.</p>';
        }
    }
</script>
