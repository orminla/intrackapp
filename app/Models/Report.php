<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    use HasFactory;

    protected $table = 'reports';
    protected $primaryKey = 'report_id';

    protected $fillable = [
        'schedule_id',
        'finished_date',
        'postponed_date',
        'postponed_reason',
        'rejection_reason',
        'status'
    ];

    protected $casts = [
        'finished_date' => 'date',
        'postponed_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id', 'schedule_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'report_id', 'report_id');
    }
}
