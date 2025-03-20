<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications'; // Define the table name

    protected $fillable = [
        'notification_type_id',
        'product_id',
        'device_id',
        'resent_at',
        'seen_at',
    ];

    // Relationship with NotificationType
    public function notificationType()
    {
        return $this->belongsTo(NotificationType::class, 'notification_type_id');
    }

    // Relationship with Product
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
