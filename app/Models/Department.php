<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
    use HasFactory;
    protected $table = 'departments';
    protected $primaryKey = 'department_id';

    protected $fillable = [
        'name',
    ];

    public function portfolios()
    {
        return $this->hasMany(Portfolio::class, 'department_id', 'department_id');
    }
}
