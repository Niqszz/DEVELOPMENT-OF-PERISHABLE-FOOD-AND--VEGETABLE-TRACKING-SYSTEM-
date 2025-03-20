<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\EnvironmentSensor;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class EnvironmentSensorController extends Controller
{

    public function store(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'deviceId' => 'required|string|max:255',
                'deviceName' => 'required|string|max:255',
            ]);

            // Find the device by deviceId
            $sensor = EnvironmentSensor::where('deviceId', $request->deviceId)->first();

            if ($sensor) {
                // Check if the sensor is already connected to another user
                if ($sensor->userId && $sensor->userId !== Auth::id()) {
                    return redirect()->route('environment-monitoring')->with('error', 'Device is already connected by another user.');
                }

                // Update existing sensor with userId and deviceName
                $sensor->userId = Auth::id();
                $sensor->deviceName = $request->deviceName;
                $sensor->save();

                // Define log file path
                $userId = Auth::id();
                $deviceId = $request->deviceId;
                $logDir = public_path("profile/{$userId}/log/environment_device_log");
                $logFile = "{$logDir}/{$deviceId}-log.txt";

                // Ensure the directory exists
                if (!File::exists($logDir)) {
                    File::makeDirectory($logDir, 0755, true);
                }

                // Append log entry to the file
                $logEntry = "[" . now() . "] Device connected: {$request->deviceName}\n";
                File::append($logFile, $logEntry);

                return redirect()->route('environment-monitoring')->with('success', 'Device connected successfully');
            } else {
                return redirect()->route('environment-monitoring')->with('error', 'Device ID does not exist');
            }
        } catch (\Exception $e) {
            Log::error('Error storing environment sensor: ' . $e->getMessage());
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }


    public function getOverviewData(Request $request)
    {
        $deviceId = $request->deviceId;

        // Find the sensor and load its connected products
        $sensor = EnvironmentSensor::with('connectedProducts')->where('deviceId', $deviceId)->first();

        if ($sensor) {
            return response()->json([
                'humidity' => $sensor->humidity,
                'temperature' => $sensor->temperature,
                'connectedProducts' => $sensor->connectedProducts, // Products connected by deviceId
            ]);
        } else {
            return response()->json(['error' => 'Device not found'], 404);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'deviceId' => 'required|string|max:255',
            'deviceName' => 'required|string|max:255',
        ]);

        $deviceId = $request->deviceId;
        $deviceName = $request->deviceName; // Ambil deviceName dari permintaan

        // Kemas kini deviceName untuk deviceId yang diberikan
        EnvironmentSensor::where('deviceId', $deviceId)->update(['deviceName' => $deviceName]);

        return redirect()->route('environment-monitoring')->with('success', 'Device name updated successfully.');
    }


    public function disconnect(Request $request)
    {
        $deviceId = $request->deviceId;

        // Find the device by deviceId
        $sensor = EnvironmentSensor::where('deviceId', $deviceId)->first();

        // Update associated Product records to remove deviceId
        Product::where('deviceId', $deviceId)->update(['deviceId' => null]);

        if ($sensor) {
            // Clear userId and deviceName from the sensor record
            $sensor->userId = null;
            $sensor->deviceName = null;
            $sensor->save();

            // Define log file path
            $userId = Auth::id(); // Assuming Auth ID matches the user for the log
            $logFile = storage_path("profile/{$userId}/log/environment_device_log/{$deviceId}-log.txt");

            // Check if the log file exists and delete it
            if (File::exists($logFile)) {
                File::delete($logFile);
            }

            return redirect()->route('environment-monitoring')->with('success', 'Device disconnected successfully');
        } else {
            return redirect()->route('environment-monitoring')->with('error', 'Device not found');
        }
    }
}
