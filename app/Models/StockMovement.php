<?php

namespace App\Models;

use App\Enums\StockMovementType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $table = 'stock_movements';
    protected $primaryKey = 'iMovementId';

    protected $fillable = [
        'iProductId',
        'iShowroomId',
        'iRelatedShowroomId',
        'strType',
        'iQuantity',
        'iBalanceAfter',
        'strReason',
        'iReferenceId',
        'strReferenceType',
        'iCreatedBy',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'iProductId', 'iProductId');
    }

    public function showroom()
    {
        return $this->belongsTo(Showroom::class, 'iShowroomId', 'iShowroomId');
    }

    public function relatedShowroom()
    {
        return $this->belongsTo(Showroom::class, 'iRelatedShowroomId', 'iShowroomId');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'iCreatedBy', 'id');
    }

    public function getLabelAttribute(): string
    {
        return StockMovementType::label($this->strType);
    }

    public function getBadgeClassAttribute(): string
    {
        return StockMovementType::badgeClass($this->strType);
    }
}
