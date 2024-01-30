<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthMobileOtp extends Model
{
    use HasFactory;

    protected $connection = 'common';

    protected $fillable = [
        'company_id',
        'country_code',
        'mobile',
        'email',
        'otp',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
