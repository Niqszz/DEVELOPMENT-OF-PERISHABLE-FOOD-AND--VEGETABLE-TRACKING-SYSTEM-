@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center">
    <div class="row">
        @if(session('success'))
        <div class="col-12">
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        </div>
        @endif

        {{-- Display validation and query exception errors --}}
        @if($errors->any())
        <div class="col-12">
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <div class="col-12 pt-4">
            <div class="text-center mb-4">
                <!-- Profile Image with Edit Icon -->
                <div class="position-relative d-inline-block">
                    <img src="{{ collect(['jpg', 'jpeg', 'png', 'gif'])->map(fn($ext) => 'profile/' . $user->id .
                    '/profile-image-' . $user->id . '.' . $ext)->first(fn($path) => file_exists(public_path($path)))
                    ? asset(collect(['jpg', 'jpeg', 'png', 'gif'])->map(fn($ext) => 'profile/' . $user->id .
                    '/profile-image-' . $user->id . '.' . $ext)->first(fn($path) => file_exists(public_path($path))))
                    : asset('assets/img/alternative/anon-avatar.png') }}" alt="Profile Photo" class="rounded-circle"
                    width="100" height="100">
                    <!-- Hidden File Input -->
                    <form action="{{ route('profile.updateImage') }}" method="POST" enctype="multipart/form-data" id="imageUploadForm">
                        @csrf
                        <input type="file" name="profile_image" id="profileImageInput" style="display: none;" onchange="document.getElementById('imageUploadForm').submit();">
                    </form>
                    <!-- Edit Button -->
                    <button class="btn btn-light position-absolute top-0 end-0" style="border-radius: 50%; padding: 4px; width: 33px;" onclick="document.getElementById('profileImageInput').click();">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                </div>
                <h6 class="mt-2">Profile Photo</h6>
            </div>
        </div>

        <div class="col-12 d-flex justify-content-center">
            <div class="card p-4" style="width: 500px; border-radius: 10px;">
                <!-- Form Fields -->
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Name" value="{{ old('name', $user->name) }}">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="{{ old('email', $user->email) }}">
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone Number" value="{{ old('phone', $user->phone) }}">
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between mt-3">
                        <button type="submit" class="btn btn-danger">Save</button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">Change Password</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Change Password Modal -->
        <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('profile.changePassword') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
