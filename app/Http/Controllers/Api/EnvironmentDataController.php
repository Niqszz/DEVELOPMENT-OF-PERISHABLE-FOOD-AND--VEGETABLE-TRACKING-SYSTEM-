<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EnvironmentSensor;
use Illuminate\Http\Request;

class EnvironmentDataController extends Controller
{
    /**
     * Handle the incoming POST request to update data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        // Validate incoming data
        $request->validate([
            'deviceId' => 'required|string',
            'temperature' => 'required|numeric',
            'humidity' => 'required|numeric',
        ]);

        // Find the existing record by deviceId
        $deviceData = EnvironmentSensor::where('deviceId', $request->deviceId)->first();

        if ($deviceData) {
            // Update the existing record with new temperature and humidity values
            $deviceData->update([
                'temperature' => $request->temperature,
                'humidity' => $request->humidity,
                'isConnected'=>1,
            ]);
            return response()->json(['message' => 'Data updated successfully']);
        } else {
            // If the record doesn't exist, return an error response
            return response()->json(['message' => 'Device ID not found'], 404);
        }
    }
}
