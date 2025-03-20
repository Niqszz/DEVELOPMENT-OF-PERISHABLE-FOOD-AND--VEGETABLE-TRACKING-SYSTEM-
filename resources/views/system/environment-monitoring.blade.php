@extends('layouts.app')
@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="row row-cols-3">
        @php
            $totalDevices = $devices->count();
            $addDeviceButtonsNeeded = 6 - $totalDevices;
        @endphp

        @if ($devices->isNotEmpty())

            @foreach ($devices as $device)
            <div class="col p-4">
                <div class="device-container" data-device-id="{{ $device->deviceId }}">
                    <div class="device-name">
                        <p>{{ $device->deviceName }}</p>
                    </div>
                    <div class="row device-reading">
                        <p class="connection-status"></p> <!-- This is where the connection status will be displayed -->
                        <div id="deviceReading">
                            <div class="col-6 temperature-reading">
                                <p class="environment">Temperature</p>
                                <p class="reading" id="temperature-{{ $device->deviceId }}">{{ $device->temperature ?? '0°C' }}</p>
                            </div>
                            <div class="col-6 humidity-reading">
                                <p class="environment">Humidity</p>
                                <p class="reading" id="humidity-{{ $device->deviceId }}">{{ $device->humidity ?? '0%' }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- Overview Button -->
                    <a href="#" data-bs-toggle="modal" data-bs-target="#addDeviceModal" data-device-id="{{$device->deviceId}}">
                        <div class="overview">
                            <p class="text-center">Overview</p>
                        </div>
                    </a>

                    <!-- Modal for Overview -->
                    <div class="modal fade" id="addDeviceModal" tabindex="-1" aria-labelledby="addDeviceModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addDeviceModalLabel">Device Overview</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- Device Data Display -->
                                    <p><strong>Humidity:</strong> <span id="humidity">--</span>%</p>
                                    <p><strong>Temperature:</strong> <span id="temperature">--</span>°C</p>

                                    <!-- Connected Products List -->
                                    <h6>Connected Products</h6>
                                    <ul id="connected-products-list">
                                        {{-- Product here --}}
                                    </ul>

                                    <!-- Form for Updating Device Name -->
                                    <form action="{{ route('environment-sensors.update') }}" method="POST">
                                        @csrf
                                        <label for="deviceName" class="form-label py-2 bold"><b>Edit Device Name</b></label>
                                        <input type="text" class="form-control pb-1" id="deviceName" name="deviceName" required>
                                        <input type="hidden" name="deviceId" id="update-device-id" value="">
                                        <div class="py-2">
                                            <button type="submit" class="btn btn-primary">Update Device Name</button>
                                        </div>
                                    </form>

                                    <!-- Disconnect Button -->
                                    <form action="{{ route('environment-sensors.disconnect') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="deviceId" id="disconnect-device-id" value="">
                                        <button type="submit" class="btn btn-danger">Disconnect</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            @endforeach

        @else
        <div class="col-12 pt-4">
            <p class="text-center">No devices connected.</p>
        </div>

        @endif

        <!-- Add Device Button(s) -->
        @for ($i = 0; $i < $addDeviceButtonsNeeded; $i++)
            <div class="col p-4">
                <div class="device-container">
                    <div class="add-device-container" id="add-device-container">
                        <button class="add-device-button" data-bs-toggle="modal" data-bs-target="#addDeviceModal">
                            Add Device +
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal for Adding Device -->
            <div class="modal fade" id="addDeviceModal" tabindex="-1" aria-labelledby="addDeviceModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addDeviceModalLabel">Add Device</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('environment-sensors.store') }}" method="POST" id="addDeviceForm">
                                @csrf
                                <div class="mb-3">
                                    <label for="deviceId" class="form-label">Device ID</label>
                                    <input type="text" class="form-control" id="deviceId" name="deviceId" required>
                                </div>
                                <div class="mb-3">
                                    <label for="deviceName" class="form-label">Device Name</label>
                                    <input type="text" class="form-control" id="deviceName" name="deviceName" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endfor
    </div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Select all buttons with the class "overview"
    const overviewButtons = document.querySelectorAll(".overview");

    // Iterate over each overview button
    overviewButtons.forEach(function (overviewButton) {
        // Add a click event listener to each button
        overviewButton.addEventListener("click", function () {
            // Use closest to find the nearest anchor element and get the data-device-id attribute
            const deviceId = this.closest('a').getAttribute("data-device-id");
            console.log("Device ID:", deviceId); // Log the Device ID for confirmation

            // Fetch data about the environment sensor using the deviceId
            fetch(`/environment-sensors/overview?deviceId=${deviceId}`)
                .then(response => {
                    // Check if the response is not OK (status code not in the range 200-299)
                    if (!response.ok) {
                        // Throw an error with the response status text
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    // Parse the JSON from the response
                    return response.json();
                })
                .then(data => {
                    // Check if there is an error in the returned data
                    if (data.error) {
                        // Display an alert with the error message
                        alert(data.error);
                    } else {
                        // Update the humidity display with the fetched data
                        document.getElementById("humidity").innerText = data.humidity;
                        // Update the temperature display with the fetched data
                        document.getElementById("temperature").innerText = data.temperature;

                        // Get the list element where connected products will be displayed
                        const productsList = document.getElementById("connected-products-list");
                        productsList.innerHTML = ""; // Clear any existing items in the list

                        // Check if there are no connected products
                        if (data.connectedProducts.length === 0) {
                            // Create a message for no products
                            const noProductMessage = document.createElement("li");
                            noProductMessage.innerText = "No product connected to this Device";
                            // Append the no product message to the list
                            productsList.appendChild(noProductMessage);
                        } else {
                            // If there are products, add them to the list
                            data.connectedProducts.forEach(product => {
                                const listItem = document.createElement("li");
                                listItem.innerText = product.productName; // Assume product has a name attribute
                                // Append the list item to the connected products list
                                productsList.appendChild(listItem);
                            });
                        }

                        // Set the deviceId for updating and disconnecting
                        document.getElementById("update-device-id").value = deviceId;
                        // Also set the deviceId for the disconnect action
                        document.getElementById("disconnect-device-id").value = deviceId;
                    }
                })
                .catch(error => console.error("Error fetching overview data:", error)); // Log any errors that occur during the fetch
        });
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Function to fetch and update temperature, humidity, and connection status for all devices
    function updateReadings() {
        const deviceContainers = document.querySelectorAll('.device-container');

        deviceContainers.forEach((container) => {
            const deviceId = container.getAttribute('data-device-id');
            if (!deviceId) return;  // Skip if deviceId is not found

            fetch(`api/environment-sensor-data?device_id=${deviceId}`)
                .then(response => response.json())
                .then(data => {
                    // Ensure the device's data exists before updating the DOM
                    if (data) {
                        const temperatureElement = document.getElementById(`temperature-${deviceId}`);
                        const humidityElement = document.getElementById(`humidity-${deviceId}`);
                        const connectionStatusElement = container.querySelector('.connection-status');
                        const deviceReadingElement = container.querySelector('#deviceReading');

                        // Update temperature and humidity
                        if (temperatureElement) {
                            temperatureElement.textContent = data.temperature ?? '0°C';  // Default if not found
                        }
                        if (humidityElement) {
                            humidityElement.textContent = data.humidity ?? '0%';  // Default if not found
                        }

                        // Update connection status
                        if (data.isConnected == 0) {
                            if (connectionStatusElement) {
                                connectionStatusElement.textContent = 'Device is not connected to internet';
                            }
                            if (deviceReadingElement) {
                                deviceReadingElement.style.display = 'none'; // Hide the readings if disconnected
                            }
                        } else {
                            if (connectionStatusElement) {
                                connectionStatusElement.textContent = ''; // Clear the message when connected
                            }
                            if (deviceReadingElement) {
                                deviceReadingElement.style.display = 'flex'; // Show the readings if connected
                            }
                        }
                    }
                })
                .catch(error => {
                    console.error(`Error fetching readings for device ${deviceId}:`, error);
                });
        });
    }

    // Periodically update the readings and connection status every second (1000ms) for all devices at once
    setInterval(updateReadings, 1000);
});

</script>

@endsection
