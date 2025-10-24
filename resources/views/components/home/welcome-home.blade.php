@php
    $appName = env('APP_NAME');
@endphp
<div class="header-with-backdrop-blur text-white mx-2 sm:mx-0 shadow-md mt-2 mb-3 rounded-md">
    <div class="w-full h-full py-20 backdrop-blur-sm hover:backdrop-brightness-50 duration-500 ease-in-out backdrop-brightness-75 flex flex-col lg:flex-row justify-between items-center rounded-md">
        <a href="#scroll-map" id="scroll-ke-map" class="text-3xl font-semibold mb-1 lg:ml-10 sm:mb-2 lg:mb-0">
            @if ($appName == 'Al-Aqobah 1')
                Al-Aqobah 1
            @elseif ($appName == 'PT. Telkominfra')
                PT. Telkominfra
            @endif
        </a>
        <div class="flex items-center">
            @auth
                <span class="lg:mr-10">Selamat datang, {{ Auth::user()->name }}</span>
            @else
                <span class="lg:mr-10">Selamat datang, Pengunjung</span> {{-- Atau hilangkan baris ini jika tidak ingin menampilkan apa pun --}}
            @endauth
        </div>
    </div>
</div>


    {{-- `<div class="bg-white shadow-md rounded-lg p-8 w-96">
        <h1 class="text-2xl font-bold mb-4 text-center text-gray-800">Kontrol Lampu</h1>

        <div class="mb-4">
            <p class="text-gray-700">Status Koneksi:</p>
            <div id="statusIndicator" class="text-sm text-gray-600 italic">Menghubungkan...</div>
        </div>

        <div class="mb-6">
            <p class="text-gray-700">Status Lampu:</p>
            <div id="lampStatus" class="text-xl font-semibold text-center text-gray-800 py-2">OFF</div>
        </div>

        <div class="flex justify-center space-x-4">
            <button id="onButton" onclick="toggleLamp('On')" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                ON
            </button>
            <button id="offButton" onclick="toggleLamp('Off')" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                OFF
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/mqtt@4.3.7/dist/mqtt.min.js"></script>
    <script>
        const brokerUrl = "{{ env('MQTT_BROKER_URL') }}";
        const topic = "{{ env('MQTT_TOPIC') }}";
        const client = mqtt.connect(brokerUrl, {
            username: "{{ env('MQTT_AUTH_USERNAME') }}",
            password: "{{ env('MQTT_AUTH_PASSWORD') }}",
            reconnectPeriod: 1000,
            keepalive: 60
        });

        client.on('connect', function () {
            console.log('Terhubung ke broker MQTT');
            document.getElementById('statusIndicator').textContent = 'Connected to MQTT Broker';
            client.subscribe(topic, function (err) {
                if (err) {
                    console.log(`Gagal berlangganan ke topik: ${topic}`);
                } else {
                    console.log(`Berlangganan ke topik: ${topic}`);
                }
            });
        });

        client.on('message', function (receivedTopic, message) {
            if (receivedTopic === topic) {
                const status = message.toString();
                document.getElementById('lampStatus').textContent = status.toUpperCase();
                updateButtonStates(status);
            }
        });

        function toggleLamp(status) {
            const newStatus = status === 'On' ? 'On' : 'Off';
            client.publish(topic, newStatus);
        }

        client.on('offline', function () {
            document.getElementById('statusIndicator').textContent = 'Disconnected from MQTT Broker';
            document.getElementById('lampStatus').textContent = 'OFFLINE';
            disableButtons();
        });

        function updateButtonStates(status) {
            const onButton = document.getElementById('onButton');
            const offButton = document.getElementById('offButton');
            if (status.toUpperCase() === 'ON') {
                onButton.classList.add('bg-green-700', 'cursor-not-allowed');
                onButton.classList.remove('bg-green-500', 'hover:bg-green-700', 'cursor-pointer');
                offButton.classList.remove('bg-red-700', 'cursor-not-allowed');
                offButton.classList.add('bg-red-500', 'hover:bg-red-700', 'cursor-pointer');
            } else if (status.toUpperCase() === 'OFF') {
                offButton.classList.add('bg-red-700', 'cursor-not-allowed');
                offButton.classList.remove('bg-red-500', 'hover:bg-red-700', 'cursor-pointer');
                onButton.classList.remove('bg-green-700', 'cursor-not-allowed');
                onButton.classList.add('bg-green-500', 'hover:bg-green-700', 'cursor-pointer');
            }
        }

        function disableButtons() {
            const onButton = document.getElementById('onButton');
            const offButton = document.getElementById('offButton');
            onButton.classList.add('bg-gray-400', 'cursor-not-allowed');
            onButton.classList.remove('bg-green-500', 'hover:bg-green-700', 'cursor-pointer');
            offButton.classList.add('bg-gray-400', 'cursor-not-allowed');
            offButton.classList.remove('bg-red-500', 'hover:bg-red-700', 'cursor-pointer');
        }

        // Inisialisasi status tombol saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            updateButtonStates(document.getElementById('lampStatus').textContent);
        });
    </script> --}}


<style>
.header-with-backdrop-blur {
    background-image: url('/images/masjid/Pic 5_Al-Aqobah 1.jpg');
    background-size: cover;
    background-position-y: 50%;
}
</style>