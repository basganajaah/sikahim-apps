<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pendaftaran extends Model
{
    protected $table = 'pendaftaran';

    protected $primaryKey = 'registration_id';

    protected $fillable = [
        'id',
        'activity_id',
        'registrant_name',
        'age',
        'gender',
        'registration_date',
        'registration_status',
        'notes',
    ];

    protected $casts = [
        'registration_date' => 'date',
        'gender' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'activity_id', 'activity_id');
    }
}
