<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    use HasFactory;

    protected $table = 'product_stocks';
    protected $primaryKey = 'iProductStockId';

    protected $fillable = [
        'iProductId',
        'iShowroomId',
        'inside_quantity',
        'showroom_quantity',
        'minimum_quantity',
        'remarks',
    ];

    protected $casts = [
        'inside_quantity' => 'integer',
        'showroom_quantity' => 'integer',
        'minimum_quantity' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'iProductId', 'iProductId');
    }

    public function showroom()
    {
        return $this->belongsTo(Showroom::class, 'iShowroomId', 'iShowroomId');
    }

    public function getTotalStockAttribute(): int
    {
        return (int) $this->inside_quantity + (int) $this->showroom_quantity;
    }
}
