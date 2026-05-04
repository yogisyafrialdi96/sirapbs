<?php

namespace App\Filament\Resources\PengajuanRapbsResource\RelationManagers;

use App\Models\ItemPengajuan;
use App\Models\KategoriBelanjas;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;

class ItemPengajuansRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Item Kebutuhan';
    protected static ?string $recordTitleAttribute = 'nama_item';

    /**
     * Hanya dapat membuat/mengedit/menghapus item saat status draft atau direvisi.
     */
    public function isReadOnly(): bool
    {
        return ! in_array($this->getOwnerRecord()->status, ['draft', 'direvisi']);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('kategori_belanja_id')
                ->label('Kategori Belanja')
                ->options(fn () => KategoriBelanjas::where('is_active', true)
                    ->orderBy('nama')
                    ->pluck('nama', 'id'))
                ->searchable()
                ->preload()
                ->required(),

            Forms\Components\TextInput::make('nama_item')
                ->label('Nama Item')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),

            Forms\Components\Textarea::make('deskripsi')
                ->label('Deskripsi')
                ->rows(2)
                ->nullable()
                ->columnSpanFull(),

            Forms\Components\Textarea::make('spesifikasi')
                ->label('Spesifikasi Teknis')
                ->rows(3)
                ->nullable()
                ->columnSpanFull(),

            Forms\Components\TextInput::make('satuan')
                ->label('Satuan')
                ->required()
                ->maxLength(30)
                ->placeholder('Unit, Set, Buah, …'),

            Forms\Components\TextInput::make('volume')
                ->label('Volume / Jumlah')
                ->required()
                ->numeric()
                ->minValue(0.01)
                ->live(debounce: 500)
                ->afterStateUpdated(function (Get $get, Set $set, ?string $state): void {
                    $harga = (float) str_replace(',', '.', $get('harga_satuan') ?? 0);
                    $vol   = (float) str_replace(',', '.', $state ?? 0);
                    $set('total_harga', $vol > 0 && $harga > 0 ? round($vol * $harga, 2) : 0);
                }),

            Forms\Components\TextInput::make('harga_satuan')
                ->label('Harga Satuan (Rp)')
                ->required()
                ->numeric()
                ->minValue(0)
                ->prefix('Rp')
                ->live(debounce: 500)
                ->afterStateUpdated(function (Get $get, Set $set, ?string $state): void {
                    $harga = (float) str_replace(',', '.', $state ?? 0);
                    $vol   = (float) str_replace(',', '.', $get('volume') ?? 0);
                    $set('total_harga', $vol > 0 && $harga > 0 ? round($vol * $harga, 2) : 0);
                }),

            Forms\Components\TextInput::make('total_harga')
                ->label('Total Harga (Rp)')
                ->prefix('Rp')
                ->numeric()
                ->disabled()
                ->dehydrated()
                ->helperText('Dihitung otomatis dari volume × harga satuan.'),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kategoriBelanjas.nama')
                    ->label('Kategori')
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('nama_item')
                    ->label('Nama Item')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn (ItemPengajuan $record): string => $record->nama_item),

                Tables\Columns\TextColumn::make('satuan')
                    ->label('Satuan'),

                Tables\Columns\TextColumn::make('volume')
                    ->label('Volume')
                    ->numeric(decimalPlaces: 2),

                Tables\Columns\TextColumn::make('harga_satuan')
                    ->label('Harga Satuan')
                    ->money('IDR'),

                Tables\Columns\TextColumn::make('total_harga')
                    ->label('Total Harga')
                    ->money('IDR')
                    ->sortable()
                    ->summarize([
                        Sum::make('semua')
                            ->money('IDR')
                            ->label('Total Semua Item'),
                        Sum::make('disetujui')
                            ->money('IDR')
                            ->label('Total Disetujui')
                            ->query(fn ($query) => $query->where('status', 'disetujui')),
                    ]),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray'    => 'pending',
                        'success' => 'disetujui',
                        'danger'  => 'ditolak',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending'   => 'Belum Direview',
                        'disetujui' => 'Disetujui',
                        'ditolak'   => 'Ditolak',
                        default     => $state,
                    })
                    ->label('Status Review'),

                Tables\Columns\TextColumn::make('catatan_reviewer')
                    ->label('Catatan Reviewer')
                    ->wrap()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->headerActions([
                Actions\CreateAction::make(),
            ])
            ->actions([
                // --- Admin: persetujuan per item (hanya saat status diajukan) ---
                Actions\Action::make('setujui_item')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->schema([
                        Forms\Components\Textarea::make('catatan_reviewer')
                            ->label('Catatan Persetujuan (opsional)')
                            ->rows(2)
                            ->nullable(),
                    ])
                    ->modalHeading('Setujui Item')
                    ->action(function (ItemPengajuan $record, array $data): void {
                        $record->update([
                            'status'           => 'disetujui',
                            'catatan_reviewer' => $data['catatan_reviewer'] ?? null,
                        ]);
                        Notification::make()->success()->title('Item disetujui.')->send();
                    })
                    ->visible(fn (ItemPengajuan $record): bool =>
                        $this->getOwnerRecord()->status === 'diajukan'
                        && auth()->user()?->isAdmin()
                        && $record->status !== 'disetujui'
                    ),

                Actions\Action::make('tolak_item')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->schema([
                        Forms\Components\Textarea::make('catatan_reviewer')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->rows(2),
                    ])
                    ->modalHeading('Tolak Item')
                    ->action(function (ItemPengajuan $record, array $data): void {
                        $record->update([
                            'status'           => 'ditolak',
                            'catatan_reviewer' => $data['catatan_reviewer'],
                        ]);
                        Notification::make()->danger()->title('Item ditolak.')->send();
                    })
                    ->visible(fn (ItemPengajuan $record): bool =>
                        $this->getOwnerRecord()->status === 'diajukan'
                        && auth()->user()?->isAdmin()
                        && $record->status !== 'ditolak'
                    ),

                Actions\Action::make('reset_review')
                    ->label('Reset')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Reset Status Review')
                    ->modalDescription('Status review item ini akan dikembalikan ke "Belum Direview".')
                    ->action(function (ItemPengajuan $record): void {
                        $record->update([
                            'status'           => 'pending',
                            'catatan_reviewer' => null,
                        ]);
                        Notification::make()->info()->title('Status review direset.')->send();
                    })
                    ->visible(fn (ItemPengajuan $record): bool =>
                        $this->getOwnerRecord()->status === 'diajukan'
                        && auth()->user()?->isAdmin()
                        && $record->status !== 'pending'
                    ),

                // --- Lihat detail item (selalu tersedia saat isReadOnly) ---
                Actions\ViewAction::make()
                    ->visible(fn () => $this->isReadOnly()),

                // --- Pegawai: edit/hapus item (isReadOnly() membatasi hanya draft/direvisi) ---
                Actions\EditAction::make()
                    ->after(function (ItemPengajuan $record): void {
                        // Reset status review saat item diedit dalam direvisi
                        if ($record->status !== 'pending') {
                            $record->update(['status' => 'pending', 'catatan_reviewer' => null]);
                        }
                    }),

                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at');
    }
}
