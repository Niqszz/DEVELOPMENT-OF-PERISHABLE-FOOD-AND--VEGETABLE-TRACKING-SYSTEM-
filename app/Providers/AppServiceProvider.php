<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use App\Models\Product;
use App\Models\User;
use App\Models\EnvironmentSensor;
use App\Models\SpoiledgeReading;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $user = Auth::user();
            $view->with('user', $user);

            // Check if the user is authenticated
            if ($user) {
                // Fetch the user's devices
                $devices = EnvironmentSensor::where('userId', $user->id)->get();
                $view->with('devices', $devices);
		
		$spoiledgedevices = SpoiledgeReading::where('userId', $user->id)->get();
                $view->with('spoiledgedevices', $spoiledgedevices);

                // Fetch products specific to the authenticated user
                $products = Product::where('userId', $user->id)->with(['device', 'category'])->get();
		$view->with('products', $products);
		
		
		
		$productsWithoutDevice = Product::where('userId', $user->id)->get();
                $view->with('productsWithoutDevice', $productsWithoutDevice);
		
		            // Count products by stats
		$goodCount = Product::where('userId', $user->id)
			->where('status', 'good')
			->count();

		$averageCount = Product::where('userId', $user->id)
			->where('status', 'average')
			->count();

		$badCount = Product::where('userId', $user->id)
			->where('status', 'bad')
			->count();

		// Share counts with the view
		$view->with('goodCount', $goodCount);
		$view->with('averageCount', $averageCount);
		$view->with('badCount', $badCount);


                // Optionally, if you need all users (but this is typically not recommended)
                $view->with('users', User::all());
            } else {
                // Share empty collections for unauthenticated users
                $view->with('products', collect());
                $view->with('users', collect());
                $view->with('devices', collect()); // Share empty collection for devices
            }
        });
    }

}
