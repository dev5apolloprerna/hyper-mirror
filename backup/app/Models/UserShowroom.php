<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserShowroom extends Model
{
    use HasFactory;

    protected $table = 'user_showrooms';
    protected $primaryKey = 'UserShowRoomId';

    protected $fillable = [
        'UserId',
        'ShowRoomId',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'UserId', 'id');
    }

    public function showroom()
    {
        return $this->belongsTo(Showroom::class, 'ShowRoomId', 'iShowroomId');
    }
}
