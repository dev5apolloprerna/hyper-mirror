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
}