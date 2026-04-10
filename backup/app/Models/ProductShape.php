<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductShape extends Model
{
    use HasFactory;

    protected $table = 'product_shape';
    protected $primaryKey = 'shape_id';
    public $timestamps = false;

    protected $fillable = [
        'shape_title',
        'iStatus',
        'isDelete',
    ];
}