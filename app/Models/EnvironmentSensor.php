<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnvironmentSensor extends Model
{
    use HasFactory;

    protected $table = 'environment_sensor'; // Specify the table name

    protected $fillable = [
        'userId',
        'deviceName',
        'humidity',
        'temperature',
        'last_log_at',
        'isConnected', //store 1 if the device is connected to internet and 0 if disconnect from internet
    ];

    protected $primaryKey = 'deviceId';
    public $incrementing = false;

    // Define relationship to User if necessary
    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
    public function connectedProducts()
    {
        return $this->hasMany(Product::class, 'deviceId', 'deviceId');
    }
}
