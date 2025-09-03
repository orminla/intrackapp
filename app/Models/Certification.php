<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Certification extends Model
{
    use HasFactory;

    protected $table = 'certifications';
    protected $primaryKey = 'certification_id';

    protected $fillable = [
        'inspector_id',
        'portfolio_id',
        'name',
        'issuer',
        'original_name',
        'file_path',
        'issued_at',
        'expired_at',
    ];

    public function inspector()
    {
        return $this->belongsTo(Inspector::class, 'inspector_id', 'inspector_id');
    }

    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class, 'portfolio_id', 'portfolio_id');
    }
}
