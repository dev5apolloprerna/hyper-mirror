<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table      = 'invoices';
    protected $primaryKey = 'iInvoiceId';

    protected $fillable = [
        'strInvoiceNo',
        'iShowroomId',
        'iCreatedBy',
        'InvoiceDate',
        'strNotes',
        'customer_name',
        'customer_mobile',
        'customer_address',
        'status',
        'payment_mode',
        'payment_received',
    ];

    protected $casts = [
        'payment_received' => 'boolean',
    ];

    public function showroom()
    {
        return $this->belongsTo(Showroom::class, 'iShowroomId', 'iShowroomId');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'iCreatedBy', 'id');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'iInvoiceId', 'iInvoiceId');
    }

    public function getTotalAmountAttribute(): float
    {
        return (float) $this->items->sum('iAmount');
    }
    public function getUnpaidAmountAttribute(): float
    {
        return $this->payment_received ? 0.0 : $this->total_amount;
    }
}