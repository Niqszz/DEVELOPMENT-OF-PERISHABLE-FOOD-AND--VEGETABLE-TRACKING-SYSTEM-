<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\EnvironmentSensor;
use App\Models\SpoiledgeReading;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use App\Models\Notification;
use Illuminate\Support\Facades\Mail;
use App\Mail\EnvironmentNotification;

class ProductTemperatureHumidityLog extends Command
{
    // The name and signature of the console command.
    protected $signature = 'product:log-temperature-humidity';

    // The console command description.
    protected $description = 'Log temperature and humidity of products and device status';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        //////////////////////////////////////////////////////////////////////////////////////////////////////
        //Send Notification
        $products = Product::whereNotNull('deviceId')->get();

        // Loop through each product
        foreach ($products as $product) {
            $deviceId = $product->deviceId;

            // Step 2: Get the temperature and humidity from EnvironmentSensor where deviceId matches
            $sensor = EnvironmentSensor::where('device_id', $deviceId)->first();

            if ($sensor) {
                $temperature = $sensor->temperature;
                $humidity = $sensor->humidity;

                // Step 3: Check if the environment conditions are unsuitable (below the suitable values)
                $suitableTemperature = 20; // Replace with your suitable temperature value
                $suitableHumidity = 60; // Replace with your suitable humidity value

                if ($temperature < $suitableTemperature || $humidity < $suitableHumidity) {

                    // Step 4: Check the notification table to see if this product_id already has a notification record
                    $notification = Notification::where('product_id', $product->id)->first();

                    if ($notification) {
                        // Check if the product already has a notification and if the resend interval (5 hours) has passed
                        $lastSentAt = Carbon::parse($notification->resent_at);
                        $currentHour = Carbon::now();

                        // Step 5: Check if the time difference between now and the resent_at is more than 5 hours
                        if ($currentHour->diffInHours($lastSentAt) > 5) {
                            // Update the resend_at time and send an email
                            $notification->update(['resent_at' => $currentHour]);

                            // Send the email notification to the user
                            $this->sendEmailNotification($product, $temperature, $humidity);
                        }
                    } else {
                        // Step 6: No notification exists, so insert a new record and send an email
                        Notification::create([
                            'product_id' => $product->id,
                            'notification_type_id' => 1, // Set the appropriate notification type ID
                            'device_id' => $deviceId,
                            'resent_at' => Carbon::now(),
                            'seen_at' => null,  // Initially not seen
                        ]);

                        // Send the email notification to the user
                        $this->sendEmailNotification($product, $temperature, $humidity);
                    }
                }
            }
        }
        //////////////////////////////////////////////////////////////////////////////////////////////////////

        // Get all products
        $products = Product::all();
        $devices = EnvironmentSensor::all();
        $sdevices = SpoiledgeReading::all();

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

