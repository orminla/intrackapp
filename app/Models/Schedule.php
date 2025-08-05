<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Schedule extends Model
{
    use HasFactory;

    protected $primaryKey = 'schedule_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'inspector_id',
        'partner_id',
        'started_date',
        'product_id',
        'status'
    ];
    protected $casts = [
        'started_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function inspector()
    {
        return $this->belongsTo(Inspector::class, 'inspector_id', 'inspector_id');
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id', 'partner_id');
    }

    public function scheduleLogs()
    {
        return $this->hasMany(ScheduleLog::class, 'schedule_id', 'schedule_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function report()
    {
        return $this->hasOne(Report::class, 'schedule_id', 'schedule_id');
    }

    public function selectedDetails()
    {
        return $this->belongsToMany(DetailProduct::class, 'schedule_details', 'schedule_id', 'detail_id');
    }
}
