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
        'status',
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
}
