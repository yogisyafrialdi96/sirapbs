<?php

namespace App\Exports\Sheets;

use App\Models\ItemPengajuan;
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

class DetailItemSheet implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithStyles
{
    private int $rowNo = 0;

    public function __construct(
        private ?string $status = null,
        private ?int $tahunAjaranId = null,
    ) {}

    public function query(): Builder
    {
        return ItemPengajuan::query()
            ->with([
                'pengajuanRapbs.user.unitKerja.departemen',
                'pengajuanRapbs.user.jabatan',
                'pengajuanRapbs.tahunAjaran',
                'kategoriBelanjas',
            ])
            ->whereHas('pengajuanRapbs', function (Builder $q): void {
                if ($this->status) {
                    $q->where('status', $this->status);
                }
                if ($this->tahunAjaranId) {
                    $q->where('tahun_ajaran_id', $this->tahunAjaranId);
                }
            })
            ->orderBy('pengajuan_rapbs_id')
            ->orderBy('id');
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Pengajuan',
            'NIP',
            'Nama Pegawai',
            'Unit Kerja',
            'Departemen',
            'Tahun Ajaran',
            'Status Pengajuan',
            'Kategori Belanja',
            'Nama Item',
            'Deskripsi',
            'Spesifikasi',
            'Satuan',
            'Volume',
            'Harga Satuan (Rp)',
            'Total Harga (Rp)',
            'Status Item',
            'Catatan Reviewer',
        ];
    }

    /** @param ItemPengajuan $row */
    public function map($row): array
    {
        $this->rowNo++;
        $pengajuan = $row->pengajuanRapbs;

        return [
            $this->rowNo,
            $pengajuan?->kode_pengajuan ?? '-',
            $pengajuan?->user?->nip ?? '-',
            $pengajuan?->user?->name ?? '-',
            $pengajuan?->user?->unitKerja?->nama ?? '-',
            $pengajuan?->user?->unitKerja?->departemen?->nama ?? '-',
            $pengajuan?->tahunAjaran?->nama ?? '-',
            match ($pengajuan?->status) {
                'draft'     => 'Draft',
                'diajukan'  => 'Diajukan',
                'direvisi'  => 'Perlu Revisi',
                'disetujui' => 'Disetujui',
                'ditolak'   => 'Ditolak',
                default     => $pengajuan?->status ?? '-',
            },
            $row->kategoriBelanjas?->nama ?? '-',
            $row->nama_item,
            $row->deskripsi ?? '-',
            $row->spesifikasi ?? '-',
            $row->satuan,
            (float) $row->volume,
            (float) $row->harga_satuan,
            (float) $row->total_harga,
            match ($row->status) {
                'pending'   => 'Belum Direview',
                'disetujui' => 'Disetujui',
                'ditolak'   => 'Ditolak',
                default     => $row->status,
            },
            $row->catatan_reviewer ?? '-',
        ];
    }

    public function title(): string
    {
        return 'Detail Item';
    }

    public function styles(Worksheet $sheet): array
    {
        $lastCol = 'R';
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '15803D']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'AAAAAA']]],
        ]);

        $highestRow = $sheet->getHighestRow();
        if ($highestRow > 1) {
            // Format number columns: N (volume), O (harga_satuan), P (total_harga)
            $sheet->getStyle("N2:N{$highestRow}")->getNumberFormat()->setFormatCode('#,##0.##');
            $sheet->getStyle("O2:P{$highestRow}")->getNumberFormat()->setFormatCode('#,##0');
            // Borders
            $sheet->getStyle("A2:{$lastCol}{$highestRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => false],
            ]);
            // Ditolak rows: light red background
            // (done per-row would require WithConditionalStyles; skip for simplicity)
        }

        return [];
    }
}
