<?php

namespace App\Enums;

enum RegistrationStatus: string
{
    case Registered = 'registered';
    case CheckedIn = 'checked_in';

    public function label(): string
    {
        return match ($this) {
            self::Registered => 'Terdaftar',
            self::CheckedIn => 'Sudah Check-in',
        };
    }
}
