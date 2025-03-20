<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EnvironmentDataController;
use App\Http\Controllers\Api\EnvironmentDeviceLogController;
use App\Http\Controllers\Api\getSensorData;
use App\Http\Controllers\Api\SpoiledgeDataController;
use App\Http\Controllers\Api\StartReading;
use App\Http\Controllers\Api\ProductConditionController;



Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/update-data', [EnvironmentDataController::class, 'store']);
Route::post('/receive-log/{deviceId}', [EnvironmentDeviceLogController::class, 'receiveLog']);
Route::get('device/{deviceId}/status', [EnvironmentDeviceLogController::class, 'checkConnectionStatus']);
Route::post('/sensor-data', [SpoiledgeDataController::class, 'store']);
Route::post('/sensor/start', [StartReading::class,'start'])->name('sensor.start');
Route::post('/sensor/stop', [StartReading::class,'stop'])->name('sensor.stop');
Route::post('/result', [ProductConditionController::class, 'storeOrUpdate']);
Route::get('/environment-sensor-data', [getSensorData::class, 'getSensorData']);
Route::get('spoiledgeDevice/{deviceId}/status', [SpoiledgeDataController::class, 'checkConnectionStatus']);

