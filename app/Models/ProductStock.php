<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    use HasFactory;

    protected $table = 'product_stocks';
    protected $primaryKey = 'iStockId';

    protected $fillable = [
        'iProductId',
        'iShowroomId',
        'iQuantity',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'iProductId', 'iProductId');
    }

    public function showroom()
    {
        return $this->belongsTo(Showroom::class, 'iShowroomId', 'iShowroomId');
    }
}
