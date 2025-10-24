@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    {{-- <link rel="stylesheet" href="{{ secure_asset('css/leaflet/leaflet.css') }}" /> --}}

    <style>
        #map {
            height: 400px;
            width: 100%;
        }

        .leaflet-control.info {
            z-index: 1000;
        }

        .info i {
            display: inline-block !important;
        }

        .metric-btn-active {
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.5);
            transform: scale(0.88);
            /* border: 2px solid white; */
        }

        .dot:checked {
            transform: scale(0.5);
        }
    </style>
@endpush

<div class="mt-8">
    <h3 class="text-2xl font-extrabold text-gray-800 mb-6 border-b pb-2">Visualisasi Data Log</h3>

    <div class="mb-4 grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-2">
        <button type="button" data-value="rsrp"
            class="metric-btn metric-btn-active w-1/2 w-auto bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-1 rounded-lg transition duration-150 shadow-md">
            RSRP
        </button>
        <button type="button" data-value="rssi"
            class="metric-btn w-1/2 w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-1 rounded-lg transition duration-150 shadow-md">
            RSSI
        </button>
        <button type="button" data-value="rsrq"
            class="metric-btn w-1/2 w-auto bg-orange-500 hover:bg-orange-600 text-white font-semibold px-6 py-1 rounded-lg transition duration-150 shadow-md">
            RSRQ
        </button>
        <button type="button" data-value="sinr"
            class="metric-btn w-1/2 w-auto bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-1 rounded-lg transition duration-150 shadow-md">
            SINR
        </button>
        <button type="button" data-value="cell"
            class="metric-btn w-1/2 w-auto bg-yellow-400 hover:bg-yellow-500 text-white font-semibold px-6 py-1 rounded-lg transition duration-150 shadow-md">
            CELL
        </button>
        <button type="button" data-value="serv"
            class="metric-btn w-1/2 w-auto bg-gray-600 hover:bg-gray-700 text-white font-semibold px-6 py-1 rounded-lg transition duration-150 shadow-md">
            SERV
        </button>
        <button type="button" data-value="bw"
            class="metric-btn w-1/2 w-auto bg-gray-800 hover:bg-gray-900 text-white font-semibold px-6 py-1 rounded-lg transition duration-150 shadow-md">
            BW
        </button>
    </div>

    <div class="flex mx-4 mt-2 mb-3">
        <label for="ratio-switch" class="flex items-center cursor-pointer select-none">
            <div class="relative">
                <input type="checkbox" id="ratio-switch" class="sr-only peer">
                <div class="w-12 h-6 bg-gray-300 rounded-full peer-checked:bg-green-400 transition-colors duration-300">
                </div>
                <div
                    class="dot absolute top-1 left-1 w-4 h-4 bg-white rounded-full transition-all duration-300 peer-checked:translate-x-6">
                </div>
            </div>
            <span class="text-sm text-gray-600 ml-2 font-semibold">Rasio Detail</span>
        </label>
    </div>


    <div id="metric-keterangan" class="mb-3 ml-2">Metrik Aktif: <b class="text-green-600">Reference Signal Received
            Power</b></div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="grid grid-cols-1 gap-4">
            @foreach (collect($mapsData)->where('status', 'Before') as $mapItem)
                @if (!is_int($mapItem['id']))
                    <x-telkominfra.maintenance.show.maps.item-map :mapItem="$mapItem" />
                @endif
            @endforeach
        </div>
        <div class="grid grid-cols-1 gap-4">
            @foreach (collect($mapsData)->where('status', 'After') as $mapItem)
                @if (!is_int($mapItem['id']))
                    <x-telkominfra.maintenance.show.maps.item-map :mapItem="$mapItem" />
                @endif
            @endforeach
        </div>
    </div>

    @if(Auth::user()?->admin)

        @if ($mapsData)
            <div class="relative flex items-center my-6">
                <div class="flex-grow border-t border-gray-300"></div>
                <span class="mx-4 text-gray-500 text-sm font-medium">Data Log Terpisah</span>
                <div class="flex-grow border-t border-gray-300"></div>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="grid grid-cols-1 gap-4">
                @foreach (collect($mapsData)->where('status', 'Before') as $mapItem)
                    @if (is_int($mapItem['id']))
                        <x-telkominfra.maintenance.show.maps.item-map :mapItem="$mapItem" />
                    @endif
                @endforeach
            </div>
            <div class="grid grid-cols-1 gap-4">
                @foreach (collect($mapsData)->where('status', 'After') as $mapItem)
                    @if (is_int($mapItem['id']))
                        <x-telkominfra.maintenance.show.maps.item-map :mapItem="$mapItem" />
                    @endif
                @endforeach
            </div>
        </div>

    @endif
