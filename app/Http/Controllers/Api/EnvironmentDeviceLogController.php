<?php

    namespace App\Http\Controllers\Api;

    use App\Http\Controllers\Controller;
    use App\Models\EnvironmentSensor;
    use Carbon\Carbon;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\File;
    use Illuminate\Support\Facades\Log;


    class EnvironmentDeviceLogController extends Controller
    {
        /**
         * API to receive logs from the ESP32 device.
         */
        public function receiveLog(Request $request, $deviceId)
        {
            // Validate incoming data
            $request->validate([
                'temperature' => 'required|numeric',
                'humidity' => 'required|numeric',
                'log_data' => 'required|string', // Log data sent by ESP32
            ]);

            // Find the device by its ID
            $device = EnvironmentSensor::where('deviceId', $deviceId)->first();

            if (!$device) {
                return response()->json(['message' => 'Device not found.'], 404);
            }

            // Check if userId is null (device not associated with any user)
            if ($device->userId === null) {
                $device->last_log_at = Carbon::now();
                $device->save();
                return response()->json(['message' => 'Device is not connected to any user.'], 400);
            }

            // Log file path (profile/{userId}/log/environment_device_log/{deviceId}-log.txt)
            $logFilePath = public_path("profile/{$device->userId}/log/environment device log/{$device->deviceId}-log.txt");

            // Ensure the directory exists
            $logDirectory = public_path("profile/{$device->userId}/log/environment device log/");
            if (!File::exists($logDirectory)) {
                File::makeDirectory($logDirectory, 0755, true); // Create directory with correct permissions
            }

            // Ensure the log file exists, if not create it
            if (!File::exists($logFilePath)) {
                File::put($logFilePath, ""); // Create an empty log file if it doesn't exist
            }

            // Create or append log to the file
            $logContent = "Temperature: {$request->temperature}, Humidity: {$request->humidity}, Log Data: {$request->log_data}, Time: " . Carbon::now() . "\n";
            File::append($logFilePath, $logContent);

            // Update the device's last log timestamp in the database
            $device->last_log_at = Carbon::now();
            $device->isConnected = true;  // Mark as connected when log is updated
            $device->save();

            return response()->json(['message' => 'Log received and saved successfully.']);
        }

        /**
         * Check if the device is connected based on the log update time.
         */
        public function checkConnectionStatus($deviceId)
        {
            // Find the device by its ID
            $device = EnvironmentSensor::where('deviceId', $deviceId)->first();

            if (!$device) {
                return response()->json(['message' => 'Device not found.'], 404);
            }

            // Calculate the difference in time from the last log update
            $lastLogTime = Carbon::parse($device->last_log_at);
            $timeDifference = abs(Carbon::now()->diffInMinutes($lastLogTime));

            // Log the time difference
            Log::info("Time difference for device {$deviceId}: {$timeDifference} minutes.");

            if ($timeDifference > 1) { // No log update in the last 1 minute
                $device->isConnected = false;  // Mark as disconnected
                $device->save();
                return response()->json(['message' => 'Device marked as disconnected.']);
            }

            return response()->json(['message' => 'Device is connected.']);
        }
    }
