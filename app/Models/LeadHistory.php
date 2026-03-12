<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadHistory extends Model
{
    use HasFactory;

    protected $table = 'lead_histories';
    protected $primaryKey = 'id';

    protected $fillable = [
        'iLeadId',
        'strComments',
        'NetFolloupwdate',
        'iStatus',
        'iEnterBy',
        'EntryDate',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'iLeadId', 'iLeadId');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'iEnterBy', 'id');
    }
}