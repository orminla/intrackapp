<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Portfolio extends Model
{
    use HasFactory;

    protected $table = 'portfolios';
    protected $primaryKey = 'portfolio_id';

    protected $fillable = [
        'department_id',
        'name'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function inspectors()
    {
        return $this->hasMany(Inspector::class, 'portfolio_id');
    }
}
