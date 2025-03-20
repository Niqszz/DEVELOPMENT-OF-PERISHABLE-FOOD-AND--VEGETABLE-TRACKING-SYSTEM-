@include('layouts.partial.head') <!-- Make sure you have a main layout file named `app.blade.php` -->

<div class="container-fluid login">
    <div class="row">
        <div class="col-12 d-flex justify-content-center align-content-center flex-wrap">
            <div class="login-card">
                <h2>Login</h2>
                
                <!-- Display success message from registration -->
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif
                
                <form method="POST" action="{{ route('login') }}">
                    @csrf
        
                    <!-- Email Address -->
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input id="email" type="email" name="email" class="form-control" required autofocus>
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
        
                    <!-- Password -->
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input id="password" type="password" name="password" class="form-control" required>
                        @error('password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
        
                    <!-- Remember Me -->
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Remember Me</label>
                    </div>
        
                    <!-- Login Button -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Login</button>
                        <button type="button" class="register-btn" onclick="window.location='/register';">Register</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>