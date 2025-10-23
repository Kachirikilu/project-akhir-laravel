@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
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

    <div id="metric-keterangan" class="mb-3 ml-2"></div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="grid grid-cols-1 gap-4">
            @foreach (collect($mapsData)->where('status', 'Before') as $mapItem)
                @if (!is_int($mapItem['id']))
                    <x-telkominfra.map.item-map :mapItem="$mapItem" />
                @endif
            @endforeach
        </div>
        <div class="grid grid-cols-1 gap-4">
            @foreach (collect($mapsData)->where('status', 'After') as $mapItem)
                @if (!is_int($mapItem['id']))
                    <x-telkominfra.map.item-map :mapItem="$mapItem" />
                @endif
            @endforeach
        </div>
    </div>

    <div class="relative flex items-center my-6">
        <div class="flex-grow border-t border-gray-300"></div>
        <span class="mx-4 text-gray-500 text-sm font-medium">Data Log Terpisah</span>
        <div class="flex-grow border-t border-gray-300"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="grid grid-cols-1 gap-4">
            @foreach (collect($mapsData)->where('status', 'Before') as $mapItem)
                @if (is_int($mapItem['id']))
                    <x-telkominfra.map.item-map :mapItem="$mapItem" />
                @endif
            @endforeach
        </div>
        <div class="grid grid-cols-1 gap-4">
            @foreach (collect($mapsData)->where('status', 'After') as $mapItem)
                @if (is_int($mapItem['id']))
                    <x-telkominfra.map.item-map :mapItem="$mapItem" />
                @endif
            @endforeach
        </div>
    </div>
</div>


