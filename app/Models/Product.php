<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $primaryKey = 'iProductId';

    protected $fillable = [
        'iCategoryId',
        'strProductName',
        'MRP',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'iCategoryId', 'iCategoryId');
    }
     public function stocks()
    {
        return $this->hasMany(ProductStock::class, 'iProductId', 'iProductId');
    }
}