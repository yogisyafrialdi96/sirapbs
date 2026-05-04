<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KategoriBelanjaResource\Pages;
use App\Models\KategoriBelanjas;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class KategoriBelanjaResource extends Resource
{
    protected static ?string $model = KategoriBelanjas::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';
    protected static \UnitEnum|string|null $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Kategori Belanja';
    protected static ?string $modelLabel = 'Kategori Belanja';
    protected static ?string $pluralModelLabel = 'Kategori Belanja';
    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make()->schema([
                Forms\Components\TextInput::make('kode')
                    ->required()
                    ->maxLength(20)
                    ->unique(ignoreRecord: true)
                    ->label('Kode Rekening'),
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Kategori'),
                Forms\Components\Textarea::make('deskripsi')
                    ->nullable()
                    ->rows(3)
                    ->columnSpanFull()
                    ->label('Deskripsi'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode')
                    ->searchable()
                    ->sortable()
                    ->label('Kode Rekening'),
                Tables\Columns\TextColumn::make('nama')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Kategori'),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->limit(60)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Deskripsi'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Aktif'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Diperbarui'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif'),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make()
                    ->before(function (Actions\DeleteAction $action, KategoriBelanjas $record) {
                        if ($record->itemPengajuans()->exists()) {
                            $action->cancel();
                            \Filament\Notifications\Notification::make()
                                ->danger()
                                ->title('Tidak dapat dihapus')
                                ->body('Kategori ini sudah digunakan dalam item pengajuan.')
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('kode');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListKategoriBelanja::route('/'),
            'create' => Pages\CreateKategoriBelanja::route('/create'),
            'edit'   => Pages\EditKategoriBelanja::route('/{record}/edit'),
        ];
    }
}
