@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row p-5">
        <div class="product-management-content" style="overflow-y: scroll">
            <div class="col-md-12">
                <!-- Search and Buttons -->
                //<div class="d-flex justify-content-between align-items-center mb-3">
                //    <div class="input-group w-25">
                //        <input type="text" class="form-control" placeholder="Search">
                //        <button class="btn btn-outline-secondary" type="button"><i class="fas fa-search"></i></button>
                //    </div>
                //</div>

                <!-- Table -->
                <table class="table table-hover table-bordered rounded">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Product Name</th>
                            <th>Device</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Overview</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                        @if($product->status !== null)
                            <tr>
                                <td> <img src="{{ asset($product->imagePath) }}" alt="{{ $product->productName }}" class="img-thumbnail" style="width: 30px;"></td>
                                <td>{{ $product->productName }}</td>
                                <td>{{ $product->device->deviceName ?? 'N/A' }}</td>
                                <td>{{ $product->category->categoryName ?? 'N/A' }}</td>
                                <td>
                                    @if (in_array($product->status, ['Good', 'Average', 'Bad', 'Almost Bad']))
                                        <span class="badge
                                            {{ $product->status === 'Good' ? 'bg-success' : ($product->status === 'Average' ? 'bg-warning' : 'bg-danger') }}">
                                            {{ $product->status }}
                                        </span>
                                    @else
                                        {{ $product->status }}
                                    @endif
                                </td>
                                <th>
                                    <button class="btn btn-outline-secondary overview-btn" data-product-id="{{ $product->id }}">Overview</button>
                                </th>
                            </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Popup Structure in HTML -->
<div class="popup-overlay" style="display: none; z-index:3">
    <div class="popup-content">
        <h4>Product Overview</h4>
        <div class="product-details">
            <div class="product-image">
                <img id="popup-product-image" src="" alt="Product Image">
            </div>
            <div class="product-info">
                <h5>Product : <span id="popup-product-name"></span></h5>
                <p>Category: <span id="popup-product-category"></span></p>
                <p>Checked on: <span id="popup-checked-date"></span></p>
            </div>
        </div>
        <div class="environment-details">
            <div class="cumulative-duration">
                <h6>Cumulative Duration</h6>
                <p>Temperature: <span id="popup-cumulative-temp"></span> hours</p>
                <p>Humidity: <span id="popup-cumulative-humidity"></span> hours</p>
            </div>
            <div class="current-environment">
                <h6>Environment Readings</h6>
                <p>Temperature: <span id="popup-temperature"></span>°C</p>
                <p>Humidity: <span id="popup-humidity"></span>%</p>
            </div>
            <div class="current-environment">
                <h6>Methane Reading</h6>
                <p>Methane: <span id="popup-methane"></span>ppm</p>
            </div>
            <div class="status-section">
                <p>Status: <span id="popup-status"></span></p>
            </div>
        </div>
        <button class="close-popup">Close</button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Add click event listener to all overview buttons
        document.querySelectorAll('.overview-btn').forEach(button => {
            button.addEventListener('click', function () {
                // Get the product ID from the button's data attribute
                const productId = this.getAttribute('data-product-id');

                // Fetch product condition details from the server
                fetch(`/product-condition/${productId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const productCondition = data.data;

                        // Update the popup content with fetched data
                        document.querySelector('#popup-product-name').textContent = productCondition.productName;
                        // Assuming category information is in a different format, adjust accordingly
                        document.querySelector('#popup-product-category').textContent = productCondition.categoryId; // Adjust if necessary
                        document.querySelector('#popup-checked-date').textContent = new Date(productCondition.updated_at).toLocaleDateString();
                        document.querySelector('#popup-status').textContent = productCondition.status;
                        document.querySelector('#popup-status').className = getStatusClass(productCondition.status);
                        document.querySelector('#popup-methane').textContent = productCondition.averageMethaneReading;
                        document.querySelector('#popup-temperature').textContent = productCondition.temperature;
                        document.querySelector('#popup-humidity').textContent = productCondition.humidity;
                        document.querySelector('#popup-cumulative-temp').textContent = (productCondition.cumulative_duration_temperature / 3600).toFixed(2);
                        document.querySelector('#popup-cumulative-humidity').textContent = (productCondition.cumulative_duration_humidity / 3600).toFixed(2);

                        // Update the image path
                        document.querySelector('#popup-product-image').src = productCondition.imagePath;

                        // Show the popup
                        document.querySelector('.popup-overlay').style.display = 'flex';
                    } else {
                        alert('Failed to retrieve product details.');
                    }
                })
                .catch(error => {
                    console.error('Error fetching product condition:', error);
                });
            });
        });

        // Close the popup when the "Close" button is clicked
        document.querySelector('.close-popup').addEventListener('click', function () {
            document.querySelector('.popup-overlay').style.display = 'none';
        });

        // Utility function to get the CSS class based on status
        function getStatusClass(status) {
            switch (status) {
                case 'Good': return 'bg-success';
                case 'Average': return 'bg-warning';
                case 'Bad':
                case 'Almost Bad': return 'bg-danger';
                default: return 'bg-secondary';
            }
        }
    });

</script>

<style>
    .product-details {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .product-image img {
        width: 80px;
        height: auto;
    }

    .product-info {
        flex: 1;
        padding-left: 20px;
    }

    .environment-details {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-top: 20px;
    }

    .status-section p {
        font-weight: bold;
        font-size: 16px;
    }

    .bg-success {
        color: green;
    }

    .bg-warning {
        color: orange;
    }

    .bg-danger {
        color: red;
    }

    .popup-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .popup-content {
        background: white;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        max-width: 500px;
        width: 100%;
    }

    .close-popup {
        margin-top: 10px;
        padding: 5px 10px;
        background: #f5f5f5;
        border: none;
        cursor: pointer;
    }
</style>


@endsection
