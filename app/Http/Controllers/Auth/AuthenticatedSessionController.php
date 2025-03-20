<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Add this line
use Illuminate\Http\RedirectResponse;

class AuthenticatedSessionController extends Controller
{

    /**
     * Handle an incoming authentication request.
     */
    public function create()
    {
        return view('auth.login');
    }


    public function store(LoginRequest $request): RedirectResponse
    {
       // Log CSRF token and session ID
        Log::info('Login attempt', [
            'csrf_token' => $request->input('_token'),
            'session_id' => $request->session()->getId(),
            'user_email' => $request->input('email'),
        ]);

        try {
            $request->authenticate();
            $request->session()->regenerate();

            Log::info('Login successful for user', [
                'user_id' => Auth::id(),
                'session_id' => $request->session()->getId(),
            ]);

            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            Log::error('Login failed', [
                'error_message' => $e->getMessage(),
            ]);

            return redirect()->route('login')->withErrors(['login' => 'Login failed. Please try again.']);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login'); // Redirect to the login page after logout
    }
    
}
