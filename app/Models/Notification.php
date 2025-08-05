<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';
    protected $primaryKey = 'notif_id';

    protected $fillable = [
        'users_id',
        'title',
        'notifType',
        'message',
        'ref_id',
        'is_read'
    ];

    /** Relasi ke User (pengguna yang menerima notifikasi) */
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    /** Tandai notifikasi sebagai telah dibaca */
    public function bacaNotifikasi()
    {
        $this->is_read = true;
        $this->save();
    }

    /*Kirim notifikasi */
    public static function kirimNotifikasi(array $data)
    {
        return self::create($data);
    }
}
