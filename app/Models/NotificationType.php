<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationType extends Model
{
    use HasFactory;

    protected $table = 'notification_types'; // Define the table name

    protected $fillable = [
        'type',
        'content',
    ];

    // Relationship with notifications
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'notification_type_id');
    }
}
