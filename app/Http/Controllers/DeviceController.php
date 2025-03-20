<?php

namespace App\Http\Controllers;

use App\Models\SpoiledgeReading; // Ensure you have this model created
use App\Models\Product;
use App\Models\EnvironmentSensor;
use App\Models\ProductCondition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DeviceController extends Controller
{
    /**
     * Connect a device for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'sdeviceId' => 'required|string|max:255',
        ]);

        // Find the device by sdeviceId
        $device = SpoiledgeReading::where('sdeviceId', $request->sdeviceId)->first();

        if ($device) {
            // Update userId if device exists
            $device->userId = Auth::id(); // Set the authenticated user's ID
            $device->save();

            return redirect()->route('spoiledge-detector')->with('success', 'Device connected successfully');
        }

        return redirect()->route('spoiledge-detector')->with('error', 'Device does not exist.');
    }

    /**
     * Disconnect a device for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function disconnectDevice(Request $request)
    {
        $validatedData = $request->validate([
            'sdeviceId' => 'required|string|max:50',
        ]);

        // Find the device by sdeviceId
        $device = SpoiledgeReading::where('sdeviceId', $validatedData['sdeviceId'])
                    ->where('userId', Auth::id()) // Ensure the device belongs to the authenticated user
                    ->first();

        if ($device) {
            // Clear the userId to disconnect the device
            $device->userId = null;
            $device->save();

            return redirect()->route('spoiledge-detector')->with('success', 'Device disconnected successfully');
        }

        return redirect()->route('spoiledge-detector')->with('error', 'Device not found or not connected.');
    }

    public function startSensor(Request $request)
    {
        try {
            Log::info('Incoming request data:', $request->all());
            // Validate that device_id and product_id are provided in the request
            $validated = $request->validate([
                'device_id' => 'required|string',
                'product_id' => 'required|integer|exists:products,id', // Validate product_id
            ]);

            // Find the connected device by the provided device_id
            $device = SpoiledgeReading::where('sdeviceId', $validated['device_id'])->first();
            $isConnected = SpoiledgeReading::where('sdeviceId', $validated['device_id'])
                                           ->where('isConnected', 1)
                                           ->first();

            $haveEnvironmentSensor = Product::where('id', $validated['product_id'])
                                           ->whereNotNull('deviceId')
                                           ->first();
            $productCondition = ProductCondition::where('product_id',$validated['product_id'])->first();
            if($productCondition){
                $productCondition -> humidity = NULL;
                $productCondition -> temperature = NULL;
                $productCondition -> cumulative_duration_humidity = NULL;
                $productCondition -> cumulative_duration_temperature = NULL;
                $productCondition -> averageMethaneReading = NULL;
                $productCondition -> status = NULL;

                $productCondition -> save();
            }
            // Check if the device exists
            if (!$device) {
                return response()->json(['status' => false, 'message' => 'Device not found'], 404);
            }

	    if ($isConnected) {
	        if($haveEnvironmentSensor){
	            $device->shouldStart = true;
	            $device->product_id =  $validated['product_id'];
		        $device->updated_at = now();
	            $device->save();  // Save the updated device state
	        }else{
	            return response()->json(['status' => false, 'message' => 'Product is not associate with any environment sensor']);
	        }

            } else {
                return response()->json(['status' => false, 'message' => 'Spoiledge Sensor is not connected to internet']);
            }

        return response()->json(['status' => true, 'message' => 'Sensor readings started.']);
    } catch (\Exception $e) {
        // Log the error for debugging
        Log::error('Error starting sensor: ' . $e->getMessage());

        return response()->json([
            'status' => false,
            'message' => 'An error occurred while starting the sensor.',
            'error' => $e->getMessage()
        ]);
    }
}

    //This is for the AJAX, TO TURN ON BACK THE BUTTON IF THE DEVICE ALREADY FINISH READING
    public function checkDeviceStatus(Request $request)
    {
        // You can fetch the device from the session or based on user/device ID
        $device = SpoiledgeReading::where('sdeviceId', $request->device_id)->first();
        $productCondition = ProductCondition::where('product_id',$request->product_id)->first();
	//dd($request->device_id);
        if (!$device) {
            return response()->json([
                'status' => false,
                'message' => 'Device not found',
            ], 404);
        }

        if(!$productCondition){
            return response()->json([
                'shouldStart' => $device->shouldStart,
            ]);
        }else{
            $readings = [
                'methane' => $productCondition->averageMethaneReading,
                'temperature' => $productCondition->temperature,
                'humidity' => $productCondition->humidity,
                'status' => $productCondition->status,
            ];

            return response()->json([
                'shouldStart' => $device->shouldStart,
                'readings' => $readings,
            ]);
        }
    }

}