@push('scripts')
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

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
                if (selectedMetric === "rsrp") color = getMetricInfo('rsrp', rsrp);
                if (selectedMetric === "rssi") color = getMetricInfo('rssi', rssi);
                if (selectedMetric === "rsrq") color = getMetricInfo('rsrq', rsrq);
                if (selectedMetric === "sinr") color = getMetricInfo('sinr', sinr);
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
                    color: '#000000',
                    weight: 0,
                    opacity: 1,
                    fillOpacity: 0.8
                });

                let freValue = frequency;
                if (freValue == 2300 || freValue == 2400) {
                    freValue = "2300-2400";
                }

                seg.bindPopup(
                    "Segmen Drive Test<br>" +
                    "Cell ID: <b>" + cell + "</b><br>" +
                    "RSRP: <b>" + rsrp.toFixed(1) + " dBm</b><br>" +
                    "RSSI: <b>" + rssi.toFixed(1) + " dBm</b><br>" +
                    "RSRQ: <b>" + rsrq.toFixed(1) + " dB</b><br>" +
                    "SINR: <b>" + sinr.toFixed(1) + " dB</b><br>" +
                    "PCI: <b>" + pci + "</b><br>" +
                    "Earfcn: <b>" + earfcn + "</b><br>" +
                    "Band: <b>" + band + "</b><br>" +
                    "Frequency: <b>" + freValue + " MHz</b><br>" +
                    "Bandwidth: <b>" + bandwidth + " MHz</b><br>" +
                    "N-Value: <b>" + n_value + "</b><br>" +
                    "Waktu: <b>" + waktu + "</b><br>" +
                    "Koordinat: " + latitude.toFixed(6) + ", " + longitude.toFixed(6)
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
        function getMetricInfo(metric, value, counters = null) {
            // Definisi range dan warna per metric
            const configs = {
                rsrp: [
                    { min: -80, color: '#0652DD' },
                    { min: -85, color: '#1289A7' },
                    { min: -90, color: '#009432' },
                    { min: -95, color: '#A3CB38' },
                    { min: -100, color: '#C4E538' },
                    { min: -105, color: '#FFC312' },
                    { min: -110, color: '#F79F1F' },
                    { min: -115, color: '#EE5A24' },
                    { min: -120, color: '#c23616' },
                    { min: -999, color: '#000000' }
                ],
                rssi: [
                    { min: -52, color: '#0652DD' },
                    { min: -58, color: '#1289A7' },
                    { min: -64, color: '#009432' },
                    { min: -70, color: '#A3CB38' },
                    { min: -76, color: '#C4E538' },
                    { min: -82, color: '#FFC312' },
                    { min: -88, color: '#F79F1F' },
                    { min: -94, color: '#EE5A24' },
                    { min: -100, color: '#c23616' },
                    { min: -999, color: '#000000' }
                ],
                rsrq: [
                    { min: -3, color: '#0652DD' },
                    { min: -5, color: '#1289A7' },
                    { min: -7, color: '#009432' },
                    { min: -9, color: '#A3CB38' },
                    { min: -11, color: '#C4E538' },
                    { min: -13, color: '#FFC312' },
                    { min: -15, color: '#F79F1F' },
                    { min: -17, color: '#EE5A24' },
                    { min: -19, color: '#c23616' },
                    { min: -999, color: '#000000' }
                ],
                sinr: [
                    { min: 20, color: '#0652DD' },
                    { min: 15, color: '#1289A7' },
                    { min: 10, color: '#009432' },
                    { min: 5, color: '#A3CB38' },
                    { min: 0, color: '#C4E538' },
                    { min: -5, color: '#FFC312' },
                    { min: -10, color: '#F79F1F' },
                    { min: -15, color: '#EE5A24' },
                    { min: -20, color: '#c23616' },
                    { min: -999, color: '#000000' }
                ]
            };

            const ranges = configs[metric.toLowerCase()];
            if (!ranges) return '#C0C0C0';

            for (let i = 0; i < ranges.length; i++) {
                if (value >= ranges[i].min) {
                    if (counters) counters[i]++;
                    return ranges[i].color;
                }
            }
            return '#C0C0C0';
        }


        
        function getColorBySERV(frequency) {
            if (frequency == 2300 || frequency == 2400) return '#0652DD';
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
            if (bandwidth == 1.4) return '#000000';
            return '#C0C0C0';
        }

        function getColorByCell(cell) {
            if (!cell) return '#C0C0C0';

            const str = cell.toString();
            let hash = 2166136261;
            for (let i = 0; i < str.length; i++) {
                hash ^= str.charCodeAt(i);
                hash = Math.imul(hash, 1677761992901391973989);
            }

            hash = (hash ^ (hash >>> 16)) >>> 0;

            const hue = hash % 360;
            const saturation = 60 + ((hash >> 8) % 30);
            const lightness = 60 + ((hash >> 16) % 20);

            return `hsl(${hue}, ${saturation}%, ${lightness}%)`;
        }

        // function getColorByRSRP(rsrp) {
        //     if (rsrp >= -85) return '#1B1464';
        //     if (rsrp >= -95) return '#A3CB38';
        //     if (rsrp >= -100) return '#4cd137';
        //     if (rsrp >= -105) return '#fbc531';
        //     if (rsrp < -105) return '#EA2027';
        //     return '#C0C0C0';
        // }
        // function getColorByRSSI(rssi) {
        //     if (rssi >= -85) return '#1B1464';
        //     if (rssi >= -95) return '#A3CB38';
        //     if (rssi >= -100) return '#4cd137';
        //     if (rssi >= -105) return '#fbc531';
        //     if (rssi < -105) return '#EA2027';
        //     return '#C0C0C0';
        // }
        // function getColorByRSRQ(rsrq) {
        //     if (rsrq >= -10) return '#1B1464';
        //     if (rsrq >= -12) return '#A3CB38';
        //     if (rsrq >= -16) return '#4cd137';
        //     if (rsrq >= -20) return '#fbc531';
        //     if (rsrq < -20) return '#EA2027';
        //     return '#C0C0C0';
        // }
        // function getColorBySINR(sinr) {
        //     if (sinr >= 20) return '#1B1464';
        //     if (sinr >= 10) return '#A3CB38';
        //     if (sinr >= 0) return '#4cd137';
        //     if (sinr >= -5) return '#fbc531';
        //     if (sinr < -5) return '#EA2027';
        //     return '#C0C0C0';
        // }

        // ========== Legend Dinamis ==========
        function getColorLegend(metric, mapId) {
            var legend = L.control({
                position: 'bottomright'
            });
            legend.onAdd = function(map) {
                var div = L.DomUtil.create('div', 'info p-2 text-sm bg-white bg-opacity-90 shadow-md rounded-md');

                let colors = [],
                    labels = [];

                if (metric == "serv") {
                    colors = ['#0652DD', '#009432', '#FFC312', '#c23616'];
                } else if (metric == "bw") {
                    colors = ['#0652DD', '#009432', '#C4E538', '#F79F1F', '#c23616', '#000000'];
                } else if (metric !== "cell") {
                    colors = [
                        '#0652DD', '#1289A7', '#009432', '#A3CB38', '#C4E538',
                        '#FFC312', '#F79F1F', '#EE5A24', '#c23616', '#000000'
                    ];
                }

                if (metric == "rsrp") {
                    labels = ['≥ -80', '-85 s/d -80', '-90 s/d -85', '-95 s/d -90', '-100 s/d -95', '-105 s/d -100',
                        '-110 s/d -105', '-115 s/d -110', '-120 s/d -115', '≤ -120'
                    ];
                } else if (metric == "rssi") {

                    labels = ['≥ -52', '-58 s/d -52', '-64 s/d -58', '-70 s/d -64', '-76 s/d -70', '-82 s/d -76',
                        '-88 s/d -82', '-94 s/d -88', '-100 s/d -94', '≤ -100'
                    ];
                } else if (metric == "rsrq") {
                    labels = ['≥ -3', '-5 s/d -3', '-7 s/d -5', '-9 s/d -7', '-11 s/d -9', '-13 s/d -11', '-15 s/d -13',
                        '-17 s/d -15', '-19 s/d -17', '≤ -19'
                    ];
                } else if (metric == "sinr") {
                    labels = ['≥ 20', '15 s/d 20', '10 s/d 15', '5 s/d 10', '0 s/d 5', '-5 s/d 0', '-10 s/d -5',
                        '-15 s/d -10', '-20 s/d -15', '≤ -20'
                    ];
                }

                // Fungsi bantu untuk menampilkan label + warna + jumlah + persentase
                function appendColorStat(div, labels, colors, data, calcFunc) {
                    const total = data.length;
                    const counts = Array(labels.length).fill(0);
                    data.forEach(v => calcFunc(v, counts));

                    div.innerHTML += `<div class="text-xs mt-1">Total data: ${total}</div>`;
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
                                border-radius:2px;"></i>
                            <span>${label}</span>
                        </div>
                        <span class="ml-2"> (${percent}%)</span>
                    </div>`;
                    });
                }

                const entry = mapRegistry[mapId];
                const hasData = entry && entry.data && entry.data.length > 0;

                // === RSRP ===
                if (metric === "rsrp") {
                    div.innerHTML += '<b>RSRP (dBm)</b><br>';

                    if (hasData) {
                        const data = entry.data.map(d => d.rsrp).filter(v => v !== undefined && v !== null);
                        appendColorStat(div, labels, colors, data, (v, c) => getMetricInfo('rsrp', v, c));
                    } else div.innerHTML += '<span class="text-gray-500 text-xs">Tidak ada data.</span>';

                    // === RSSI ===
                } else if (metric === "rssi") {
                    div.innerHTML += '<b>RSSI (dBm)</b><br>';

                    if (hasData) {
                        const data = entry.data.map(d => d.rssi).filter(v => v !== undefined && v !== null);
                        appendColorStat(div, labels, colors, data, (v, c) => getMetricInfo('rssi', v, c));
                    } else div.innerHTML += '<span class="text-gray-500 text-xs">Tidak ada data.</span>';

                    // === RSRQ ===
                } else if (metric === "rsrq") {
                    div.innerHTML += '<b>RSRQ (dB)</b><br>';

                    if (hasData) {
                        const data = entry.data.map(d => d.rsrq).filter(v => v !== undefined && v !== null);
                        appendColorStat(div, labels, colors, data, (v, c) => getMetricInfo('rsrq', v, c));
                    } else div.innerHTML += '<span class="text-gray-500 text-xs">Tidak ada data.</span>';

                    // === SINR ===
                } else if (metric === "sinr") {
                    div.innerHTML += '<b>SINR (dB)</b><br>';

                    if (hasData) {
                        const data = entry.data.map(d => d.sinr).filter(v => v !== undefined && v !== null);
                        appendColorStat(div, labels, colors, data, (v, c) => getMetricInfo('sinr', v, c));
                    } else div.innerHTML += '<span class="text-gray-500 text-xs">Tidak ada data.</span>';
                } else if (metric === "serv") {
                    div.innerHTML += '<b>Serving System</b><br>';

                    const entry = mapRegistry[mapId];
                    let allBands = [];

                    if (entry && entry.data && entry.data.length > 0) {
                        entry.data.forEach(d => {
                            if (d.frekuensi !== undefined && d.frekuensi !== null) {
                                allBands.push(String(d.frekuensi)); // pastikan string
                            }
                        });
                    }

                    const uniqueBands = [...new Set(allBands)];

                    // Map range frekuensi ke label lengkap
                    const bandLabelMap = {
                        '2300': 'L2300-2400 Band 40',
                        '2400': 'L2300-2400 Band 40',
                        '2100': 'L2100 Band 1',
                        '1800': 'L1800 Band 3',
                        '900': 'L900 Band 8',
                    };

                    const bandColorMap = {
                        '2300': '#0652DD',
                        '2400': '#0652DD',
                        '2100': '#009432',
                        '1800': '#FFC312',
                        '900': '#c23616',
                    };
                    uniqueBands.forEach(band => {
                        const label = bandLabelMap[band] || `L${band}`;
                        const color = bandColorMap[band] || '#000000';
                        div.innerHTML += `
            <i style="background:${color};
                width:12px;height:12px;
                float:left;margin-right:6px;
                transform:translateY(4px);"></i>
            <span style="font-size:11px;">${label}</span><br>`;
                    });
                    if (uniqueBands.length === 0) {
                        div.innerHTML += `<span style="color:gray;font-size:11px;">Tidak ada data frequency</span><br>`;
                    }

                    return div;
                } else if (metric === "bw") {
                    div.innerHTML += '<b>Bandwidth</b><br>';

                    const entry = mapRegistry[mapId];
                    let allBandwidths = [];

                    if (entry && entry.data && entry.data.length > 0) {
                        entry.data.forEach(d => {
                            if (d.bandwidth !== undefined && d.bandwidth !== null) {
                                // pastikan disimpan dalam bentuk string agar cocok di bwOrder
                                allBandwidths.push(String(d.bandwidth).trim());
                            }
                        });
                    }

                    const uniqueBandwidths = [...new Set(allBandwidths)];

                    const bwColorMap = {
                        '20': '#0652DD',
                        '15': '#009432',
                        '10': '#C4E538',
                        '5': '#F79F1F',
                        '3': '#c23616',
                        '1.4': '#000000',
                    };

                    const bwOrder = ['20', '15', '10', '5', '3', '1.4'];
                    const sortedBandwidths = bwOrder.filter(bw => uniqueBandwidths.includes(bw));

                    // Hanya tampilkan bandwidth yang tersedia
                    sortedBandwidths.forEach(bw => {
                        const color = bwColorMap[bw] || '#000000';
                        div.innerHTML += `
            <i style="background:${color};
                width:12px;height:12px;
                float:left;margin-right:6px;
                transform:translateY(4px);"></i>
            <span style="font-size:11px;">${bw} MHz</span><br>`;
                    });

                    // Jika kosong, tampilkan teks abu-abu
                    if (sortedBandwidths.length === 0) {
                        div.innerHTML += `<span style="color:gray;font-size:11px;">Tidak ada data bandwidth</span><br>`;
                    }

                    return div;
                } else if (metric === "cell") {
                    div.innerHTML += '<b>Cell ID</b><br>';

                    const entry = mapRegistry[mapId];
                    let allCells = [];

                    if (entry && entry.data && entry.data.length > 0) {
                        entry.data.forEach(d => {
                            if (d.cell_id !== undefined && d.cell_id !== null) {
                                allCells.push(d.cell_id);
                            }
                        });
                    }

                    const uniqueCells = [...new Set(allCells)];
                    uniqueCells.forEach(cell => {
                        const color = getColorByCell(cell);
                        div.innerHTML += `
            <i style="background:${color};
                width:12px;height:12px;
                float:left;margin-right:6px;
                transform:translateY(4px);"></i>
            <span style="font-size:11px;">${cell}</span><br>`;
                    });

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
                    selecName = 'Cell ID';
                }

                metricKetElement.innerHTML = `Metrik Aktif: <b class="${colorMetric}">${selecName}</b>`;
            } else {
                console.error("Elemen dengan ID 'metric-keterangan' tidak ditemukan.");
            }
        }
    </script>
@endpush
