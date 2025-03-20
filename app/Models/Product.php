<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products'; // Specify the table name if it's not the plural of the model name

    protected $fillable = [
        'productName',
        'imagePath',
        'deviceId',
        'categoryId',
        'suitableTemp',
        'suitableHumidity',
        'status',
        'goodScore',
        'averageScore',
        'badScore',
        'userId', // Add userId if it's part of the table
        'first_out_of_range_temperature',
        'first_out_of_range_humidity',
        'cumulative_duration_out_of_range_temperature',
        'cumulative_duration_out_of_range_humidity',
        'longest_duration_humidity_check',
        'longest_duration_temperature_check',
        'humidity_check',
        'temperature_check',
    ];
    // Set the primary key to sdeviceId
    protected $primaryKey = 'id';
    
    // Define the relationship with Device
    public function device()
    {
        return $this->belongsTo(EnvironmentSensor::class, 'deviceId'); // Adjust the foreign key if needed
    }

    // Define relationships, for example, if a product belongs to a category
    public function category()
    {
        return $this->belongsTo(Category::class, 'categoryId');
    }

    // Define relationship to User if necessary
    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
}


