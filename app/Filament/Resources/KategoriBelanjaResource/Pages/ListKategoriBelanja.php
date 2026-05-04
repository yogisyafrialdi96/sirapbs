<?php

namespace App\Filament\Resources\KategoriBelanjaResource\Pages;

use App\Filament\Resources\KategoriBelanjaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKategoriBelanja extends ListRecords
{
    protected static string $resource = KategoriBelanjaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
