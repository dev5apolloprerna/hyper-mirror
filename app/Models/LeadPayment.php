<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadPayment extends Model
{
    use HasFactory;

    protected $table = 'lead_payments';
    protected $primaryKey = 'iLeadPaymentId';

    protected $fillable = [
        'iLeadId',
        'iPaidAmount',
        'iDiscountAmount',
        'PaymentDate',
        'PaymentMode',
        'iUserID',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'iLeadId', 'iLeadId');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'iUserID', 'id');
    }
}