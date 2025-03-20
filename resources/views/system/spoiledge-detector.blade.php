@extends('layouts.app')

@section('content')

<div class="container">

    <div class="row spoiledge-detector py-5">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif


        <div class="col-12 pt-4 show">
            <div class="add-detector-container" id="add-device-container">
                @if ($connectedDevice)
                    <!-- Show Disconnect Button if a device is connected -->
                    <form action="{{ route('spoiledge-sensors.device') }}" method="POST" id="disconnectDeviceForm">
                        @csrf
                        <input type="hidden" name="sdeviceId" value="{{ $connectedDevice->sdeviceId }}">
                        <button type="submit" class="btn btn-danger py-2" id="disconnectDeviceButton">
                            Disconnect Device
                        </button>
                    </form>
                    <form id="startSensorForm" action="{{ route('spoiledge-sensors.startSensor') }}" method="POST" onsubmit="handleFormSubmit(event)">
                        @csrf
                        <input type="hidden" name="device_id" value="{{ $connectedDevice->sdeviceId }}">
                        <input type="hidden" name="product_id" id="product_id" value=""> <!-- Hidden product ID -->
                        <button id="startButton" type="submit" class="btn btn-primary mt-2" disabled>
                            Start Sensor Readings
                        </button>
                    </form>
                @else
                    <!-- Show Connect Button if no device is connected -->
                    <button class="add-detector-button" data-bs-toggle="modal" data-bs-target="#addDeviceModal">
                        Connect Device +
                    </button>
                @endif
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
                        <form action="{{ route('spoiledge-sensors.store') }}" method="POST" id="addDeviceForm">
                            @csrf
                            <div class="mb-3">
                                <label for="sdeviceId" class="form-label">Device ID</label>
                                <input type="text" class="form-control" id="sdeviceIdInput" name="sdeviceId" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 pt-4">
            <div class="select-product">
                <select name="product" id="productSelect">
                    <option value="" disabled selected>Select a product</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" data-status="{{ $product->status }}">
                            {{ $product->productName }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-12 pt-4">
            <div class="status-2">
                <div class="product-status-box">
                    <p>Product Status: <span id="productStatus">Select a product to view its status</span></p>
                </div>
                <div class="device-status-spoiledge">
                    @if ($connectedDevice)
                        <p>Device Status:Connected</p>
                    @else
                        <p>Device Status:Disconnected</p>
                    @endif

                </div>
            </div>
        </div>
        <div class="col-12 pt-4">
            <div class="overview">
                <h3>Overview</h3>
                <hr>
                <div class="row row-cols-3">
                    <div class="col d-flex justify-content-center">
                        Methane: <span id="methane">NA</span> ppm
                    </div>
                    <div class="col d-flex justify-content-center">
                        Temperature: <span id="temperature">NA</span> °C
                    </div>
                    <div class="col d-flex justify-content-center">
                        Humidity: <span id="humidity">NA</span>%
                    </div>
                    <div class="col-12 d-flex justify-content-center result">
                        Result: <span id="result">NA</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // Select the dropdown and status display elements
    const productSelect = document.getElementById('productSelect');
    const productStatus = document.getElementById('productStatus');

    // Listen for changes on the dropdown
    productSelect.addEventListener('change', function () {
        // Get the selected option's data-status attribute
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const status = selectedOption.getAttribute('data-status');

        // Update the product status display
        productStatus.textContent = status ? status : 'Status not available';
    });
</script>

<!-- Your form and select element go here -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Select the dropdown, hidden input, and submit button
        const productSelect = document.getElementById('productSelect');
        const productIdInput = document.getElementById('product_id');
        const startButton = document.getElementById('startButton');

        // Listen for changes on the dropdown
        productSelect.addEventListener('change', function () {
            // Get the selected product ID from the dropdown
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const productId = selectedOption.value;

            // Update the hidden input field with the selected product ID
            productIdInput.value = productId;

            // Enable the start button if a product is selected
            if (productId) {
                startButton.disabled = false;
            } else {
                startButton.disabled = true;
            }
        });
    });
</script>



<script>
@if(isset($connectedDevice) && $connectedDevice->sdeviceId)
    // Handle form submission
    function handleFormSubmit(event) {
        // Prevent default form submission
        event.preventDefault();

        // Get the form and button elements
        const form = document.getElementById('startSensorForm');
        const button = document.getElementById('startButton');
        const deviceId = '{{ $connectedDevice->sdeviceId }}';
        const productId = document.getElementById('product_id').value; // Get product_id value
        const productSelect = document.getElementById('productSelect');

        // Check if product_id is set
        if (!productId) {
            alert('Please select a product!');
            return;
        }

        // Disable the button and change its appearance
        button.disabled = true;
        button.classList.remove('btn-primary');
        button.classList.add('btn-dangerous');
        button.textContent = 'Reading';

        productSelect.disabled = true;

        // Send the form data via fetch instead of traditional form submission
        fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                device_id: deviceId,
                product_id: productId  // Include the product_id in the request body
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Response data:', data); // Log response data for debugging

            if (data.status) {
                alert(data.message); // Display success message
            } else {
                alert('Failed to start sensor: ' + data.message); // Display error message if any
                // Re-enable the button if the request failed
                button.disabled = false;
                button.classList.remove('btn-dangerous');
                button.classList.add('btn-primary');
                button.textContent = 'Start Sensor Readings';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while starting the sensor.');
            // Re-enable the button if an error occurred
            button.disabled = false;
            button.classList.remove('btn-dangerous');
            button.classList.add('btn-primary');
            button.textContent = 'Start Sensor Readings';
        });
    }
@endif
</script>


<script>
@if(isset($connectedDevice) && $connectedDevice->sdeviceId)
    function checkDeviceStatus() {
        // Assign device ID from the Blade variable
        const deviceId = '{{ $connectedDevice->sdeviceId }}';

        // Get the selected product_id value from the dropdown
        const productId = document.getElementById('product_id').value;

        // Make a GET request with the device_id and product_id as query parameters
        fetch(`{{ route('spoiledge-sensors.checkDeviceStatus') }}?device_id=${deviceId}&product_id=${productId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            console.log('Response received:', response);
            return response.json();
        })
        .then(data => {
            // Update the displayed readings if the data is received successfully
            if (data.shouldStart === 0) {
                // Update the UI with the new sensor readings
                document.getElementById('methane').textContent = data.readings.methane || 'NA';
                document.getElementById('temperature').textContent = data.readings.temperature || 'NA';
                document.getElementById('humidity').textContent = data.readings.humidity || 'NA';
                document.getElementById('result').textContent = data.readings.status || 'NA';

                // Re-enable the button when shouldStart is 0
                const button = document.getElementById('startButton');
                button.disabled = false;
                button.classList.remove('btn-dangerous');
                button.classList.add('btn-primary');
                button.textContent = 'Start Sensor Readings';

                const productSelect = document.getElementById('productSelect');
                productSelect.disabled =false;
            }
        })
        .catch(error => {
            console.error('Error checking device status:', error);
        });
    }

    // Call the checkDeviceStatus function every 5 seconds
    setInterval(checkDeviceStatus, 1000);  // 5000ms = 5 seconds
@endif

</script>
@endsection