        foreach ($products as $product) {
            // Check if the product has a deviceId and if it's not null
            if (is_null($product->deviceId)) {
                // Path to the log file for the product
                $logFilePath = public_path("profile/{$product->userId}/log/product log/product-{$product->id}-log.txt");

                // Check if the log file exists. If not, create it with initial information.
                if (!File::exists($logFilePath)) {
                    // Initial product creation log
                    File::put($logFilePath, "Product Created - ID: {$product->id}, Name: {$product->productName}, Category: {$product->categoryId}, Timestamp: " . Carbon::now() . "\n");
                    File::put($logFilePath, "==============================================================\n");
                }

                // Append message about the product not being connected to a device
                File::append($logFilePath, "The product is not connected to a device yet.\n");
                File::append($logFilePath, "==============================================================\n");

                // Skip further checks for this product since it's not connected to a device
                continue;
            }

            // If the product has a device, proceed with the normal logging logic
            $sensor = EnvironmentSensor::find($product->deviceId);
            $device = EnvironmentSensor::find($product->deviceId);
            $deviceStatus = $device && $device->isConnected ? 'Connected' : 'Disconnected';

            if ($sensor && $deviceStatus === 'Connected') {
                // Get current temperature and humidity from the sensor
                $currentTemp = $sensor->temperature;
                $currentHumidity = $sensor->humidity;

                // Get product's suitable temperature and humidity
                $suitableTemp = $product->suitableTemp;
                $suitableHumidity = $product->suitableHumidity;

                // Get the current time
                $currentTime = Carbon::now();

                // Initialize out of range check variables
                $outOfRangeTemperature = false;
                $outOfRangeHumidity = false;
                $durationOutOfRangeTemperature = 0;
                $durationOutOfRangeHumidity = 0;

                if ($currentTemp < $suitableTemp) {
                    $outOfRangeTemperature = true;
                    // Store first occurrence of out-of-range time if not already stored
                    if ($product->first_out_of_range_temperature == '00:00:00') {
                        $product->first_out_of_range_temperature = $currentTime;
                        $product->save();
                    }
                    $durationOutOfRangeTemperature = abs($currentTime->diffInMinutes($product->first_out_of_range_temperature));


                    // Convert the cumulative time to a Carbon instance to manipulate
                    $cumulativeDuration = Carbon::createFromFormat('H:i:s', $product->cumulative_duration_out_of_range_temperature);

                    // Increment the cumulative duration by the calculated duration
                    $cumulativeDuration->addMinutes(1);

                    // Update the cumulative duration in the product (store as time string)
                    $product->cumulative_duration_out_of_range_temperature = $cumulativeDuration->format('H:i:s');
                    $product->save();
                } else {
                    // Reset the first occurrence when temperature is back to normal
                    $outOfRangeTemperature = false;
                    if ($product->first_out_of_range_temperature != '00:00:00') {
                        $product->first_out_of_range_temperature = '00:00:00';
                        $product->save();
                    }
                }

                if ($currentHumidity < $suitableHumidity) {
                    $outOfRangeHumidity = true;
                    // Store first occurrence of out-of-range time if not already stored
                    if ($product->first_out_of_range_humidity == '00:00:00') {
                        $product->first_out_of_range_humidity = $currentTime;
                        $product->save();
                    }
                    $durationOutOfRangeHumidity = abs($currentTime->diffInMinutes($product->first_out_of_range_humidity));
                    // Convert the cumulative time to a Carbon instance to manipulate
                    $cumulativeDuration = Carbon::createFromFormat('H:i:s', $product->cumulative_duration_out_of_range_humidity);

                    // Increment the cumulative duration by the calculated duration
                    $cumulativeDuration->addMinutes(1);

                    // Update the cumulative duration in the product (store as time string)
                    $product->cumulative_duration_out_of_range_humidity = $cumulativeDuration->format('H:i:s');
                    $product->save();
                } else {
                    // Reset the first occurrence when humidity is back to normal
                    $outOfRangeHumidity = false;
                    if ($product->first_out_of_range_humidity != '00:00:00') {
                        $product->first_out_of_range_humidity = '00:00:00';
                        $product->save();
                    }
                }

                // Prepare the log message
                $logMessage = "The part at above is not appended while the lower part is appended\n\n";

                if ($outOfRangeTemperature || $outOfRangeHumidity) {
                    // Append to log file
                    $logMessage .= "==============================================================\n";
                    $logMessage .= "Cumulative duration in minute out of range(Temperature): " . $product->cumulative_duration_out_of_range_temperature . "\n";
                    $logMessage .= "Cumulative duration in minute out of range(Humidity): " . $product->cumulative_duration_out_of_range_humidity . "\n";
                    $logMessage .= "Current time duration in minute of range(Temperature): $durationOutOfRangeTemperature\n";
                    $logMessage .= "Current time duration in minute of range(Humidity): $durationOutOfRangeHumidity\n";
                    $logMessage .= "==============================================================\n";
                }

                // Check if device is connected to the internet

                // Log if device is disconnected
                if ($deviceStatus === 'Disconnected') {
                    $logMessage .= "Device is disconnected from the internet at: " . Carbon::now() . "\n";
                    $logMessage .= "==============================================================\n";
                }

                // Path to the log file for the product
                $logFilePath = public_path("profile/{$product->userId}/log/product log/product-{$product->id}-log.txt");

                // Create the log file if it doesn't exist
                if (!File::exists($logFilePath)) {
                    File::put($logFilePath, "Product Created - ID: {$product->id}, Name: {$product->productName}, Category: {$product->categoryId}, Timestamp: " . Carbon::now() . "\n");
                    File::put($logFilePath, "==============================================================\n");
                }

                // Append the log message
                File::append($logFilePath, $logMessage);
            }
            // Get all devices from the database
        }

        $this->info('Temperature, humidity, and device status logged successfully.');
    }
    /**
     * Send an email notification about the unsuitable environment conditions.
     *
     * @param Product $product
     * @param float $temperature
     * @param float $humidity
     */
    protected function sendEmailNotification($product, $temperature, $humidity)
    {
        // Send the email to the user associated with the product
        Mail::to($product->user->email)->send(new EnvironmentNotification($product, $temperature, $humidity));
    }
}
