<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $table = 'leads';
    protected $primaryKey = 'iLeadId';

    protected $fillable = [
        'iCustomerId',
        'iCurrentYearLeadId',
        'strLeadNo',
        'IsMeasureMentRequired',
        'MeasurementVisitDate',
        'SiteAddress',
        'CreatedDate',
        'iCurrentLeadStatus',
        'NetFollowupdate',
        'expected_delivery_date',
        'isFittingLeadOnly',
        'isFittingRequired',
        'isFittingChargeIncluded',
        'iFittingCharges',
        'isDiscountApplicable',
        'decDiscountAmount',
        'isGstApplicable',
        'decGstAmount',
        'iLeadAmount',
        'iQuotationId',
        'iCreatedBy',
        'iShowroomId',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'iCustomerId', 'iCustomerId');
    }

    public function showroom()
    {
        return $this->belongsTo(Showroom::class, 'iShowroomId', 'iShowroomId');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'iCreatedBy', 'id');
    }

    public function quotation()
    {
        return $this->belongsTo(LeadQuotation::class, 'iQuotationId', 'iQuotationId');
    }
    public function quotations()
    {
        return $this->hasMany(LeadQuotation::class, 'iLeadId', 'iLeadId');
    }
    public function designs()
    {
        return $this->hasMany(LeadDesign::class, 'iLeadId', 'iLeadId');
    }
    public function histories()
    {
        return $this->hasMany(LeadHistory::class, 'iLeadId', 'iLeadId');
    }
    public function payments()
    {
        return $this->hasMany(LeadPayment::class, 'iLeadId', 'iLeadId');
    }

}
