<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplainMaster extends Model
{
    use HasFactory;

    protected $table = 'complain_master';

    protected $primaryKey = 'complain_id';

    protected $fillable = [
        'irole_id',
        'user_id',
        'name',
        'email',
        'phone',
        'address',
        'comment',
        'invoice_no',
        'iShowroomId',
        'status',
        'resolve_user_id',
        'resolve_comment',
        'resolve_date',
        'payment_type',
        'amount',
        'iStatus',
        'isDelete',
    ];

    protected $casts = [
        'resolve_date' => 'datetime',
        'amount' => 'decimal:2',
        'iStatus' => 'integer',
        'isDelete' => 'integer',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolve_user_id');
    }
    public function showroom()
    {
        return $this->belongsTo(Showroom::class, 'iShowroomId', 'iShowroomId');
    }
}
