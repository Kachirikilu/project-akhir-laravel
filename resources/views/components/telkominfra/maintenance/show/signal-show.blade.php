@php
    /**
     * Helper function untuk menghitung dan memformat selisih sinyal (RSRP, RSRQ, SINR).
     * Peningkatan (nilai yang lebih tinggi, atau kurang negatif) dianggap sebagai perbaikan.
     * @param float|null $beforeValue Nilai rata-rata sebelum.
     * @param float|null $afterValue Nilai rata-rata sesudah.
     * @param string $unit Satuan (dBm atau dB).
     * @return string Output HTML yang diformat.
     */
    $formatSignalDiff = function ($beforeValue, $afterValue, $unit) {
        if ($beforeValue === null || $afterValue === null) {
            return ($afterValue ?? 'N/A') . $unit . ' <span class="text-gray-500">(N/A)</span>';
        }

        $diff = $afterValue - $beforeValue;
        $diff_abs_formatted = number_format(abs($diff), 2); 

        $arrow = '';
        $sign = '';
        $color = 'text-gray-500';
        $diff_display = 'No Change';

        if ($diff > 0) {
            $color = 'text-emerald-600';
            $arrow = '&uarr;'; 
            $sign = '+';
            $diff_display = "{$sign}{$diff_abs_formatted}{$unit} {$arrow}";
        } elseif ($diff < 0) {
            $color = 'text-red-600';
            $arrow = '&darr;';
            $sign = '-';
            $diff_display = "{$sign}{$diff_abs_formatted}{$unit} {$arrow}";
        }

        return "{$afterValue}{$unit} <span class='{$color} text-xs font-semibold'>({$diff_display})</span>";
    };

    $rsrpBefore = $signalAverages['Before']['rsrp_avg'] ?? null;
    $rsrpAfter = $signalAverages['After']['rsrp_avg'] ?? null;

    $rssiBefore = $signalAverages['Before']['rssi_avg'] ?? null;
    $rssiAfter = $signalAverages['After']['rssi_avg'] ?? null;

    $rsrqBefore = $signalAverages['Before']['rsrq_avg'] ?? null;
    $rsrqAfter = $signalAverages['After']['rsrq_avg'] ?? null;

    $sinrBefore = $signalAverages['Before']['sinr_avg'] ?? null;
    $sinrAfter = $signalAverages['After']['sinr_avg'] ?? null;

@endphp

<div class="bg-white shadow overflow-hidden rounded-lg mb-6">
    <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">Rata-rata Sinyal</h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">Rata-rata sinyal sebelum dan sesudah pemeliharaan.</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-3 gap-4 border-t border-gray-200">
        <div>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Status</dt>
                <dd class="font-bold text-orange-600 border-orange-600 mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">Before Maintenance</dd>
            </div>
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Rata-rata RSRP</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $rsrpBefore ?? 'N/A' }} dBm</dd>
            </div>
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Rata-rata RSSI</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $rssiBefore ?? 'N/A' }} dBm</dd>
            </div>
            <div class="bg-white-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Rata-rata RSRQ</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $rsrqBefore ?? 'N/A' }} dB</dd>
            </div>
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Rata-rata SINR</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $sinrBefore ?? 'N/A' }} dB</dd>
            </div>
        </div>
        
        <div>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Status</dt>
                <dd class="font-bold text-green-600 border-green-600 mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">After Maintenance</dd>
            </div>
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Rata-rata RSRP</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    {!! $formatSignalDiff($rsrpBefore, $rsrpAfter, ' dBm') !!}
                </dd>
            </div>
            <div class="bg-white-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Rata-rata RSSI</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    {!! $formatSignalDiff($rssiBefore, $rssiAfter, ' dBm') !!}
                </dd>
            </div>
            <div class="bg-white-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Rata-rata RSRQ</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    {!! $formatSignalDiff($rsrqBefore, $rsrqAfter, ' dB') !!}
                </dd>
            </div>
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Rata-rata SINR</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    {!! $formatSignalDiff($sinrBefore, $sinrAfter, ' dB') !!}
                </dd>
            </div>
        </div>
    </div>
</div>
