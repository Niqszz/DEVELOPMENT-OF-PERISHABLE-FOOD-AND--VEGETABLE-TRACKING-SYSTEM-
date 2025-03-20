<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration form.
     */
    public function create()
    {
        return view('auth.register'); // Make sure this view exists
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:15'],
            'gender' => ['required', 'string', 'in:Male,Female'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Logging for troubleshooting
        Log::info('Registering user with:', [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'password' => Hash::make($request->password),
        ]);

        if (!$user) {
            abort(500, 'User could not be created');
        }

        // Create main profile folder based on user ID
        $profileFolderPath = public_path("profile/{$user->id}");
        if (!File::exists($profileFolderPath)) {
            File::makeDirectory($profileFolderPath, 0755, true);
        }

        // Create product image folder inside the user's profile folder
        $productImgFolderPath = public_path("profile/{$user->id}/product img");
        if (!File::exists($productImgFolderPath)) {
            File::makeDirectory($productImgFolderPath, 0755, true);
        }

        // Create log folder inside the user's profile folder
        $logFolderPath = public_path("profile/{$user->id}/log");
        if (!File::exists($logFolderPath)) {
            File::makeDirectory($logFolderPath, 0755, true);
        }
        // Create log folder inside the user's profile folder
        $logProductFolderPath = public_path("profile/{$user->id}/log/product log");
        if (!File::exists($logProductFolderPath)) {
            File::makeDirectory($logProductFolderPath, 0755, true);
        }
        // Create log folder inside the user's profile folder
        $logEnvironmentFolderPath = public_path("profile/{$user->id}/log/environment device log");
        if (!File::exists($logEnvironmentFolderPath)) {
            File::makeDirectory($logEnvironmentFolderPath, 0755, true);
        }

        // Create the log file named {userId}-reading-log.txt
        $logFilePath = "{$logFolderPath}/{$user->id}-reading-log.txt";
        if (!File::exists($logFilePath)) {
            File::put($logFilePath, "User {$user->id} log created on " . now());
        }


        // Fire the registered event for the new user
        event(new Registered($user));

        // Redirect to the login page
        return redirect()->route('login')->with('status', 'Registration successful. Please log in.');
    }
}
