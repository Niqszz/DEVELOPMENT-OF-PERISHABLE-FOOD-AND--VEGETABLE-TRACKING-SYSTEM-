<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCondition extends Model
{
    use HasFactory;

    // Define the table name if not following the convention
    protected $table = 'product_conditions';

    // Define the fillable fields
    protected $fillable = [
        'product_id',
        'humidity',
        'temperature',
        'cumulative_duration_humidity',
        'cumulative_duration_temperature',
        'averageMethaneReading',
        'status',
    ];

    // Specify date attributes for automatic casting to Carbon instances
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    // Relationship with the Product model
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
