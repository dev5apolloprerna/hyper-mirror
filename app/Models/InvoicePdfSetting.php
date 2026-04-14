<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoicePdfSetting extends Model
{
    protected $table = 'invoice_pdf_settings';

    protected $fillable = [
        'terms_and_conditions',
        'bank_details',
    ];
}
