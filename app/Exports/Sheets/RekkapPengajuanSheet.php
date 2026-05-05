<?php

namespace App\Exports\Sheets;

use App\Models\PengajuanRapbs;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RekkapPengajuanSheet implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithStyles
{
    private int $rowNo = 0;

    public function __construct(
        private ?string $status = null,
        private ?int $tahunAjaranId = null,
    ) {}

    public function query(): Builder
    {
        $query = PengajuanRapbs::query()
            ->with(['user.unitKerja.departemen', 'user.jabatan', 'tahunAjaran'])
            ->withSum(
                ['items as total_anggaran' => fn ($q) => $q->where('status', '!=', 'ditolak')],
                'total_harga'
            )
            ->withSum(
                ['items as total_disetujui' => fn ($q) => $q->where('status', 'disetujui')],
                'total_harga'
            )
            ->withCount('items')
            ->withCount(['items as items_disetujui_count' => fn ($q) => $q->where('status', 'disetujui')])
            ->withCount(['items as items_ditolak_count' => fn ($q) => $q->where('status', 'ditolak')])
            ->orderBy('created_at', 'desc');

        if ($this->status) {
            $query->where('status', $this->status);
        }
        if ($this->tahunAjaranId) {
            $query->where('tahun_ajaran_id', $this->tahunAjaranId);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Pengajuan',
            'NIP',
            'Nama Pegawai',
            'Jabatan',
            'Unit Kerja',
            'Departemen',
            'Tahun Ajaran',
            'Status',
            'Jml Item',
            'Item Disetujui',
            'Item Ditolak',
            'Total Anggaran (non-ditolak)',
            'Total Disetujui',
            'Tgl Pengajuan',
            'Catatan',
        ];
    }

    /** @param PengajuanRapbs $row */
    public function map($row): array
    {
        $this->rowNo++;

        return [
            $this->rowNo,
            $row->kode_pengajuan,
            $row->user?->nip ?? '-',
            $row->user?->name ?? '-',
            $row->user?->jabatan?->nama ?? '-',
            $row->user?->unitKerja?->nama ?? '-',
            $row->user?->unitKerja?->departemen?->nama ?? '-',
            $row->tahunAjaran?->nama ?? '-',
            match ($row->status) {
                'draft'     => 'Draft',
                'diajukan'  => 'Diajukan',
                'direvisi'  => 'Perlu Revisi',
                'disetujui' => 'Disetujui',
                'ditolak'   => 'Ditolak',
                default     => $row->status,
            },
            $row->items_count ?? 0,
            $row->items_disetujui_count ?? 0,
            $row->items_ditolak_count ?? 0,
            $row->total_anggaran ?? 0,
            $row->total_disetujui ?? 0,
            $row->tanggal_pengajuan?->format('d/m/Y H:i') ?? '-',
            $row->catatan ?? '-',
        ];
    }

    public function title(): string
    {
        return 'Rekap Pengajuan';
    }

    public function styles(Worksheet $sheet): array
    {
        $lastCol = 'P';
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'AAAAAA']]],
        ]);

        $highestRow = $sheet->getHighestRow();
        if ($highestRow > 1) {
            // Format currency columns: M (total_anggaran) and N (total_disetujui)
            $sheet->getStyle("M2:N{$highestRow}")->getNumberFormat()->setFormatCode('#,##0');
            // Borders on data rows
            $sheet->getStyle("A2:{$lastCol}{$highestRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
        }

        return [];
    }
}
