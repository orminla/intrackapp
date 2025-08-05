<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectorChangeRequest extends Model
{
    use HasFactory;

    protected $table = 'inspector_change_requests';

    protected $fillable = [
        'schedule_id',
        'reason',
        'old_inspector_id',
        'new_inspector_id',
        'requested_date',
        'status',
    ];

    protected $casts = [
        'requested_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    // Relasi ke jadwal inspeksi
    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id', 'schedule_id');
    }

    // Relasi ke petugas lama
    public function oldInspector()
    {
        return $this->belongsTo(Inspector::class, 'old_inspector_id', 'inspector_id');
    }

    // Relasi ke petugas baru
    public function newInspector()
    {
        return $this->belongsTo(Inspector::class, 'new_inspector_id', 'inspector_id');
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }
}
