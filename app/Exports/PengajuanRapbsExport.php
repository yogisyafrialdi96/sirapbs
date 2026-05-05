<?php

namespace App\Exports;

use App\Exports\Sheets\DetailItemSheet;
use App\Exports\Sheets\RekkapPengajuanSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PengajuanRapbsExport implements WithMultipleSheets
{
    public function __construct(
        private ?string $status = null,
        private ?int $tahunAjaranId = null,
    ) {}

    public function sheets(): array
    {
        return [
            new RekkapPengajuanSheet($this->status, $this->tahunAjaranId),
            new DetailItemSheet($this->status, $this->tahunAjaranId),
        ];
    }
}

