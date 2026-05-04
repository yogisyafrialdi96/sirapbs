<?php

namespace App\Filament\Resources\KategoriBelanjaResource\Pages;

use App\Filament\Resources\KategoriBelanjaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKategoriBelanja extends EditRecord
{
    protected static string $resource = KategoriBelanjaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
