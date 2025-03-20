@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row pt-5 spx-5">
        <div class="product-management-content" style="overflow-y: scroll">
            <div class="col-md-12">
                <!-- Search and Buttons -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="input-group w-25">
                        <input type="text" class="form-control" placeholder="Search">
                        <button class="btn btn-outline-secondary" type="button"><i class="fas fa-search"></i></button>
                    </div>
                    <div>
                        <a href="{{ route('products.create') }}" class="btn btn-danger">Add Product</a>
                        <button id="delete-button" class="btn btn-outline-secondary">Remove</button>
                    </div>
                </div>

                <!-- Table -->
                <form action="{{ route('products.delete') }}" method="POST" id="product-delete-form">
                    @csrf
                    @method('DELETE')

                    <!-- Table -->
                    <table class="table table-hover table-bordered rounded">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="select-all"></th>
                                <th>Product Name</th>
                                <th>Device</th>
                                <th>Category</th>
                                <th>Suitable Environment</th>
                                <th>Status</th>
                                <th>Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr>
                                    <td><input type="checkbox" name="selected_products[]" value="{{ $product->id }}"></td>
                                    <td>
                                        <img src="{{ asset($product->imagePath) }}" alt="{{ $product->productName }}" class="img-thumbnail" style="width: 30px;">
                                        {{ $product->productName }}
                                    </td>
                                    <td>{{ $product->device->deviceName ?? 'N/A' }}</td>
                                    <td>{{ $product->category->categoryName ?? 'N/A' }}</td>
                                    <td>
                                        Temp: {{ $product->suitableTemp }}°C<br>
                                        Humid: {{ $product->suitableHumidity }}
                                    </td>
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
                                    <td><a href="{{ route('products.edit', $product->id) }}" class="btn btn-outline-secondary">Edit</a></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No products found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </form>


                <!-- Pagination (if needed) -->
                <nav aria-label="Page navigation example" class="d-flex justify-content-center mt-3">
                    <ul class="pagination">
                        <!-- Your pagination logic here -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<script>
    // Select All Checkboxes
    document.getElementById('select-all').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('input[name="selected_products[]"]');
        checkboxes.forEach(checkbox => checkbox.checked = this.checked);
    });

    // Delete Confirmation
    document.getElementById('delete-button').addEventListener('click', function(event) {
        event.preventDefault();

        if (confirm('Are you sure you want to delete the selected products?')) {
            document.getElementById('product-delete-form').submit();
        }
    });
</script>

@endsection
