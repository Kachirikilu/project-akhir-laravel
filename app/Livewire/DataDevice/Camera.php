<?php

namespace App\Livewire\DataDevice;

use Livewire\Component;
// use Illuminate\Support\Facades\Http;
use PhpMqtt\Client\Facades\MQTT;
use Illuminate\Support\Facades\Log;
use App\Jobs\MqttSubcriberJob;
use App\Models\IoTCamera;


class Camera extends Component
{
    public $id_device;
    public $message;
    public $image;
    
    public $motion;
    public $inputMessage;

    public $iotCamera = [];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        // MqttSubcriberJob::dispatch();
        $jd = cache()->get('esp32Cam');
        if ($jd) {
            $this->id_device = $jd['id_device'] ?? null;
            $this->message = $jd['message'] ?? null;
            $this->image = $jd['image'] ?? null;
        }

        $mt = cache()->get('esp32Cam_motion');
        if ($mt) {
            $this->motion = $mt;
        } else {
            $this->motion = null;
        }

        $this->iotCamera = IoTCamera::latest()->take(12)->get();

    }

    public function sendMessage()
    {
        try {
            $message = $this->inputMessage;

            $topic = env('MQTT_TOPIC_PUBS', 'iot/PlantCare');
            MQTT::connection()->publish($topic, $message);

            $this->inputMessage = '';
            session()->flash('success', 'Pesan berhasil dikirim');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengirim pesan: ' . $e->getMessage());
        }
    }

    public function sendCapture()
    {
        try {
            $message = 'Capture';

            $topic = env('MQTT_TOPIC_PUBS', 'iot/PlantCare');
            MQTT::connection()->publish($topic, $message);

            $this->inputMessage = '';
            session()->flash('success', 'Pesan berhasil dikirim');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengirim pesan: ' . $e->getMessage());
        }
    }

    
    public function render()
    {
        return view('livewire.camera');
    }
}

