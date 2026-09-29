<?php

namespace App\Enums;

enum CertificateStatus: string
{
    case Pending = 'pending';
    case Generated = 'generated';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Sedang diproses',
            self::Generated => 'Selesai',
            self::Failed => 'Gagal',
        };
    }
}
