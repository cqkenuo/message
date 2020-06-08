<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    //

    protected $fillable = [
        'user_id',
        'record_id',
        'status',
        'reason',
        'manager_back'

    ];
}
