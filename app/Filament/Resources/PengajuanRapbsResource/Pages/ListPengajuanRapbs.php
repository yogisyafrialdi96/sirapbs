<?php

namespace App\Filament\Resources\PengajuanRapbsResource\Pages;

use App\Exports\PengajuanRapbsExport;
use App\Filament\Resources\PengajuanRapbsResource;
use App\Models\TahunAjaran;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

class ListPengajuanRapbs extends ListRecords
{
    protected static string $resource = PengajuanRapbsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Action::make('export_excel')
                ->label('Export Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->visible(fn () => auth()->user()?->isAdmin())
                ->schema([
                    Forms\Components\Select::make('status')
                        ->label('Filter Status')
                        ->options([
                            ''           => 'Semua Status',
                            'draft'      => 'Draft',
                            'diajukan'   => 'Diajukan',
                            'direvisi'   => 'Perlu Revisi',
                            'disetujui'  => 'Disetujui',
                            'ditolak'    => 'Ditolak',
                        ])
                        ->default('')
                        ->placeholder('Semua Status'),

                    Forms\Components\Select::make('tahun_ajaran_id')
                        ->label('Filter Tahun Ajaran')
                        ->options(
                            TahunAjaran::orderBy('nama', 'desc')
                                ->pluck('nama', 'id')
                                ->prepend('Semua Tahun Ajaran', '')
                        )
                        ->default('')
                        ->placeholder('Semua Tahun Ajaran'),
                ])
                ->modalHeading('Export Data Pengajuan RAPBS')
                ->modalSubmitActionLabel('Download Excel')
                ->action(function (array $data): mixed {
                    $status = $data['status'] ?: null;
                    $tahunAjaranId = $data['tahun_ajaran_id'] ?: null;

                    $filename = 'pengajuan-rapbs-' . now()->format('Ymd-His') . '.xlsx';

                    return Excel::download(
                        new PengajuanRapbsExport($status, $tahunAjaranId),
                        $filename
                    );
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua'),
            'draft' => Tab::make('Draft')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'draft')),
            'diajukan' => Tab::make('Diajukan')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'diajukan')),
            'direvisi' => Tab::make('Perlu Revisi')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'direvisi')),
            'disetujui' => Tab::make('Disetujui')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'disetujui')),
            'ditolak' => Tab::make('Ditolak')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'ditolak')),
        ];
    }
}
