<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadDesign extends Model
{
    use HasFactory;

    protected $table = 'lead_designs';
    protected $primaryKey = 'iLeadDesignId';

    protected $fillable = [
        'iLeadId',
        'strFilename',
        'strTitle',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'iLeadId', 'iLeadId');
    }
}
