<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    protected $table = 'password_reset_tokens';
    protected $primaryKey = 'token_id';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'token',
        'user_id',
        'expired_at',
        'is_used',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    // Relasi ke user (jika perlu)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
