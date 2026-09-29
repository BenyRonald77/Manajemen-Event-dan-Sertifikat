<?php

namespace App\Models;

use App\Enums\CertificateStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Certificate extends Model
{
    /** @use HasFactory<\Database\Factories\CertificateFactory> */
    use HasFactory;

    protected $fillable = [
        'registration_id',
        'certificate_number',
        'verification_token',
        'file_path',
        'status',
        'generated_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => CertificateStatus::class,
            'generated_at' => 'datetime',
        ];
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    /**
     * Nomor sertifikat manusiawi, contoh CERT-2026-000123. Unik, boleh
     * dicetak/dipamerkan (beda dari verification_token, lihat PRD.md).
     */
    public static function generateUniqueNumber(): string
    {
        $year = now()->year;

        do {
            $sequence = random_int(1, 999999);
            $number = sprintf('CERT-%d-%06d', $year, $sequence);
        } while (self::where('certificate_number', $number)->exists());

        return $number;
    }

    /**
     * Token verifikasi 32 karakter yang dipakai di URL publik. Sengaja acak
     * dan tidak berkaitan dengan certificate_number.
     */
    public static function generateUniqueToken(): string
    {
        do {
            $token = Str::random(32);
        } while (self::where('verification_token', $token)->exists());

        return $token;
    }

    public function isGenerated(): bool
    {
        return $this->status === CertificateStatus::Generated;
    }

    public function fileExists(): bool
    {
        return $this->file_path !== null && Storage::disk('local')->exists($this->file_path);
    }
}
