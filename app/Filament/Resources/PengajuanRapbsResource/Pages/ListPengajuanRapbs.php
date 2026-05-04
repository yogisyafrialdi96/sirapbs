<?php

namespace App\Filament\Resources\PengajuanRapbsResource\Pages;

use App\Filament\Resources\PengajuanRapbsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPengajuanRapbs extends ListRecords
{
    protected static string $resource = PengajuanRapbsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
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
