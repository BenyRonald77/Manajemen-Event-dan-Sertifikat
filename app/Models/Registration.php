<?php

namespace App\Models;

use App\Enums\RegistrationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Registration extends Model
{
    /** @use HasFactory<\Database\Factories\RegistrationFactory> */
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'email',
        'phone',
        'registration_code',
        'qr_payload',
        'status',
        'checked_in_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => RegistrationStatus::class,
            'checked_in_at' => 'datetime',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function certificate(): HasOne
    {
        return $this->hasOne(Certificate::class);
    }

    public function isCheckedIn(): bool
    {
        return $this->status === RegistrationStatus::CheckedIn;
    }

    /**
     * Buat kode pendaftaran acak 8 karakter (huruf besar + angka) yang unik
     * di seluruh tabel registrations. qr_payload memakai kode yang sama
     * (lihat catatan desain di PRD.md).
     */
    public static function generateUniqueCode(): string
    {
        do {
            $code = Str::upper(Str::random(8));
        } while (self::where('registration_code', $code)->exists());

        return $code;
    }
}
