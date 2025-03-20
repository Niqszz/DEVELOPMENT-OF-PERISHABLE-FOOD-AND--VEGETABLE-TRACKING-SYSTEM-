<!DOCTYPE html>
<html lang="en">
@include('layouts.partial.head')
<body>
    <!-- Top Navbar -->
    <div class="container-fluid">
        <div class="row">
            <!-- Left Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-none d-md-block sidebar px-0">
                <div class="position-sticky pt-3">
                    <div class="text-center mt-4">
                        <img src="{{ collect(['jpg', 'jpeg', 'png', 'gif'])->map(fn($ext) => 'profile/' . $user->id .
                        '/profile-image-' . $user->id . '.' . $ext)->first(fn($path) => file_exists(public_path($path)))
                        ? asset(collect(['jpg', 'jpeg', 'png', 'gif'])->map(fn($ext) => 'profile/' . $user->id .
                        '/profile-image-' . $user->id . '.' . $ext)->first(fn($path) => file_exists(public_path($path))))
                        : asset('assets/img/alternative/anon-avatar.png') }}" alt="Profile Photo" class="rounded-circle"
                        width="100" height="100">

                        <p class="text-white mt-2">{{ $user->name }}</p>
                        <p class="text-white">ID Number: {{ $user->id }}</p>
                    </div>
                    <hr>
                    <ul class="nav flex-column mt-4">
                            <li class="nav-item">
                                <a class="nav-link text-white {{ request()->is('dashboard') ? 'active' : '' }}" href="/dashboard">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white {{ request()->is('profile-management') ? 'active' : '' }}" href="/profile-management">Profile Management</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white {{ request()->is('environment-monitoring') ? 'active' : '' }}" href="/environment-monitoring">Environment Monitoring</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white {{ request()->is('spoiledge-detector') ? 'active' : '' }}" href="/spoiledge-detector">Spoiledge Detector</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white {{ request()->is('product-management') ? 'active' : '' }}" href="/product-management">Product Management</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white {{ request()->is('notification') ? 'active' : '' }}" href="/notification">Notification</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white {{ request()->is('report') ? 'active' : '' }}" href="/report">REPORT</a>
                            </li>
                    </ul>
                    <hr>
                    <div class="mt-auto text-left mt-5">
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <a class="text-white logout-button" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Log out</a>
                    </div>
                </div>
            </nav>
            <div class="col-md-10 px-0 {{ str_replace('.', '-', Route::currentRouteName()) }}" id="contentContainer">
                <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
                    <div class="container-fluid">
                        <div class="col d-flex justify-content-center">
                            <a class="navbar-brand" href="/{{ str_replace('.', '/', Route::currentRouteName()) }}">{{ strtoupper(str_replace(['-', '.'], ' ', Route::currentRouteName())) }}</a>
                        </div>

                        <div class="justify-content-end">
                            <div class="d-flex align-items-center">
                                <img src="{{ collect(['jpg', 'jpeg', 'png', 'gif'])->map(fn($ext) => 'profile/' . $user->id .
                                '/profile-image-' . $user->id . '.' . $ext)->first(fn($path) => file_exists(public_path($path)))
                                ? asset(collect(['jpg', 'jpeg', 'png', 'gif'])->map(fn($ext) => 'profile/' . $user->id .
                                '/profile-image-' . $user->id . '.' . $ext)->first(fn($path) => file_exists(public_path($path))))
                                : asset('assets/img/alternative/anon-avatar.png') }}" alt="Profile Photo" class="rounded-circle"
                                width="40" height="40">

                                <i class="fas fa-bell me-3" style="font-size: 20px; cursor: pointer;"></i>
                                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                                    <span class="navbar-toggler-icon"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </nav>
                <div class="container position-relative">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
    <!-- Page content goes here -->

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</body>
@include('layouts.partial.footer')
</html>
