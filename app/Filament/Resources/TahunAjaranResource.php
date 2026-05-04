<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TahunAjaranResource\Pages;
use App\Models\TahunAjaran;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class TahunAjaranResource extends Resource
{
    protected static ?string $model = TahunAjaran::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';
    protected static \UnitEnum|string|null $navigationGroup = 'RAPBS';
    protected static ?string $navigationLabel = 'Tahun Ajaran';
    protected static ?string $modelLabel = 'Tahun Ajaran';
    protected static ?string $pluralModelLabel = 'Tahun Ajaran';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make()->schema([
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(20)
                    ->unique(ignoreRecord: true)
                    ->placeholder('2025/2026')
                    ->label('Nama Tahun Ajaran'),
                Forms\Components\Toggle::make('is_aktif')
                    ->label('Buka Pengajuan')
                    ->helperText('Hanya satu tahun ajaran yang boleh aktif pada satu waktu.')
                    ->default(false),
                Forms\Components\DatePicker::make('tanggal_mulai')
                    ->required()
                    ->label('Tanggal Mulai'),
                Forms\Components\DatePicker::make('tanggal_selesai')
                    ->required()
                    ->after('tanggal_mulai')
                    ->label('Tanggal Selesai'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->searchable()
                    ->sortable()
                    ->label('Tahun Ajaran'),
                Tables\Columns\TextColumn::make('tanggal_mulai')
                    ->date('d M Y')
                    ->sortable()
                    ->label('Mulai'),
                Tables\Columns\TextColumn::make('tanggal_selesai')
                    ->date('d M Y')
                    ->sortable()
                    ->label('Selesai'),
                Tables\Columns\BadgeColumn::make('is_aktif')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Pengajuan Dibuka' : 'Pengajuan Ditutup')
                    ->colors([
                        'success' => true,
                        'gray'    => false,
                    ])
                    ->label('Status'),
                Tables\Columns\TextColumn::make('dibukOleh.name')
                    ->label('Dibuka Oleh')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('dibuka_pada')
                    ->dateTime('d M Y H:i')
                    ->label('Dibuka Pada')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ditutup_pada')
                    ->dateTime('d M Y H:i')
                    ->label('Ditutup Pada')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pengajuan_rapbs_count')
                    ->counts('pengajuanRapbs')
                    ->label('Jml Pengajuan')
                    ->alignCenter(),
            ])
            ->filters([])
            ->actions([
                Actions\Action::make('buka')
                    ->label('Buka Pengajuan')
                    ->icon('heroicon-o-lock-open')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Buka Periode Pengajuan')
                    ->modalDescription('Tahun ajaran lain yang sedang aktif akan ditutup secara otomatis.')
                    ->visible(fn (TahunAjaran $record): bool => ! $record->is_aktif)
                    ->action(function (TahunAjaran $record): void {
                        DB::transaction(function () use ($record) {
                            TahunAjaran::where('is_aktif', true)
                                ->where('id', '!=', $record->id)
                                ->update([
                                    'is_aktif'    => false,
                                    'ditutup_pada' => now(),
                                ]);

                            $record->update([
                                'is_aktif'    => true,
                                'dibuka_oleh' => auth()->id(),
                                'dibuka_pada' => now(),
                                'ditutup_pada' => null,
                            ]);
                        });

                        Notification::make()
                            ->success()
                            ->title('Periode pengajuan dibuka')
                            ->body("Tahun ajaran {$record->nama} kini aktif menerima pengajuan.")
                            ->send();
                    }),

                Actions\Action::make('tutup')
                    ->label('Tutup Pengajuan')
                    ->icon('heroicon-o-lock-closed')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Tutup Periode Pengajuan')
                    ->modalDescription('Pegawai tidak dapat lagi mengajukan RAPBS untuk tahun ajaran ini.')
                    ->visible(fn (TahunAjaran $record): bool => $record->is_aktif)
                    ->action(function (TahunAjaran $record): void {
                        $record->update([
                            'is_aktif'    => false,
                            'ditutup_pada' => now(),
                        ]);

                        Notification::make()
                            ->warning()
                            ->title('Periode pengajuan ditutup')
                            ->body("Tahun ajaran {$record->nama} telah ditutup.")
                            ->send();
                    }),

                Actions\EditAction::make(),
                Actions\DeleteAction::make()
                    ->before(function (Actions\DeleteAction $action, TahunAjaran $record) {
                        if ($record->pengajuanRapbs()->exists()) {
                            $action->cancel();
                            Notification::make()
                                ->danger()
                                ->title('Tidak dapat dihapus')
                                ->body('Tahun ajaran ini sudah memiliki data pengajuan.')
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([])
            ->defaultSort('nama', 'desc');
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->withCount('pengajuanRapbs')
            ->with('dibukOleh');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTahunAjarans::route('/'),
            'create' => Pages\CreateTahunAjaran::route('/create'),
            'edit'   => Pages\EditTahunAjaran::route('/{record}/edit'),
        ];
    }
}
