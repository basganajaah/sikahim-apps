<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notification';

    protected $primaryKey = 'notification_id';

    protected $fillable = ['title', 'message', 'sent_at', 'is_read'];

    protected $casts = [
        'sent_at' => 'datetime',
        'is_read' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'mendapat_notifikasi', 'notification_id', 'id');
    }
}
