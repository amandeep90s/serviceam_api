<?php

namespace App\Models\Common;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuthMobileOtp extends BaseModel
{
    use HasFactory;

    protected $connection = 'common';

    protected $fillable = [
        'company_id', 'country_code', 'mobile', 'email', 'otp'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];
}
