<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadQuotation extends Model
{
    use HasFactory;

    protected $table = 'lead_quotations';
    protected $primaryKey = 'iQuotationId';

    protected $fillable = [
        'iLeadId',
        'iProductCategoryId',
        'iProductId',
        'unit_of_measurement',
        'shape_id',
        'feature_id',
        'remarks',
        'quantity',
        'decHeight',
        'decWidth',
        'decRatePerSqft',
        'decTotalSqft',
        'iAmount',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'iLeadId', 'iLeadId');
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'iProductCategoryId', 'iCategoryId');
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
