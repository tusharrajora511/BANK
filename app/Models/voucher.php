<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class voucher extends Model
{
    protected $table = 'voucher';
    protected $fillable = [
        'employee_id',
        'voucher_code',
        'voucher_type',
    ];
}
