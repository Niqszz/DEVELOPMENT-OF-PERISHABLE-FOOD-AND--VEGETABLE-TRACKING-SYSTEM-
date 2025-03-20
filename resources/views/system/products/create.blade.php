@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row p-5">
        <div class="product-management-content">
            <h2>Add Product</h2>
            <div class="col-12 product-mangement-content">
                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- General Error Message -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="productName" class="form-label">Product Name</label>
                        <input type="text" class="form-control @error('productName') is-invalid @enderror" id="productName" name="productName" value="{{ old('productName') }}" required>
                        @error('productName')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="deviceId" class="form-label">Device</label>
                        @if($devices->isEmpty())
                            <div class="alert alert-warning" role="alert">
                                No device connected yet
                            </div>
                        @else
                            <select class="form-select @error('deviceId') is-invalid @enderror" id="deviceId" name="deviceId" required>
                                <option value="">Select Device</option>
                                @foreach($devices as $device)
                                    <option value="{{ $device->deviceId }}" >{{ $device->deviceName }}</option>
                                @endforeach
                            </select>
                            @error('deviceId')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="categoryId" class="form-label">Category</label>
                        <select class="form-select @error('categoryId') is-invalid @enderror" id="categoryId" name="categoryId" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $id => $categoryName)
                                <option value="{{ $id }}" {{ old('categoryId') == $id ? 'selected' : '' }}>{{ $categoryName }}</option>
                            @endforeach
                        </select>
                        @error('categoryId')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="suitableTemp" class="form-label">Suitable Temperature</label>
                        <input type="number" step="0.01" class="form-control @error('suitableTemp') is-invalid @enderror" id="suitableTemp" name="suitableTemp" value="{{ old('suitableTemp') }}" required>
                        @error('suitableTemp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="suitableHumidity" class="form-label">Suitable Humidity</label>
                        <input type="number" step="0.01" class="form-control @error('suitableHumidity') is-invalid @enderror" id="suitableHumidity" name="suitableHumidity" value="{{ old('suitableHumidity') }}" required>
                        @error('suitableHumidity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="goodScore" class="form-label">Good Score (0-100)</label>
                        <input type="number" class="form-control @error('goodScore') is-invalid @enderror" id="goodScore" name="goodScore" min="0" max="100" value="{{ old('goodScore') }}" required>
                        @error('goodScore')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="averageScore" class="form-label">Average Score (0-100)</label>
                        <input type="number" class="form-control @error('averageScore') is-invalid @enderror" id="averageScore" name="averageScore" min="0" max="100" value="{{ old('averageScore') }}" required>
                        @error('averageScore')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="badScore" class="form-label">Bad Score (0-100)</label>
                        <input type="number" class="form-control @error('badScore') is-invalid @enderror" id="badScore" name="badScore" min="0" max="100" value="{{ old('badScore') }}" required>
                        @error('badScore')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    </div>

                    <button type="submit" class="btn btn-primary">Add Product</button>
                </form>
            </div>
            <div class="col-12 my-3">
                <a href="{{ route('product-management') }}" class="btn btn-secondary mb-5">Back</a>
            </div>
            <!-- Back Button -->
        </div>
    </div>
</div>
@endsection
