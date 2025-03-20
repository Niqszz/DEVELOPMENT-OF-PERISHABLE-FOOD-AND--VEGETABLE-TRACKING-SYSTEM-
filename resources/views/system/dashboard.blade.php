@extends('layouts.app')

@section('content')
<div class="dashboard-background"></div>
<div class="row position-relative background-image">
        <div class="col-6">
        <div class="row row-cols-2 calendar p-5" id="datetime-container">
            <div class="col-3 d-flex flex-column text-center date-display" id="date-display">Loading date...</div>
            <div class="col-9 d-flex justify-content-center hour-display" >
                <div class="d-flex align-self-center" id="hour-display">
                    Loading hour...
                </div>
            </div>
        </div>


        <div class="row pb-5 px-5">
            <div class="device-environtment">
                <div class="col-12">
                    <h1 class="text-center">Device</h1>
                </div>
                <!-- Device selection dropdown -->
		<div class="col-12 d-flex justify-content-center mb-3">
		    <select id="device_select" class="form-control">
		        <option value="">Select Device</option>
		        @foreach ($devices as $device)
		            <option value="{{ $device->deviceId }}">{{ $device->deviceName }}</option>
		        @endforeach
		    </select>
		</div>

                <!-- Real-time readings box -->
                <div class="col-12 d-flex readind-box">
                    <div class="temperature col-6">
                        <p id="temperature_reading" class="text-center reading">0°C</p>
                        <p class="text-center">Temperature</p>
                    </div>
                    <div class="temperature col-6">
                        <p id="humidity_reading" class="text-center reading">0%</p>
                        <p class="text-center">Humidity</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="row">
            <div class="col-12 pt-5 pe-5 pb-3">
                <div class="status">
                    <p class="text-center">Status</p>
                    <hr>
                    <div class="row row-cols-3 calendar p-1" id="datetime-container">
                        <div class="col text-center" id="good">
                            <p class="reading" id="good-count">{{ $goodCount }}</p>
                            <p>Good</p>
                        </div>
                        <div class="col text-center" id="average">
                            <p class="reading" id="average-count">{{ $averageCount }}</p>
                            <p>Average</p>
                        </div>
                        <div class="col text-center" id="bad">
                            <p class="reading" id="bad-count">{{ $badCount }}</p>
                            <p>Bad</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 pb-5 pe-5">
                <div class="device-status">
                    <p class="text-center">Device Status</p>
                <hr>
                <div class="row row-cols-3">
                    <div class="col">
                        <img src="{{ asset('assets/img/icon/Environment.png') }}" alt="imge-1">
                    </div>
                    <div class="col">
                        <div class="d-flex flex-column">
                            <p>Environment Sensor</p>
                            <p>Device Connected: {{ $devices->count() }}</p>
                        </div>
                    </div>
                    <div class="col">
                        @if($devices->count()== 0)
                            <img src="{{ asset('assets/img/icon/Not Connected.png') }}" alt="imge-1">
                        @else
                        <img src="{{ asset('assets/img/icon/Connected.png') }}" alt="imge-1">
                        @endif
                    </div>

                    <div class="col">
                        <img src="{{ asset('assets/img/icon/Spoiledge.png') }}" alt="imge-1">
                    </div>
                    <div class="col">
                        <div class="d-flex flex-column">
                            <p>Spoiledge Sensor</p>
                            <p>Device Connecter: {{$spoiledgedevices->count()}}</p>
                        </div>
                    </div>
                    <div class="col">
                        @if($spoiledgedevices->count()== 0)
                            <img src="{{ asset('assets/img/icon/Not Connected.png') }}" alt="imge-1">
                        @else
                            <img src="{{ asset('assets/img/icon/Connected.png') }}" alt="imge-1">
                        @endif
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateDateTime() {
        fetch('/current-datetime-html')
            .then(response => response.json())
            .then(data => {
                document.getElementById('date-display').innerHTML = `<p class='day'>${data.currentDayDay}</p> <p class='month'> ${data.currentMonth}</p>`;
                document.getElementById('hour-display').innerHTML = `<span class='hour'>${data.currentHour}:</span><span class='minute'>${data.currentMinute} ${data.currentTime}</span>`; // Fixed typo here
            })
            .catch(error => console.error('Error fetching date and time:', error));
    }
    // Update every minute (60000ms) or as frequently as needed
    setInterval(updateDateTime, 1000);
    updateDateTime(); // Initial load
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deviceSelect = document.getElementById('device_select');
        const temperatureReading = document.getElementById('temperature_reading');
        const humidityReading = document.getElementById('humidity_reading');

        // Function to fetch sensor data
        function fetchSensorData(deviceId) {
            // Ensure a device is selected before fetching data
            if (!deviceId) {
                return;
            }

            // Send an AJAX request to get the data for the selected device
            fetch(`api/environment-sensor-data?device_id=${deviceId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Update the temperature and humidity readings
                temperatureReading.textContent = `${data.temperature}°C`;
                humidityReading.textContent = `${data.humidity}%`;
            })
            .catch(error => console.error('Error fetching sensor data:', error));
        }

        // Event listener for device selection
        deviceSelect.addEventListener('change', function () {
            const selectedDeviceId = deviceSelect.value;
            // Fetch data for the newly selected device
            fetchSensorData(selectedDeviceId);
        });

        // Function to periodically fetch sensor data
        setInterval(() => {
            const selectedDeviceId = deviceSelect.value;
            // Fetch data if a device is selected
            if (selectedDeviceId) {
                fetchSensorData(selectedDeviceId);
            }
        }, 1000); // Fetch data every 1 second (1000ms)
    });

</script>

@endsection
