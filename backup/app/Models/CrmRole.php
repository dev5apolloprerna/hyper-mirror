<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrmRole extends Model
{
    protected $primaryKey = 'iRoleId';

    protected $fillable = ['strRole', 'slug'];
}
