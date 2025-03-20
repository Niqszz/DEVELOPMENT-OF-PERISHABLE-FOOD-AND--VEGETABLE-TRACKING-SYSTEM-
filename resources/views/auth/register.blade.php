@include('layouts.partial.head')

<div class="container-fluid register">
    <div class="row py-5 ">
        <div class="col-12 d-flex justify-content-center ">
            <div class="register-card">
                <h2>Register</h2>
        
                <!-- Display Validation Errors -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
        
                <form method="POST" action="{{ route('register') }}">
                    @csrf
        
                    <!-- Name -->
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input id="name" type="text" name="name" class="form-control" required autofocus>
                    </div>
        
                    <!-- Email Address -->
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input id="email" type="email" name="email" class="form-control" required>
                    </div>
        
                    <!-- Phone Number -->
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input id="phone" type="text" name="phone" class="form-control" required>
                    </div>
        
                    <!-- Gender -->
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender" class="form-control" required>
                            <option value="" disabled selected>Select</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
        
                    <!-- Password -->
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input id="password" type="password" name="password" class="form-control" required>
                    </div>
        
                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required>
                    </div>
        
                    <!-- Buttons -->
                    <div class="form-actions">
                        <button type="submit" class="register-btn">Register</button>
                        <button type="button" class="btn btn-secondary" onclick="window.location='{{ url('/') }}'">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
