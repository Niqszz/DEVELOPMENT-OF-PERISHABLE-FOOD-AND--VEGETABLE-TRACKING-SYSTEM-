<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpoiledgeReading extends Model
{
    use HasFactory;

    // Specify the table name if it's not the default plural form of the model name
    protected $table = 'spoiledge_readings';

    // Define the columns that are mass assignable
    protected $fillable = [
        'userId', //To store user id, which mean it if there is userId, the device is connected to user
        'sdeviceId',//To store the device Id
        'methane_level_ppm',//to store methane level
        'product_id',
        'letConnect',//for user to decide if want to make the device keep connected even after log out
        'isConnected',//to set the deviCe connected or not TO INTERNET
	    'shouldStart',//Flag to start the reading or not
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    // Set the primary key to sdeviceId
    protected $primaryKey = 'sdeviceId';

    // Indicate that the primary key is not auto-incrementing
    public $incrementing = false;
    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class, 'userId','id');
    }
    //define relationship with product model
    public function product(){
        return $this->belongsTo(Product::class,'product_id','id');
    }
}
