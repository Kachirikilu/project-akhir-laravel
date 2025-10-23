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

    <div class="mb-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-2">
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
                <x-telkominfra.map.item-map :mapItem="$mapItem" />
            @endforeach
        </div>
        <div class="grid grid-cols-1 gap-4">
            @foreach (collect($mapsData)->where('status', 'After') as $mapItem)
                <x-telkominfra.map.item-map :mapItem="$mapItem" />
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
                    .frekuensi, d.bandwidth, d.n_value, d.timestamp_waktu
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
                    waktu = end[12];

                let color;
                if (selectedMetric === "rsrp") color = getColorByRSRP(rsrp);
                if (selectedMetric === "rssi") color = getColorByRSSI(rssi);
                if (selectedMetric === "rsrq") color = getColorByRSRQ(rsrq);
                if (selectedMetric === "sinr") color = getColorBySINR(sinr);
                if (selectedMetric === "serv") color = getColorBySERV(frequency);
                if (selectedMetric === "bw") color = getColorByBW(bandwidth);

                // let seg = L.polyline([
                //     [start[0], start[1]],
                //     [end[0], end[1]]
                // ], {
                //     color: color,
                //     weight: 6,
                //     opacity: 0.8
                // });

                let seg = L.circleMarker([latitude, longitude], {
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
            entry.legend = getColorLegend(selectedMetric);
            entry.legend.addTo(map);
        }

        // ========== Warna Metric ==========
        function getColorByRSRP(rsrp) {
            if (rsrp >= -80) return '#0051ff';
            if (rsrp >= -85) return '#16bef7';
            if (rsrp >= -90) return '#00ffc8';
            if (rsrp >= -95) return '#2ECC71';
            if (rsrp >= -100) return '#F4D03E';
            if (rsrp >= -105) return '#FF8C00';
            if (rsrp >= -110) return '#FF4500';
            if (rsrp >= -115) return '#d82a17';
            if (rsrp > -120) return '#800000';
            if (rsrp <= -120) return '#000000';
            return '#C0C0C0';
        }

        function getColorByRSSI(rssi) {
            if (rssi >= -52) return '#0051ff';
            if (rssi >= -58) return '#16bef7';
            if (rssi >= -64) return '#00ffc8';
            if (rssi >= -70) return '#2ECC71';
            if (rssi >= -76) return '#F4D03E';
            if (rssi >= -82) return '#FF8C00';
            if (rssi >= -88) return '#FF4500';
            if (rssi >= -94) return '#d82a17';
            if (rssi > -100) return '#800000';
            if (rssi <= -100) return '#000000';
            return '#C0C0C0';
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
            if (rsrq > -19) return '#800000';
            if (rsrq <= -19) return '#000000';
            return '#C0C0C0';
        }

        function getColorBySINR(sinr) {
            if (sinr >= 20) return '#0051ff';
            if (sinr >= 15) return '#16bef7';
            if (sinr >= 10) return '#00ffc8';
            if (sinr >= 5) return '#2ECC71';
            if (sinr >= 0) return '#F4D03E';
            if (sinr >= -5) return '#FF8C00';
            if (sinr >= -10) return '#FF4500';
            if (sinr >= -15) return '#d82a17';
            if (sinr > -20) return '#800000';
            if (sinr <= -20) return '#000000';
            return '#C0C0C0';
        }

        function getColorBySERV(frequency) {
            if (frequency == 900) return '#0051ff';
            if (frequency == 1800) return '#00FFC8';
            if (frequency == 2100) return '#F4D03E';
            if (frequency == 2300 || frequency == 2400) return '#FF4500';
            return '#C0C0C0';
        }

        function getColorByBW(bandwidth) {
            if (bandwidth == 1.4) return '#0051ff';
            if (bandwidth == 3) return '#00ffc8';
            if (bandwidth == 5) return '#2ECC71';
            if (bandwidth == 10) return '#FF8C00';
            if (bandwidth == 15) return '#FF4500';
            if (bandwidth == 20) return '#800000';
            return '#C0C0C0';
        }

        // function getColorByRSRP(rsrp) {
        //     if (rsrp >= -85) return '#1B1464';
        //     if (rsrp >= -95) return '#009432';
        //     if (rsrp >= -100) return '#4cd137';
        //     if (rsrp >= -105) return '#fbc531';
        //     if (rsrp < -105) return '#EA2027';
        //     return '#C0C0C0';
        // }
        // function getColorByRSSI(rssi) {
        //     if (rssi >= -85) return '#1B1464';
        //     if (rssi >= -95) return '#009432';
        //     if (rssi >= -100) return '#4cd137';
        //     if (rssi >= -105) return '#fbc531';
        //     if (rssi < -105) return '#EA2027';
        //     return '#C0C0C0';
        // }
        // function getColorByRSRQ(rsrq) {
        //     if (rsrq >= -10) return '#1B1464';
        //     if (rsrq >= -12) return '#009432';
        //     if (rsrq >= -16) return '#4cd137';
        //     if (rsrq >= -20) return '#fbc531';
        //     if (rsrq < -20) return '#EA2027';
        //     return '#C0C0C0';
        // }
        // function getColorBySINR(sinr) {
        //     if (sinr >= 20) return '#1B1464';
        //     if (sinr >= 10) return '#009432';
        //     if (sinr >= 0) return '#4cd137';
        //     if (sinr >= -5) return '#fbc531';
        //     if (sinr < -5) return '#EA2027';
        //     return '#C0C0C0';
        // }

        // ========== Legend Dinamis ==========
        function getColorLegend(metric) {
            var legend = L.control({
                position: 'bottomright'
            });
            legend.onAdd = function(map) {
                var div = L.DomUtil.create('div', 'info p-2 text-sm bg-white bg-opacity-90 shadow-md rounded-md');

                let colors = [],
                    labels = [];

                if (metric == "serv") {
                    colors = [
                        '#0051FF',
                        '#00FFC8',
                        '#F4D03E',
                        '#FF4500',
                    ]
                } else if (metric == "bw") {
                    colors = [
                        '#0051FF',
                        '#00ffc8',
                        '#2ECC71',
                        '#FF8C00',
                        '#FF4500',
                        '#800000',
                    ]
                } else {
                    colors = [
                        '#0051FF',
                        '#16BEF7',
                        '#00FFC8',
                        '#2ECC71',
                        '#F4D03E',
                        '#FF8C00',
                        '#FF4500',
                        '#D82A17',
                        '#800000',
                        '#000000',
                    ]
                }

                if (metric === "rsrp") {
                    labels = [
                        '≥ -80',
                        '-85 s/d -80',
                        '-90 s/d -85',
                        '-95 s/d -90',
                        '-100 s/d -95',
                        '-105 s/d -100',
                        '-110 s/d -105',
                        '-115 s/d -110',
                        '-120 s/d -115',
                        '≥ -120'
                    ];
                    div.innerHTML += '<b>RSRP (dBm)</b><br>';
                } else if (metric === "rssi") {
                    labels = [
                        '≥ -72',
                        '-78 s/d -72',
                        '-84 s/d -78',
                        '-90 s/d -84',
                        '-96 s/d -90',
                        '-102 s/d -102',
                        '-108 s/d -96',
                        '-104 s/d -108',
                        '-120 s/d -104',
                        '≥ -120'
                    ];
                    div.innerHTML += '<b>RSSI (dBm)</b><br>';
                } else if (metric === "rsrq") {
                    labels = [
                        '≥ -3',
                        '-5 s/d -3',
                        '-7 s/d -5',
                        '-9 s/d -7',
                        '-11 s/d -9',
                        '-13 s/d -11',
                        '-15 s/d -13',
                        '-17 s/d -15',
                        '-19 s/d -17',
                        '≥ -19',
                    ];
                    div.innerHTML += '<b>RSRQ (dB)</b><br>';
                } else if (metric === "sinr") {
                    labels = [
                        '≥ 20',
                        '15 s/d 20',
                        '10 s/d 15',
                        '5 s/d 10',
                        '0 s/d 5',
                        '-5 s/d 0',
                        '-10 s/d -5',
                        '-15 s/d -10',
                        '-20 s/d -15',
                        '≥ -20',
                    ];
                    div.innerHTML += '<b>SINR (dB)</b><br>';
                } else if (metric === "serv") {
                    labels = [
                        'L900 Band 8',
                        'L1800 Band 3',
                        'L2100 Band 1',
                        'L2300-2400 Band 40',
                    ];
                    div.innerHTML += '<b>Serving System</b><br>';
                } else if (metric === "bw") {
                    labels = [
                        '1.4 MHz',
                        '3 MHz',
                        '5 MHz',
                        '10 MHz',
                        '15 MHz',
                        '20 MHz',
                    ];
                    div.innerHTML += '<b>Bandwidth</b><br>';
                }

                for (let i = 0; i < colors.length; i++) {
                    div.innerHTML +=
                        '<i style="background:' + colors[i] +
                        '; width: 12px; height: 12px; float:left; margin-right:6px; transform: translateY(4px);"></i> ' +
                        '<span style="font-size: 11px;">' + labels[i] + '</span><br>';
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
                }

                metricKetElement.innerHTML = `Metrik aktif: <b class="${colorMetric}">${selectedMetric.toUpperCase()}</b>`;
            } else {
                console.error("Elemen dengan ID 'metric-keterangan' tidak ditemukan.");
            }
        }
    </script>
@endpush
