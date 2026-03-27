<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductFeature extends Model
{
    use HasFactory;

    protected $table = 'product_feature';
    protected $primaryKey = 'feature_id';
    public $timestamps = false;

    protected $fillable = [
        'feature_name',
        'iStatus',
        'isDelete',
    ];
}