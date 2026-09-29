<?php

namespace App\Models;

use App\Enums\RegistrationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'location',
        'starts_at',
        'ends_at',
        'quota',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'quota' => 'integer',
        ];
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function checkedInRegistrations(): HasMany
    {
        return $this->registrations()->where('status', RegistrationStatus::CheckedIn);
    }

    /**
     * Apakah kuota event ini sudah penuh. Event tanpa kuota (null) tidak
     * pernah penuh.
     */
    public function isFull(): bool
    {
        if ($this->quota === null) {
            return false;
        }

        return $this->registrations()->count() >= $this->quota;
    }

    public function remainingQuota(): ?int
    {
        if ($this->quota === null) {
            return null;
        }

        return max(0, $this->quota - $this->registrations()->count());
    }
}
