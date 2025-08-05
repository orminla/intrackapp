<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Partner extends Model
{
    use HasFactory;

    protected $table = 'partners';
    protected $primaryKey = 'partner_id';

    protected $fillable = [
        'name',
        'address'
    ];
}
