<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inspector extends Model
{
    use HasFactory;

    protected $table = 'inspectors';
    protected $primaryKey = 'inspector_id';

    protected $fillable = [
        'nip',
        'name',
        'phone_num',
        'portfolio_id',
        'gender',
        'users_id'
    ];

    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class, 'portfolio_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'inspector_id', 'inspector_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    public function certifications()
    {
        return $this->hasMany(Certification::class, 'inspector_id', 'inspector_id');
    }
}
