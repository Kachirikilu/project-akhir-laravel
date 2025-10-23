@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    {{-- <link rel="stylesheet" href="{{ secure_asset('css/leaflet/leaflet.css') }}" /> --}}
    <style>
        #map {
            height: 400px;
            width: 100%;
        }

        <style>.leaflet-control.info {
            z-index: 1000;
        }

        .info i {
            display: inline-block !important;
        }
    </style>
@endpush

<div class="mt-8">
    <h3 class="text-2xl font-extrabold text-gray-800 mb-6 border-b pb-2">Visualisasi Data Log</h3>

    <div class="mb-4 flex flex-wrap gap-2">
        <button type="button" data-value="rsrp"
            class="metric-btn w-1/2 sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-150 shadow-md">
            RSRP
        </button>
        <button type="button" data-value="rssi"
            class="metric-btn w-1/2 sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-150 shadow-md">
            RSSI
        </button>
        <button type="button" data-value="rsrq"
            class="metric-btn w-1/2 sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-150 shadow-md">
            RSRQ
        </button>
        <button type="button" data-value="sinr"
            class="metric-btn w-1/2 sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-150 shadow-md">
            SINR
        </button>
    </div>



    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="grid grid-cols-1 gap-4">
            @forelse (collect($mapsData)->where('status', 'Before') as $mapItem)
                <x-telkominfra.map.item-map :mapItem="$mapItem" />
            @empty
                <div class="lg:col-span-3">
                    <p class="text-center text-gray-500 py-10 border rounded-lg bg-gray-50">
                        Tidak ada data sinyal 'Before' yang ditemukan untuk perjalanan ini.
                    </p>
                </div>
            @endforelse
        </div>
        <div class="grid grid-cols-1 gap-4">
            @forelse (collect($mapsData)->where('status', 'After') as $mapItem)
                <x-telkominfra.map.item-map :mapItem="$mapItem" />
            @empty
                <div class="lg:col-span-3">
                    <p class="text-center text-gray-500 py-10 border rounded-lg bg-gray-50">
                        Tidak ada data sinyal 'After' yang ditemukan untuk perjalanan ini.
                    </p>
                </div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    {{-- <script src="{{ secure_asset('js/leaflet/leaflet.js') }}"></script> --}}

    <script>
        var allMapsData = @json($mapsData ?? []);
        var selectedMetric = "rssi";

        function setMetric(metric) {
            selectedMetric = metric;
            console.log("Metric terpilih:", metric);
            redrawMaps();
        }

        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll(".metric-btn").forEach(function(btn) {
                btn.addEventListener("click", function() {
                    var metric = this.getAttribute("data-value");
                    setMetric(metric); // <- inilah yang belum kamu panggil
                });
            });

            // ----------------------------------------------------------------------
            // B. FUNGSI PENENTU WARNA RSRQ (Tetap sama)
            // ----------------------------------------------------------------------
            function getColorByRSRP(rsrp) {
                if (rsrp >= -80) return '#2ECC71'; // hijau
                if (rsrp >= -90) return '#F4D03E'; // kuning
                if (rsrp >= -100) return '#FF8C00'; // oranye
                return '#FF0000'; // merah
            }

            function getColorByRSSI(rssi) {
                if (rssi >= -65) return '#2ECC71';
                if (rssi >= -75) return '#F4D03E';
                if (rssi >= -85) return '#FF8C00';
                return '#FF0000';
            }

            function getColorByRSRQ(rsrq) {
                if (rsrq >= -3) return '#0051ff';
                if (rsrq >= -5) return '#16bef7';
                if (rsrq >= -7) return '#00ffc8';
                if (rsrq >= -9) return '#2ECC71';
                if (rsrq >= -11) return '#F4D03E';
                if (rsrq >= -13) return '#FF8C00';
                if (rsrq >= -15) return '#FF4500';
                if (rsrq >= -17) return '#d82a17';
                if (rsrq >= -19) return '#800000';
                if (rsrq >= -20) return '#000000';
                return '#CCCCCC';
            }

            function getColorBySINR(sinr) {
                if (sinr >= 20) return '#2ECC71';
                if (sinr >= 10) return '#F4D03E';
                if (sinr >= 0) return '#FF8C00';
                return '#FF0000';
            }

            // C & D & E & F. LOOPING DAN INISIALISASI PETA UNTUK SETIAP LOG
            allMapsData.forEach(function(mapItem) {

                var mapId = 'map-' + mapItem.id;
                var centerCoords = mapItem.centerCoords;
                var visualData = mapItem.visualData;

                // 1. Inisialisasi Peta Baru
                var mapElement = document.getElementById(mapId);
                if (!mapElement) {
                    return;
                }

                var map = L.map(mapId).setView(centerCoords, 18);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                if (visualData.length > 0) {
                    // 2. Format Ulang Data Titik Pengukuran
                    var measurementPoints = visualData.map(function(data) {
                        return [
                            data.latitude,
                            data.longitude,
                            data.rsrp,
                            data.rssi,
                            data.rsrq,
                            data.sinr,
                            data.pci,
                            data.earfcn,
                            data.band,
                            data.frekuensi,
                            data.timestamp_waktu
                        ];
                    });

                    // 3. LOGIKA SEGMENTED POLYLINE 
                    for (var i = 1; i < measurementPoints.length; i++) {
                        var startPoint = measurementPoints[i - 1];
                        var endPoint = measurementPoints[i];

                        var rsrp = endPoint[2];
                        var rssi = endPoint[3];
                        var rsrq = endPoint[4];
                        var sinr = endPoint[5];
                        var pci = endPoint[6];
                        var earfcn = endPoint[7];
                        var band = endPoint[8];
                        var frequency = endPoint[9];
                        var latitude = endPoint[0];
                        var longitude = endPoint[1];
                        var waktu = endPoint[10];

                        var segementColor;
                        if (selectedMetric == 'rsrp') {
                            segmentColor = getColorByRSRP(rsrp);
                        } else if (selectedMetric == 'rssi') {
                            segmentColor = getColorByRSSI(rssi);
                        } else if (selectedMetric == 'rsrq') {
                            segmentColor = getColorByRSRQ(rsrq);
                        } else if (selectedMetric == 'sinr') {
                            segmentColor = getColorBySINR(sinr);
                        }

                        var segment = L.polyline([
                            [startPoint[0], startPoint[1]],
                            [endPoint[0], endPoint[1]]
                        ], {
                            color: segmentColor,
                            weight: 6,
                            opacity: 0.9
                        }).addTo(map);

                        segment.bindPopup(
                            "Segmen Drive Test<br>" +
                            "RSRP: <b>" + rsrp + " dBm</b><br>" +
                            "RSRQ: <b>" + rsrq + " dB</b><br>" +
                            "SINR: <b>" + sinr + " dB</b><br>" +
                            "PCI: <b>" + pci + "</b><br>" +
                            "Earfcn: <b>" + earfcn + "</b><br>" +
                            "Band: <b>" + band + "</b><br>" +
                            "Freq: <b>" + frequency + "</b><br>" +
                            "Waktu: <b>" + waktu + "</b><br>" +
                            "Koordinat: " + latitude.toFixed(6) + ", " + longitude.toFixed(6)
                        );
                    }

                    // 4. MARKER PENGUKURAN (Titik Data)
                    var measurementIcon = L.icon({
                        iconUrl: '{{ secure_asset('css/leaflet/images/marker-icon.png') }}',
                        // iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
                        iconSize: [0, 0],
                        iconAnchor: [7, 25],
                        popupAnchor: [1, -90]
                    });

                    measurementPoints.forEach(function(point) {
                        var rsrp = point[2];
                        var rsrq = point[3];
                        var infoTambahan = point[4];

                        L.marker([point[0], point[1]], {
                                icon: measurementIcon
                            })
                            .addTo(map)
                            .bindPopup(
                                "<b>Titik Data</b><br>" +
                                "RSRP: " + rsrp + " dBm<br>" +
                                "RSRQ: " + rsrq + " dB<br>" +
                                infoTambahan + "<br>" +
                                "Koordinat: " + point[0].toFixed(6) + ", " + point[1].toFixed(6)
                            );
                    });

                    var latLngs = measurementPoints.map(p => [p[0], p[1]]);
                    map.fitBounds(L.polyline(latLngs).getBounds());
                }
                // 5. TAMBAH LEGENDA 
                var legend = L.control({
                    position: 'bottomright'
                });
                legend.onAdd = function(map) {

                    var div = L.DomUtil.create('div',
                            'info p-2 text-sm bg-white bg-opacity-90 shadow-md rounded-md'),

                        rsrq_colors = ['#000000', '#800000', '#D82A17', '#FF4500', '#FF8C00', '#F4D03E',
                            '#2ECC71', '#00FFC8', '#16BEF7', '#0051FF'
                        ]

                    rsrq_labels = [
                        "< -20", // Hitam
                        "-20 s/d -19", // Abu-abu Kemerahan
                        "-19 s/d -17", // Merah
                        "-17 s/d -15", // Orange
                        "-15 s/d -12", // Kuning Tua
                        "-12 s/d -9", // Kuning Terang
                        "-9 s/d -7",
                        "-7 s/d -5",
                        "-5 s/d -3",
                        "-3 s/d 0"
                    ];

                    div.innerHTML += '<b class="font-bold" style="font-size: 12px;">RSRQ (dB)</b><br>';

                    for (var i = 0; i < rsrq_colors.length; i++) {
                        div.innerHTML +=
                            '<i style="background:' + rsrq_colors[i] +
                            '; width: 12px; height: 12px; transform: translateY(4px); float: left; margin-right: 6px; opacity: 0.7; border-radius: 3px;"></i> ' +
                            '<span style="font-size: 10px;">' + rsrq_labels[i] + '</span><br>';
                    }

                    return div;
                };
                legend.addTo(map);

            });
        });
    </script>
@endpush
