<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $table      = 'invoice_items';
    protected $primaryKey = 'iItemId';

    protected $fillable = [
        'iInvoiceId',
        'iCategoryId',
        'iProductId',
        'quantity',
        'unit_price',
        'iAmount',
        'width',
        'height',
        'item_remark',
        'unit_of_measurement',
        'calculation_multiple',
        'shape_id',
        'feature_id',
        'decRatePerSqft',
        'decTotalSqft',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'iInvoiceId', 'iInvoiceId');
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'iCategoryId', 'iCategoryId');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'iProductId', 'iProductId');
    }
     public function shape()
    {
        return $this->belongsTo(ProductShape::class, 'shape_id', 'shape_id');
    }

    public function feature()
    {
        return $this->belongsTo(ProductFeature::class, 'feature_id', 'feature_id');
    }
}
