<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\SpoiledgeReading;

class SpoiledgeDataController extends Controller
{
    public function store(Request $request)
    {
        // Validate incoming data
        $request->validate([
            'sdeviceId' => 'required|string',   // Make sure sdeviceId is required
            'methane_level_ppm' => 'required|numeric',
        ]);

        // Find the reading by sdeviceId and update the methane_level_ppm
        $reading = SpoiledgeReading::where('sdeviceId', $request->sdeviceId)->first();

        // Check if the reading exists
        if ($reading) {
            // Update the reading record
            $reading->update([
                'methane_level_ppm' => $request->methane_level_ppm,
                'isConnected' => 1,  //TO MARK IF THE DEVICE IS CONNECTED TO THE INTERNET
            ]);

            // Return the updated data
            return response()->json([
                'success' => true,
                'data' => $reading
            ], 200);
        } else {
            // If the reading doesn't exist, return a 404 error
            return response()->json([
                'success' => false,
                'message' => 'Reading not found'
            ], 404);
        }
    }

    public function checkConnectionStatus($deviceId)
        {
            // Find the device by its ID
            $sdevice = SpoiledgeReading::where('sdeviceId', $deviceId)->first();

            if (!$sdevice) {
                return response()->json(['message' => 'Device not found.'], 404);
            }

            // Calculate the difference in time from the last update
            $updated_at = Carbon::parse($sdevice->updated_at);
            $timeDifference = abs(Carbon::now()->diffInMinutes($updated_at));
            $now =Carbon::now();
            if ($timeDifference > 1) { // No log update in the last 1 minute
                $sdevice->isConnected = 0;  // Mark as disconnected
                $sdevice->save();
                return response()->json(['message' => 'Device marked as disconnected.']);
            }

            return response()->json(['message' => $now]);
        }
}
