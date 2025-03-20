<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EnvironmentSensor;
use Illuminate\Http\Request;

class getSensorData extends Controller
{
    public function getSensorData(Request $request)
    {
        // Assuming device_id is sent from the frontend
        $deviceId = $request->input('device_id');

        // Fetch the sensor data for the given device
        $sensorData = EnvironmentSensor::where('deviceId', $deviceId)->first();

        // Return the data as JSON
        return response()->json([
            'temperature' => $sensorData->temperature ?? '0',
            'humidity' => $sensorData->humidity ?? '0',
            'isConnected' => $sensorData->isConnected,
        ]);
    }
}
