<?php

namespace App\Models;

use App\Models\CrmRole;
use App\Models\Showroom;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'id', 'strUserName', 'strUserMobile', 'strUserAddress', 'iRoalId',
        'first_name', 'last_name', 'email', 'mobile_number',
        'email_verified_at', 'password', 'role_id',
        'otp', 'status', 'remember_token', 'device_token',
        'can_view_financial',
        'created_at', 'updated_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at'  => 'datetime',
        'can_view_financial' => 'boolean',
    ];

    public function crmRole()
    {
        return $this->belongsTo(CrmRole::class, 'iRoalId', 'iRoleId');
    }

    public function userShowrooms()
    {
        return $this->hasMany(UserShowroom::class, 'UserId', 'id');
    }

    public function getFullNameAttribute(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }

    public function showrooms()
    {
        return $this->belongsToMany(
            Showroom::class,
            'user_showrooms',
            'UserId',
            'ShowRoomId',
            'id',
            'iShowroomId'
        );
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
