<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'product_category'; // Use the actual table name if different

    protected $fillable = [
        'categoryName',
    ];

    // Define the relationship with products
    public function products()  
    {
        return $this->hasMany(Product::class, 'categoryId');
    }
}
