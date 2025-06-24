<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Bidder extends Model
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'type',
        'full_name',
        'email_address',
        'phone_number',
        'country',
        'password',

        // Company
        'company_name',
        'registration_number',
        'director_name',
        'director_email',
        'director_phone',
        'certificate_of_incorporation',
        'certificate_of_incorporation_status',
        'valid_trade_license',
        'valid_trade_license_status',
        'passport_copy_authorised',
        'passport_copy_authorised_status',
        'ubo_declaration',
        'ubo_declaration_status',

        // Individual
        'passport_copy',
        'passport_copy_status',
        'proof_of_address',
        'proof_of_address_status',
        'kyc_status',
        'account_status',

        'vip_bidding',
    ];
}
