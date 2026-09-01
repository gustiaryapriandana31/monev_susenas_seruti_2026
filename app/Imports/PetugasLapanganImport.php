<?php

namespace App\Imports;

use App\Models\PetugasLapangan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class PetugasLapanganImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    /**
     * Baris pertama Excel adalah header.
     */
    public function headingRow(): int
    {
        return 1;
    }

    /**
     * Ambil nilai berdasarkan beberapa kemungkinan nama kolom.
     */
    private function getValue(array $row, array $keys)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $row)) {
                return $row[$key];
            }
        }

        // Antisipasi spasi / underscore / tanda hubung
        foreach ($row as $rowKey => $value) {
            $cleanRowKey = str_replace(
                ['_', ' ', '-', '.'],
                '',
                strtolower(trim((string) $rowKey))
            );

            foreach ($keys as $key) {
                $cleanKey = str_replace(
                    ['_', ' ', '-', '.'],
                    '',
                    strtolower(trim((string) $key))
                );

                if ($cleanRowKey === $cleanKey) {
                    return $value;
                }
            }
        }

        return null;
    }

    /**
     * Excel:
     * 1 = Pencacah
     * 2 = Pengawas
     */
    private function getJabatan($status): ?string
    {
        $status = trim((string) $status);

        return match ($status) {
            '1' => 'Pencacah (PPL)',
            '2' => 'Pengawas (PML)',
            default => null,
        };
    }

    /**
     * Import / update data petugas.
     */
    public function model(array $row)
    {
        $kodePetugas = $this->getValue($row, [
            'kode_petugas',
            'kode',
        ]);

        $provinsi = $this->getValue($row, [
            'provinsi',
            'prop',
        ]);

        $kabupaten = $this->getValue($row, [
            'kabupaten',
            'kab',
        ]);

        $namaPetugas = $this->getValue($row, [
            'nama_petugas',
            'nama',
        ]);

        $noHp = $this->getValue($row, [
            'no_hp',
            'no hp',
            'nohp',
        ]);

        $status = $this->getValue($row, [
            'status',
        ]);

        /*
         * Lewati baris yang bukan data petugas.
         * Ini juga membuat baris keterangan di Excel tidak masuk database.
         */
        if (empty($kodePetugas) || empty($namaPetugas)) {
            return null;
        }

        $jabatan = $this->getJabatan($status);

        /*
         * Kalau status bukan 1 atau 2, abaikan baris tersebut.
         */
        if ($jabatan === null) {
            return null;
        }

        /*
         * Jika kode_petugas sudah ada:
         *     UPDATE data lama
         *
         * Jika belum ada:
         *     INSERT data baru
         */
        return PetugasLapangan::updateOrCreate(
            [
                'kode_petugas' => trim((string) $kodePetugas),
            ],
            [
                'provinsi' => $provinsi,
                'kabupaten' => $kabupaten,
                'nama_petugas' => trim((string) $namaPetugas),
                'no_hp' => $noHp,
                'kode_jabatan' => $status,
                'jabatan' => $jabatan,
                'status' => $status,
            ]
        );
    }
}