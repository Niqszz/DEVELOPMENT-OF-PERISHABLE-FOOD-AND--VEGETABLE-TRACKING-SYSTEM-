<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\EnvironmentSensor;
use App\Models\SpoiledgeReading;
use Illuminate\Http\Request;

class StartReading extends Controller
{
    function timeToFloat($time) {
        // Split the time string into hours, minutes, and seconds
        $parts = explode(':', $time);

        // Convert the time to total seconds
        $totalSeconds = (int)$parts[0] * 3600 + (int)$parts[1] * 60 + (int)$parts[2];

        return (float)$totalSeconds;  // Return as float
    }

	public function start(Request $request)
	{
	    $deviceId = $request->input('device_id');

        $device = SpoiledgeReading::where('sdeviceId', $deviceId)
                                ->where('shouldStart', 1)
                                ->first();
	    if ($device) {
            // Get associated product
            $productId = SpoiledgeReading::where('sdeviceId', $deviceId)->value('product_id');
            $product = Product::where('id', $productId)->first();
            $enviromentSensor = EnvironmentSensor::where('deviceId', $product->deviceId)->first();
            $cumulativeDurationTemp = $product->cumulative_duration_out_of_range_temperature;
            $cumulativeDurationHum = $product->cumulative_duration_out_of_range_humidity;
            $currentTemp = $enviromentSensor->temperature;
            $currentHum = $enviromentSensor->humidity;
            $environmentSensorConnected = $enviromentSensor->isConnected;

	        if (is_null($cumulativeDurationTemp) && is_null($cumulativeDurationHum)) {
	            // Step 2: If both are null, check if the environment sensor is connected
	            if (!$environmentSensorConnected) {
	                return response()->json(['message' => 'Cannot start reading, sensor not connected', 'status' => false], 400);
	            }
	            return response()->json([
	                'message' => "Start command sent to device {$deviceId}",
	                'status' => true,
                    'suitableTemp'=>$product->suitableTemp,
                    'suitableHumidty'=>$product->suitableHumidity,
	                'temperature' => $currentTemp,
	                'humidity' => $currentHum,
	                'goodScore' => $product->goodScore,
	                'badScore' => $product->badScore,
                    'product_id'=>$productId,
	            ]);
	        } else {
	            return response()->json([
	                'message' => "Start command sent to device {$deviceId}",
	                'status' => true,
	                'cumulativeDurationHum' =>$this->timeToFloat($cumulativeDurationHum),
	                'cumulativeDurationTemp' =>$this->timeToFloat($cumulativeDurationTemp),
                    'suitableTemp'=>$product->suitableTemp,
                    'suitableHumidty'=>$product->suitableHumidity,
	                'temperature' => $currentTemp,
	                'humidity' => $currentHum,
	                'goodScore' => $product->goodScore,
	                'badScore' => $product->badScore,
                    'product_id'=>$productId,
	            ]);
	        }
	    }else{
		    return response()->json([
		        'message' => "Command has not started",
		        'status' => false
		    ]);
	    }

        return response()->json(['message' => 'Device not found', 'status' => false], 404);
    }


    public function stop(Request $request)
    {
        $sdeviceId = $request->input('device_id');

        $sdevice = SpoiledgeReading::where('sdeviceId', $sdeviceId)->first();
        if ($sdevice) {

            $sdevice->shouldStart = false;
            $sdevice->product_id = NULL;
            $sdevice->save();

            return response()->json([
                'message' => "Stop command sent to device {$sdeviceId}",
                'status' => true
            ]);
        }

        return response()->json(['message' => 'Device not found', 'status' => false], 404);
    }
}
