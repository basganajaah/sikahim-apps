<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluasi extends Model
{
    protected $table = 'evaluasi';

    protected $primaryKey = 'evaluation_id';

    protected $fillable = ['id', 'activity_id', 'rating', 'review'];

    protected $casts = ['rating' => 'float'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'activity_id', 'activity_id');
    }
}
