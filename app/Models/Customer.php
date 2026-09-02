<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';
    protected $primaryKey = 'iCustomerId';

    protected $fillable = [
        'strCustomer',
        'strMobile',
        'strAddress',
        'customer_type',
        'company_name',
        'gst_no',
    ];
}
