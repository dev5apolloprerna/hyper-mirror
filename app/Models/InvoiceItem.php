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
        'item_remark',
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
}