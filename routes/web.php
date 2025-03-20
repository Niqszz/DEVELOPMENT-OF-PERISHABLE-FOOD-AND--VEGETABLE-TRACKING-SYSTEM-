<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController; // Import the controller
use App\Http\Controllers\pageNavigator;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DeviceDataController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EnvironmentSensorController;



//Navigate page
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [pageNavigator::class, 'dashboard'])->name('dashboard');
    Route::get('/profile-management', [pageNavigator::class, 'profileManagement'])->name('profile-management');
    Route::get('/environment-monitoring', [pageNavigator::class, 'environmentMonitoring'])->name('environment-monitoring');
    Route::get('/spoiledge-detector', [pageNavigator::class, 'spoiledgeDetector'])->name('spoiledge-detector');
    Route::get('/product-management', [pageNavigator::class, 'productManagement'])->name('product-management');
    Route::get('/notification', [pageNavigator::class, 'notification'])->name('notification');
    Route::get('/report', [pageNavigator::class, 'report'])->name('report');

    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/delete', [ProductController::class, 'delete'])->name('products.delete');
    Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');


    Route::post('/environment-sensors', [EnvironmentSensorController::class, 'store'])->name('environment-sensors.store');
    Route::get('/environment-sensors/overview', [EnvironmentSensorController::class, 'getOverviewData']);
    Route::post('/environment-sensors/update', [EnvironmentSensorController::class, 'update'])->name('environment-sensors.update');
    Route::post('/environment-sensors/disconnect', [EnvironmentSensorController::class, 'disconnect'])->name('environment-sensors.disconnect');

    Route::post('/spoiledge-sensors', [DeviceController::class, 'store'])->name('spoiledge-sensors.store');
    Route::post('/startSensor', [DeviceController::class, 'startSensor'])->name('spoiledge-sensors.startSensor');

    Route::post('/disconnect-device', [DeviceController::class, 'disconnectDevice'])->name('spoiledge-sensors.device');
    Route::get('/check-device-status', [DeviceController::class, 'checkDeviceStatus'])->name('spoiledge-sensors.checkDeviceStatus');
    Route::get('/product-condition/{id}', [ProductController::class, 'getProductCondition']);


    Route::post('/update-device-data', [DeviceDataController::class, 'update']);


    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/image', [ProfileController::class, 'updateImage'])->name('profile.updateImage');
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.changePassword');

});


Route::get('/current-datetime-html', [DashboardController::class, 'getCurrentDateTimeHtml']);


require __DIR__.'/auth.php';
