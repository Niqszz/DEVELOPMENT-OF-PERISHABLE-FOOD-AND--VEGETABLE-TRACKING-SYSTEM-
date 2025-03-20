<?php

namespace App\Console\Commands;

use App\Models\EnvironmentSensor;
use App\Models\SpoiledgeReading;
use Illuminate\Support\Facades\Http;
use Illuminate\Console\Command;

class CheckDeviceConnectivity extends Command
{
    protected $signature = 'device:check-connectivity';
    protected $description = 'Check if the device has sent logs recently and update its connectivity status';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Get all devices from the database
        $devices = EnvironmentSensor::all();
        $sdevices = SpoiledgeReading::all();

        foreach ($devices as $device) {
            // Make the API request to check the device's status
            $response = Http::get("https://olivedrab-turtle-176629.hostingersite.com/api/device/{$device->deviceId}/status");

            // Check if the response is successful
            if ($response->successful()) {
                $data = $response->json();

                // Check if the device is connected based on the API response
                if ($data['message'] === 'Device is connected.') {
                    // If connected, update isConnected to true
		        $this->info("Device {$device->deviceId} is connected.");
                    continue;
                } else {
                    // If disconnected, update isConnected to false
                    $device->isConnected = false;
                    $device->save();
                    $this->error("Device {$device->deviceId} is disconnected.");
                }
            } else {
                $this->error("Failed to check connectivity for device {$device->deviceId}. Error: {$response->status()}");
            }
        }

        foreach ($sdevices as $sdevice) {
            // Make the API request to check the device's status
            $response = Http::get("https://olivedrab-turtle-176629.hostingersite.com/api/spoiledgeDevice/{$sdevice->sdeviceId}/status");

            // Check if the response is successful
            if ($response->successful()) {
                $data = $response->json();

                // Check if the device is connected based on the API response
                if ($data['message'] === 'Device is connected.') {
                    // If connected, update isConnected to true
		            $this->info("Device {$sdevice->sdeviceId} is connected.");
                    continue;
                } else {
                    // If disconnected, update isConnected to false
                    $sdevice->isConnected = false;
                    $sdevice->save();
                    $this->error("Device {$sdevice->sdeviceId} is disconnected.");
                }
            } else {
                $this->error("Failed to check connectivity for device {$sdevice->sdeviceId}. Error: {$response->status()}");
            }
        }
    }

}