</div>


@push('scripts')
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    {{-- <script src="{{ secure_asset('js/leaflet/leaflet.js') }}"></script> --}}

    <script>
        var allMapsData = @json($mapsData ?? []);
        var selectedMetric = "rsrp";
        var mapRegistry = {};

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll(".metric-btn").forEach(function(btn) {
                btn.addEventListener("click", function() {
                    var metric = this.getAttribute("data-value");
                    setMetric(metric);
                });
            });
            allMapsData.forEach(function(mapItem) {
                initMap(mapItem);
            });
        });

        function setMetric(metric) {
            selectedMetric = metric;
            redrawMaps();
        }

        function initMap(mapItem) {
            updateMetricButtonStyles();
            var mapId = 'map-' + mapItem.id;
            var mapElement = document.getElementById(mapId);
            var visualData = mapItem.visualData;
            if (!mapElement) return;

            var map = L.map(mapId).setView(mapItem.centerCoords, 17);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            var layer = L.layerGroup().addTo(map);

            mapRegistry[mapId] = {
                map: map,
                layer: layer,
                data: visualData,
                legend: null
            };

            drawPolyline(mapId);
        }

        function redrawMaps() {
            updateMetricButtonStyles();

            for (let mapId in mapRegistry) {
                drawPolyline(mapId);
            }
        }

        function drawPolyline(mapId) {
            let entry = mapRegistry[mapId];
            let map = entry.map;
            let layer = entry.layer;
            let visualData = entry.data;

            layer.clearLayers();
            if (!visualData || visualData.length === 0) return;

            let measurementPoints = visualData.map(function(d) {
                return [d.latitude, d.longitude, d.rsrp, d.rssi, d.rsrq, d.sinr, d.pci, d.earfcn, d.band, d
                    .frekuensi, d.bandwidth, d.n_value, d.cell_id, d.timestamp_waktu
                ];
            });

            for (let i = 1; i < measurementPoints.length; i++) {
                let start = measurementPoints[i - 1];
                let end = measurementPoints[i];

                let rsrp = end[2],
                    rssi = end[3],
                    rsrq = end[4],
                    sinr = end[5],
                    pci = end[6],
                    earfcn = end[7],
                    band = end[8],
                    frequency = end[9],
                    bandwidth = end[10],
                    n_value = end[11],
                    latitude = end[0],
                    longitude = end[1],
                    cell = end[12],
                    waktu = end[13];

                let color;
                if (selectedMetric === "rsrp") color = getMetricInfo('rsrp', rsrp).color;
                if (selectedMetric === "rssi") color = getMetricInfo('rssi', rssi).color;
                if (selectedMetric === "rsrq") color = getMetricInfo('rsrq', rsrq).color;
                if (selectedMetric === "sinr") color = getMetricInfo('sinr', sinr).color;
                if (selectedMetric === "serv") color = getColorBySERV(frequency);
                if (selectedMetric === "bw") color = getColorByBW(bandwidth);
                if (selectedMetric === "cell") color = getColorByCell(cell);


                // let seg = L.polyline([
                //     [start[0], start[1]],
                //     [end[0], end[1]]
                // ], {
                //     color: color,
                //     weight: 6,
                //     opacity: 0.8
                // });

                let seg = L.circleMarker([start[0], start[1]], {
                    radius: 3,
                    fillColor: color,
                    color: '#A00000',
                    weight: 0,
                    opacity: 1,
                    fillOpacity: 0.8
                });

                let freValue = frequency;
                // if (freValue == 2300 || freValue == 2400) {
                //     freValue = "2300-2400";
                // }

                const popUp = !isDetailRatio ? seg.bindPopup(
                        "RSRP: <b>" + rsrp.toFixed(1) + " dBm</b><br>" +
                        "RSSI: <b>" + rssi.toFixed(1) + " dBm</b><br>" +
                        "RSRQ: <b>" + rsrq.toFixed(1) + " dB</b><br>" +
                        "SINR: <b>" + sinr.toFixed(1) + " dB</b><br>" +
                        "Latitude: <b>" + latitude.toFixed(6) + "</b><br>" +
                        "Longitude: <b>" + longitude.toFixed(6) + "</b><br>" +
                        "Waktu: <b>" + waktu + "</b>"
                    ) :
                    seg.bindPopup(
                        "RSRP: <b>" + rsrp.toFixed(1) + " dBm</b><br>" +
                        "RSSI: <b>" + rssi.toFixed(1) + " dBm</b><br>" +
                        "RSRQ: <b>" + rsrq.toFixed(1) + " dB</b><br>" +
                        "SINR: <b>" + sinr.toFixed(1) + " dB</b><br>" +
                        "Cell ID: <b>" + cell + "</b><br>" +
                        "PCI: <b>" + pci + "</b><br>" +
                        "Earfcn: <b>" + earfcn + "</b><br>" +
                        "Band: <b>" + band + "</b><br>" +
                        "Frequency: <b>" + freValue + " MHz</b><br>" +
                        "Bandwidth: <b>" + bandwidth + " MHz</b><br>" +
                        "N-Value: <b>" + n_value + "</b><br>" +
                        "Latitude: <b>" + latitude.toFixed(6) + "</b><br>" +
                        "Longitude: <b>" + longitude.toFixed(6) + "</b><br>" +
                        "Waktu: <b>" + waktu + "</b>"
                    );

                layer.addLayer(seg);
            }

            // Atur Legend dinamis
            if (entry.legend) {
                map.removeControl(entry.legend);
            }
            entry.legend = getColorLegend(selectedMetric, mapId);
            entry.legend.addTo(map);
        }

        // ========== Warna Metric ==========

        let isDetailRatio = false;

        document.getElementById('ratio-switch').addEventListener('change', function() {
            isDetailRatio = this.checked;
            redrawMaps();
        });

        // ========= FUNGSI GET METRIC INFO (versi efisien) =========
        function getMetricInfo(metric, value = null, counters = null) {
            const sharedColors = [
                '#0652DD', '#1289A7', '#009432', '#A3CB38', '#C4E538',
                '#FFC312', '#F79F1F', '#EE5A24', '#c23616', '#A00000'
            ];

            const compactColors = [
                '#0652DD', '#009432', '#FFC312', '#EE5A24', '#A00000'
            ];

            const metricConfigs = {
                rsrp: {
                    labels: !isDetailRatio ? ['≥ -85', '-95 s/d -85', '-100 s/d -95', '-105 s/d -100', '≤ -105'] : [
                        '≥ -80', '-85 s/d -80', '-90 s/d -85', '-95 s/d -90', '-100 s/d -95', '-105 s/d -100',
                        '-110 s/d -105', '-115 s/d -110', '-120 s/d -115', '≤ -120'
                    ],
                    mins: !isDetailRatio ? [-85, -95, -100, -105, -9999] : [-80, -85, -90, -95, -100, -105, -110, -115,
                        -120, -9999
                    ]
                },
                rssi: {
                    labels: !isDetailRatio ? ['≥ -65', '-75 s/d -65', '-85 s/d -75', '-95 s/d -85', '≤ -95'] : [
                        "≥ -60", "-65 s/d -60", "-70 s/d -65", "-75 s/d -70", "-80 s/d -75", "-85 s/d -80",
                        "-90 s/d -85", "-95 s/d -90", "-100 s/d -95", "≤ -100"
                    ],
                    mins: !isDetailRatio ? [-65, -75, -85, -95, -9999] : [-60, -65, -70, -75, -80, -85, -90, -95, -100,
                        -9999
                    ]
                },
                rsrq: {
                    labels: !isDetailRatio ? ['≥ -10', '-14 s/d -10', '-16 s/d -14', '-20 s/d -16', '≤ -20'] : ['≥ -3',
                        '-5 s/d -3', '-7 s/d -5', '-9 s/d -7', '-11 s/d -9', '-13 s/d -11', '-15 s/d -13',
                        '-17 s/d -15', '-19 s/d -17', '≤ -19'
                    ],
                    mins: !isDetailRatio ? [-10, -14, -16, -20, -9999] : [-3, -5, -7, -9, -11, -13, -15, -17, -19, -
                        9999
                    ]
                },
                sinr: {
                    labels: !isDetailRatio ? ['≥ 20', '10 s/d 20', '0 s/d 10', '-5 s/d 0', '≤ -5'] : ['≥ 20',
                        '15 s/d 20', '10 s/d 15', '5 s/d 10', '0 s/d 5', '-5 s/d 0', '-10 s/d -5',
                        '-15 s/d -10', '-20 s/d -15', '≤ -20'
                    ],
                    mins: !isDetailRatio ? [20, 10, 0, -5, -9999] : [20, 15, 10, 5, 0, -5, -10, -15, -20, -9999]
                }
            };

            const config = metricConfigs[metric.toLowerCase()];
            if (!config) return {
                color: '#C0C0C0',
                labels: [],
                colors: []
            };

            const colors = !isDetailRatio ? compactColors : sharedColors;
            const ranges = config.mins.map((min, i) => ({
                min,
                color: colors[i] || '#A00000'
            }));

            if (value === null) {
                return {
                    labels: config.labels,
                    colors
                };
            }

            for (let i = 0; i < ranges.length; i++) {
                if (value >= ranges[i].min) {
                    if (counters) counters[i]++;
                    return {
                        color: ranges[i].color,
                        labels: config.labels,
                        colors
                    };
                }
            }

            return {
                color: '#C0C0C0',
                labels: config.labels,
                colors
            };
        }


        function getColorBySERV(frequency) {
            // if (frequency == 2300 || frequency == 2400) return '#0652DD';
            if (frequency == 2300) return '#0652DD';
            if (frequency == 2100) return '#009432';
            if (frequency == 1800) return '#FFC312';
            if (frequency == 900) return '#c23616';
            return '#C0C0C0';
        }

        function getColorByBW(bandwidth) {
            if (bandwidth == 20) return '#0652DD';
            if (bandwidth == 15) return '#009432';
            if (bandwidth == 10) return '#C4E538';
            if (bandwidth == 5) return '#F79F1F';
            if (bandwidth == 3) return '#c23616';
            if (bandwidth == 1.4) return '#A00000';
            return '#C0C0C0';
        }

        function getColorByCell(cell) {
            if (!cell) return '#C0C0C0';

            // Ambil sharedColors dari getMetricInfo (pakai warna dasar)
            // const sharedColors = [
            //     '#0652DD',
            //     '#f7b731',
            //     '#009432',
            //     '#e84393',
            //     '#273c75',
            //     '#EE5A24',
            //     '#00b894',
            //     '#c44569',
            //     '#4cd137',
            //     '#6F1E51',
            //     '#fa8231',
            //     '#192a56',
            //     '#20bf6b',
            //     '#f5cd79',
            //     '#e84118',
            //     '#786fa6',
            //     '#0097e6',
            //     '#eb3b5a',
            //     '#546de5',
            //     '#d63031',
            //     '#2f3640',
            //     '#e15f41',
            //     '#dcdde1',
            //     '#5758BB',
            //     '#00bcd4'
            // ];
            // const sharedColors = [
            //     '#EA2027', // 🔴 merah kuat
            //     '#FFC312', // 🟡 kuning cerah
            //     '#4cd137', // 🟢 hijau lime
            //     '#0097e6', // 🔵 biru terang
            //     '#EE5A24', // 🟠 oranye gelap
            //     '#a55eea', // 💜 violet cerah
            //     '#3ae374', // 🟢 hijau neon
            //     '#ffb8b8', // 🌸 pink lembut
            //     '#192a56', // 🔵 navy
            // ];

            const sharedColors = [
                '#FA2027', // merah
                '#0652DD', // biru
                '#ADFF2F', // hijau kekuningan terang
                '#e84393', // magenta
                '#FFC312', // kuning
                '#182C61', // teal gelap
                '#F97F51', // oranye
                '#9980FA', // ungu
                '#009432', // hijau gelap
                '#00cec9', // cyan
                '#fdcb6e', // emas
                '#b33939', // pink terang
                '#dcdde1',
                '#30D984',
                '#FE5A24',
                '#ffb8b8',
                '#A15f21',
            ];

            // const sharedColors = [
            //     '#FF0000', // merah
            //     '#00FF00', // hijau
            //     '#0000FF', // biru
            //     '#FFFF00', // kuning (merah+hijau)
            //     '#FF00FF', // magenta (merah+biru)
            //     '#00FFFF', // cyan (hijau+biru)
            //     '#FFA500', // oranye (merah+kuning)
            //     '#ADFF2F', // hijau kekuningan (terang)
            //     '#800080', // ungu (merah+biru gelap)
            //     '#008080', // teal (hijau+biru gelap)
            //     '#FFD700', // emas (kuning terang)
            //     '#FF69B4'  // pink terang (magenta terang)
            // ];






            // Simpan cache warna agar tiap cell selalu konsisten warnanya
            if (!window.cellColorCache) {
                window.cellColorCache = new Map();
            }

            // Kalau sudah ada di cache, langsung pakai
            if (window.cellColorCache.has(cell)) {
                return window.cellColorCache.get(cell);
            }

            const existingCount = window.cellColorCache.size;

            // Jika masih dalam jangkauan sharedColors → gunakan warna dari daftar
            if (existingCount < sharedColors.length) {
                const color = sharedColors[existingCount];
                window.cellColorCache.set(cell, color);
                return color;
            }

            // Jika lebih banyak daripada sharedColors, generate warna baru dari hash
            const str = cell.toString();
            let hash = 2166136261;
            for (let i = 0; i < str.length; i++) {
                hash ^= str.charCodeAt(i);
                hash = Math.imul(hash, 16777619);
            }
            hash = (hash ^ (hash >>> 16)) >>> 0;

            const hue = hash % 360;
            const saturation = 55 + ((hash >> 8) % 25); // jaga kontras
            const lightness = 50 + ((hash >> 16) % 20);

            const color = `hsl(${hue}, ${saturation}%, ${lightness}%)`;
            window.cellColorCache.set(cell, color);
            return color;
        }



        // ========== Legend Dinamis ==========
        function getColorLegend(metric, mapId) {
            var legend = L.control({
                position: 'bottomright'
            });

            legend.onAdd = function(map) {
                const div = L.DomUtil.create('div', 'info p-2 text-sm bg-white bg-opacity-40 shadow-md rounded-md');
                const {
                    labels,
                    colors
                } = getMetricInfo(metric);
                const entry = mapRegistry[mapId];
                const hasData = entry && entry.data && entry.data.length > 0;

                function appendColorStat(div, labels, colors, data, calcFunc) {
                    const total = data.length;
                    const counts = Array(labels.length).fill(0);
                    data.forEach(v => calcFunc(v, counts));

                    div.innerHTML += `<div class="text-xs my-1">Total data: ${total}</div>`;
                    labels.forEach((label, i) => {
                        const count = counts[i];
                        const percent = total > 0 ? ((count / total) * 100).toFixed(1) : 0;
                        div.innerHTML += `
                <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-1">
                        <i style="background:${colors[i]};
                            width:12px;height:12px;
                            display:inline-block;
                            margin-right:4px;
                            border-radius:2px;
                            transform:translateY(-1px)"></i>
                        <span style="font-size:10px;">${label}</span>
                    </div>
                    <span class="ml-2" style="font-size:10px;">${count} (${percent}%)</span>
                </div>`;
                    });
                }

                // === RSRP ===
                if (metric === "rsrp") {
                    div.innerHTML += '<b>RSRP (dBm)</b><br>';
                    if (hasData) {
                        const data = entry.data.map(d => d.rsrp).filter(v => v !== undefined && v !== null);
                        appendColorStat(div, labels, colors, data, (v, c) => getMetricInfo('rsrp', v, c));
                    } else {
                        div.innerHTML += '<span class="text-gray-500 text-xs">Tidak ada data.</span>';
                    }

                    // === RSSI ===
                } else if (metric === "rssi") {
                    div.innerHTML += '<b>RSSI (dBm)</b><br>';
                    if (hasData) {
                        const data = entry.data.map(d => d.rssi).filter(v => v !== undefined && v !== null);
                        appendColorStat(div, labels, colors, data, (v, c) => getMetricInfo('rssi', v, c));
                    } else {
                        div.innerHTML += '<span class="text-gray-500 text-xs">Tidak ada data.</span>';
                    }

                    // === RSRQ ===
                } else if (metric === "rsrq") {
                    div.innerHTML += '<b>RSRQ (dB)</b><br>';
                    if (hasData) {
                        const data = entry.data.map(d => d.rsrq).filter(v => v !== undefined && v !== null);
                        appendColorStat(div, labels, colors, data, (v, c) => getMetricInfo('rsrq', v, c));
                    } else {
                        div.innerHTML += '<span class="text-gray-500 text-xs">Tidak ada data.</span>';
                    }

                    // === SINR ===
                } else if (metric === "sinr") {
                    div.innerHTML += '<b>SINR (dB)</b><br>';
                    if (hasData) {
                        const data = entry.data.map(d => d.sinr).filter(v => v !== undefined && v !== null);
                        appendColorStat(div, labels, colors, data, (v, c) => getMetricInfo('sinr', v, c));
                    } else {
                        div.innerHTML += '<span class="text-gray-500 text-xs">Tidak ada data.</span>';
                    }

                    // === SERVING SYSTEM (Frequency) === 🆕
                } else if (metric === "serv") {
                    div.innerHTML += '<b>Serving System</b><br>';

                    let allBands = [];
                    if (hasData) {
                        entry.data.forEach(d => {
                            if (d.frekuensi !== undefined && d.frekuensi !== null) {
                                allBands.push(String(d.frekuensi));
                            }
                        });
                    }

                    const total = allBands.length;
                    const bandLabelMap = {
                        '2300': 'L2300 Band 40',
                        '2100': 'L2100 Band 1',
                        '1800': 'L1800 Band 3',
                        '900': 'L900 Band 8',
                    };
                    const bandColorMap = {
                        '2300': '#0652DD',
                        '2100': '#009432',
                        '1800': '#FFC312',
                        '900': '#c23616',
                    };

                    const uniqueBands = [...new Set(allBands)];
                    div.innerHTML += `<div class="text-xs my-1">Total data: ${total}</div>`;

                    uniqueBands.forEach(band => {
                        const count = allBands.filter(b => b === band).length;
                        const percent = total > 0 ? ((count / total) * 100).toFixed(1) : 0;
                        const label = bandLabelMap[band] || `L${band}`;
                        const color = bandColorMap[band] || '#000';
                        div.innerHTML += `
                <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-1">
                        <i style="background:${color};
                            width:12px;height:12px;
                            display:inline-block;
                            margin-right:4px;
                            border-radius:2px;
                            transform:translateY(-1px)"></i>
                        <span style="font-size:10px;">${label}</span>
                    </div>
                    <span class="ml-2" style="font-size:10px;">(${percent}%)</span>
                </div>`;
                    });

                    if (uniqueBands.length === 0)
                        div.innerHTML += `<span style="color:gray;font-size:10px;">Tidak ada data frequency</span><br>`;
                    return div;

                    // === BANDWIDTH === 🆕
                } else if (metric === "bw") {
                    div.innerHTML += '<b>Bandwidth</b><br>';
                    let allBandwidths = [];

                    if (hasData) {
                        entry.data.forEach(d => {
                            if (d.bandwidth !== undefined && d.bandwidth !== null)
                                allBandwidths.push(String(d.bandwidth).trim());
                        });
                    }

                    const total = allBandwidths.length;
                    const bwColorMap = {
                        '20': '#0652DD',
                        '15': '#009432',
                        '10': '#C4E538',
                        '5': '#F79F1F',
                        '3': '#c23616',
                        '1.4': '#A00000',
                    };
                    const bwOrder = ['20', '15', '10', '5', '3', '1.4'];
                    const uniqueBandwidths = [...new Set(allBandwidths)];
                    const sortedBandwidths = bwOrder.filter(bw => uniqueBandwidths.includes(bw));

                    div.innerHTML += `<div class="text-xs my-1">Total data: ${total}</div>`;
                    sortedBandwidths.forEach(bw => {
                        const count = allBandwidths.filter(v => v === bw).length;
                        const percent = total > 0 ? ((count / total) * 100).toFixed(1) : 0;
                        const color = bwColorMap[bw] || '#000';
                        div.innerHTML += `
                <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-1">
                        <i style="background:${color};
                            width:12px;height:12px;
                            display:inline-block;
                            margin-right:4px;
                            border-radius:2px;
                            transform:translateY(-1px)"></i>
                        <span style="font-size:10px;">${bw} MHz</span>
                    </div>
                    <span class="ml-2" style="font-size:10px;">(${percent}%)</span>
                </div>`;
                    });

                    if (sortedBandwidths.length === 0)
                        div.innerHTML += `<span style="color:gray;font-size:10px;">Tidak ada data bandwidth</span><br>`;
                    return div;

                    // === CELL ID === 🆕
                } else if (metric === "cell") {
                    div.innerHTML += '<b>Cell ID</b><br>';
                    let allCells = [];

                    if (hasData) {
                        entry.data.forEach(d => {
                            if (d.cell_id !== undefined && d.cell_id !== null)
                                allCells.push(String(d.cell_id));
                        });
                    }

                    const total = allCells.length;
                    const uniqueCells = [...new Set(allCells)];

                    div.innerHTML += `<div class="text-xs my-1">Total data: ${total}</div>`;
                    uniqueCells.forEach(cell => {
                        const count = allCells.filter(v => v === cell).length;
                        const percent = total > 0 ? ((count / total) * 100).toFixed(1) : 0;
                        const color = getColorByCell(cell);
                        div.innerHTML += `
                <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-1">
                        <i style="background:${color};
                            width:12px;height:12px;
                            display:inline-block;
                            margin-right:4px;
                            border-radius:2px;
                            transform:translateY(-1px)"></i>
                        <span style="font-size:10px;">${cell}</span>
                    </div>
                    <span  class="ml-2" style="font-size:10px;">(${percent}%)</span>
                </div>`;
                    });

                    if (uniqueCells.length === 0)
                        div.innerHTML += `<span style="color:gray;font-size:10px;">Tidak ada data Cell ID</span><br>`;
                    return div;
                }

                return div;
            };

            return legend;
        }


        function updateMetricButtonStyles() {
            const buttons = document.querySelectorAll('.metric-btn');
            buttons.forEach(button => {
                if (button.getAttribute('data-value') === selectedMetric) {
                    button.classList.add('metric-btn-active');
                } else {
                    button.classList.remove('metric-btn-active');
                }
            });
            var metricKetElement = document.getElementById('metric-keterangan');
            var colorMetric
            if (metricKetElement) {
                if (selectedMetric === 'rsrp') {
                    colorMetric = 'text-green-600';
                } else if (selectedMetric === 'rssi') {
                    colorMetric = 'text-blue-600';
                } else if (selectedMetric === 'rsrq') {
                    colorMetric = 'text-orange-600';
                } else if (selectedMetric === 'sinr') {
                    colorMetric = 'text-red-600';
                } else if (selectedMetric === 'serv') {
                    colorMetric = 'text-gray-600';
                } else if (selectedMetric === 'bw') {
                    colorMetric = 'text-gray-800';
                } else if (selectedMetric === 'cell') {
                    colorMetric = 'text-yellow-400';
                }

                let selecName
                if (selectedMetric == 'rsrp') {
                    selecName = 'Reference Signal Received Power';
                } else if (selectedMetric == 'rssi') {
                    selecName = 'Received Signal Strength Indicator';
                } else if (selectedMetric == 'rsrq') {
                    selecName = 'Reference Signal Received Quality';
                } else if (selectedMetric == 'sinr') {
                    selecName = 'Signal to Interference plus Noise Ratio';
                } else if (selectedMetric == 'serv') {
                    selecName = 'Serving System & Band';
                } else if (selectedMetric == 'bw') {
                    selecName = 'Bandwidth';
                } else if (selectedMetric == 'cell') {
                    selecName = 'Cell Identifier';
                }

                metricKetElement.innerHTML = `<b class="${colorMetric}">${selecName}</b>`;
            } else {
                console.error("Elemen dengan ID 'metric-keterangan' tidak ditemukan.");
            }
        }
    </script>
@endpush
