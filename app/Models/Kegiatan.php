<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    use HasFactory;

    protected $table = 'kegiatan';

    protected $primaryKey = 'activity_id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'thumbnail',
        'activity_name',
        'penyelenggara',
        'category_id',
        'datetime_started',
        'datetime_finished',
        'max_participant',
        'location',
        'description',
        'materi',
        'status',
        'verification',
        'rejection_reason',
    ];

    protected $casts = [
        'datetime_started' => 'datetime',
        'datetime_finished' => 'datetime',
        'materi' => 'array',
    ];

    public function penyelenggaraUser()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function pendaftaran()
    {
        return $this->hasMany(Pendaftaran::class, 'activity_id', 'activity_id');
    }

    public function evaluasi()
    {
        return $this->hasMany(Evaluasi::class, 'activity_id', 'activity_id');
    }

    public function getRemainingSlotsAttribute(): ?int
    {
        if ($this->max_participant === null) {
            return null;
        }

        $activeCount = $this->relationLoaded('pendaftaran')
            ? $this->pendaftaran->whereNotIn('registration_status', ['Dibatalkan', 'Ditolak'])->count()
            : ($this->pendaftaran_count ?? $this->pendaftaran()->whereNotIn('registration_status', ['Dibatalkan', 'Ditolak'])->count());

        return max(0, $this->max_participant - $activeCount);
    }

    public function getGoogleCalendarUrlAttribute(): string
    {
        $start = $this->datetime_started?->copy()->utc()->format('Ymd\THis\Z') ?? '';
        $end = $this->datetime_finished?->copy()->utc()->format('Ymd\THis\Z') ?? '';

        return 'https://calendar.google.com/calendar/render?'.http_build_query([
            'action' => 'TEMPLATE',
            'text' => $this->activity_name,
            'dates' => "{$start}/{$end}",
            'details' => $this->description ?? '',
            'location' => $this->location ?? '',
        ]);
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'category_id', 'category_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'memiliki_tag', 'activity_id', 'tag_id');
    }
}
