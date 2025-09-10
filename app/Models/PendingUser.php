<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PendingUser extends Model
{
    use HasFactory;

    protected $table = 'pending_users';

    protected $fillable = [
        'name',
        'gender',
        'email',
        'phone_num',
        'role',
        'nip',
        'portfolio_id',
        'password_plain',
        'verif_token',
        'expired_at',
        'verified_at',
    ];

    protected $dates = ['expired_at', 'verified_at'];
}
