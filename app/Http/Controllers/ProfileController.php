<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;


class ProfileController extends Controller
{
    // Display the profile form
    public function show()
    {
        $user = Auth::user();
        return view('profile', compact('user'));
    }

    // Handle profile updates
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validate input data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:15|unique:users,phone,' . $user->id,
        ], [
            // 'name.unique' => 'The name has already been taken.',
            'email.unique' => 'The email address has already been taken.',
            'phone.unique' => 'The phone number has already been taken.',
        ]);

        try {
            // Attempt to update the user
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);

            // Redirect back with success message
            return redirect()->route('profile-management')->with('success', 'Profile updated successfully.');

        } catch (QueryException $e) {
            // Check for duplicate entry error (SQLSTATE code 23000)
            if ($e->errorInfo[1] == 1062) {
                return redirect()->route('profile-management')
                    ->withErrors(['email' => 'The email address is already taken. Please choose another one.']);
            }

            // Handle any other unexpected exceptions
            return redirect()->route('profile-management')
                ->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }

    // Handle profile image update
    public function updateImage(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'profile_image.required' => 'You must upload a profile image.',
            'profile_image.image' => 'The uploaded file must be an image.',
            'profile_image.mimes' => 'Only jpeg, png, jpg, and gif files are allowed.',
            'profile_image.max' => 'The image must not be larger than 2MB.',
        ]);

        // Get the authenticated user
        $user = Auth::user();

        // Check if the request has a file
        if ($request->hasFile('profile_image')) {
            // Define the new file name based on user ID
            $fileName = 'profile-image-' . $user->id; // No extension here yet

            // Define the path where the file will be stored
            $filePath = public_path('profile/' . $user->id . '/');

            // Delete existing file if it exists (optional)
            // This ensures only one file with the same name exists, regardless of the format
            foreach (['jpg', 'jpeg', 'png', 'gif'] as $ext) {
                $existingFile = $filePath . $fileName . '.' . $ext;
                if (file_exists($existingFile)) {
                    unlink($existingFile); // Delete the existing file
                    break; // Stop after deleting the first existing file
                }
            }

            // Move the uploaded file to the designated directory with the desired name and keep the original extension
            $request->profile_image->move($filePath, $fileName . '.' . $request->profile_image->extension());
        }

        // Redirect back with a success message
        return redirect()->route('profile-management')->with('success', 'Profile image updated successfully.');
    }



    public function changePassword(Request $request)
    {
        // Validate inputs
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        // Check current password
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return redirect()->route('profile-management')->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        // Update password
        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('profile-management')->with('success', 'Password changed successfully.');
    }
}
