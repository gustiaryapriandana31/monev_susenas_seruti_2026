<?php

namespace App\Enums;

enum R203Status: string
{
    case LENGKAP = '1';
    case TIDAK_LENGKAP = '2';
    case TIDAK_ADA_ART = '3';
    case MENOLAK = '4';
    case RUTA_PINDAH = '5';

    /**
     * Get the human-readable label for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::LENGKAP => 'Terisi Lengkap',
            self::TIDAK_LENGKAP => 'Terisi tdk lengkap',
            self::TIDAK_ADA_ART => 'Tidak ada ART/responden yang memberikan informasi sampai akhir masa pencacahan',
            self::MENOLAK => 'menolak',
            self::RUTA_PINDAH => 'Ruta pindah',
        };
    }

    /**
     * Get all options as an associative array (value => label).
     */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }
        return $options;
    }
}
