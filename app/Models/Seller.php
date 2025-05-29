<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Seller extends Model
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'type',
        'full_name',
        'email_address',
        'phone_number',
        'country',
        'password',
        'source_of_goods',
        'company_name',
        'registration_number',
        'director_name',
        'director_email',
        'director_phone',
        'certificate_of_incorporation',
        'valid_trade_license',
        'passport_copy_authorised',
        'ubo_declaration',
        'passport_copy',
        'proof_of_ownership',
        'kyc_status',
        'account_status',
    ];
}
