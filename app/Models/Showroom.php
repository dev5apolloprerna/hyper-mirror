<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Showroom extends Model
{
    use HasFactory;

    protected $table = 'showrooms';
    protected $primaryKey = 'iShowroomId';

    protected $fillable = [
        'strShowRoomName',
    ];

    public function stocks()
    {
        return $this->hasMany(ProductStock::class, 'iShowroomId', 'iShowroomId');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'iShowroomId', 'iShowroomId');
    }
}