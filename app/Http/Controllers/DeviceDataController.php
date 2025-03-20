<?php

namespace App\Http\Controllers;

use App\Models\EnvironmentSensor;
use Illuminate\Http\Request;

class DeviceDataController extends Controller
{
    public function update($deviceId, $temperature, $humidity)
    {
        // Try to find the sensor data by deviceId
        $sensorData = EnvironmentSensor::where('deviceId', $deviceId)->first();

        // If record exists, update it
        if ($sensorData) {
            $sensorData->temperature = $temperature;
            $sensorData->humidity = $humidity;
            $sensorData->updated_at = now();  // Update the timestamp
            $sensorData->save();

            return response()->json([
                'message' => 'Device data updated successfully!',
                'deviceId' => $deviceId,
                'temperature' => $temperature,
                'humidity' => $humidity,
            ]);
        } else {
            return response()->json([
                'error' => 'Device data not found!',
            ], 404);
        }
    }
}
