<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengajuanRapbsResource\Pages;
use App\Filament\Resources\PengajuanRapbsResource\RelationManagers;
use App\Models\PengajuanRapbs;
use App\Models\RiwayatPersetujuan;
use App\Models\TahunAjaran;
use App\Models\User;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PengajuanRapbsResource extends Resource
{
    protected static ?string $model = PengajuanRapbs::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';
    protected static \UnitEnum|string|null $navigationGroup = 'RAPBS';
    protected static ?string $navigationLabel = 'Pengajuan RAPBS';
    protected static ?string $modelLabel = 'Pengajuan RAPBS';
    protected static ?string $pluralModelLabel = 'Pengajuan RAPBS';
    protected static ?int $navigationSort = 2;

    // ─── Authorization ────────────────────────────────────────────────────────
    // Override langsung agar tidak bergantung pada Gate/policy yang bisa berbeda
    // perilakunya di hosting vs localhost.

    public static function canViewAny(): bool
    {
        return auth()->check();
    }

    public static function canCreate(): bool
    {
        return auth()->check();
    }

    public static function canView(Model $record): bool
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        return $user->isAdmin() || $record->user_id === $user->id;
    }

    public static function canEdit(Model $record): bool
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if ($user->isAdmin()) {
            return true;
        }

        return $record->user_id === $user->id
            && in_array($record->status, ['draft', 'direvisi']);
    }

    public static function canDelete(Model $record): bool
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        return $record->user_id === $user->id
            && in_array($record->status, ['draft', 'ditolak'])
            || ($user->isAdmin() && in_array($record->status, ['draft', 'ditolak']));
    }

    // ─────────────────────────────────────────────────────────────────────────

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Informasi Pengajuan')->schema([
                Forms\Components\TextInput::make('kode_pengajuan')
                    ->label('Kode Pengajuan')
                    ->disabled()
                    ->placeholder('Digenerate otomatis')
                    ->visibleOn('edit'),

                Forms\Components\Select::make('user_id')
                    ->label('Pegawai')
                    ->options(fn () => User::where('is_active', true)
                        ->orderBy('name')
                        ->get()
                        ->mapWithKeys(fn ($u) => [$u->id => ($u->nip ? "[{$u->nip}] " : '') . $u->name])
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->default(fn () => auth()->id())
                    ->disabled(fn () => auth()->user()?->isPegawai() || request()->routeIs('*.edit'))
                    ->dehydrated(true),

                Forms\Components\Select::make('tahun_ajaran_id')
                    ->label('Tahun Ajaran')
                    ->options(fn () => TahunAjaran::where('is_aktif', true)
                        ->orderBy('nama', 'desc')
                        ->pluck('nama', 'id')
                    )
                    ->required()
                    ->disabledOn('edit')
                    ->helperText(fn (): string => TahunAjaran::where('is_aktif', true)->exists()
                        ? ''
                        : 'Tidak ada tahun ajaran yang sedang dibuka. Hubungi administrator.'
                    ),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'draft'     => 'Draft',
                        'diajukan'  => 'Diajukan',
                        'direvisi'  => 'Perlu Revisi',
                        'disetujui' => 'Disetujui',
                        'ditolak'   => 'Ditolak',
                    ])
                    ->disabled()
                    ->visibleOn('edit'),

                Forms\Components\Textarea::make('catatan')
                    ->label('Catatan Reviewer')
                    ->rows(3)
                    ->columnSpanFull()
                    ->disabled()
                    ->visibleOn('edit'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn (PengajuanRapbs $record): string =>
                (in_array($record->status, ['draft', 'direvisi']) && auth()->user()?->isPegawai())
                    ? Pages\EditPengajuanRapbs::getUrl(['record' => $record])
                    : Pages\ViewPengajuanRapbs::getUrl(['record' => $record])
            )
            ->columns([
                Tables\Columns\TextColumn::make('kode_pengajuan')
                    ->searchable()
                    ->sortable()
                    ->label('Kode'),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->label('Pegawai'),
                Tables\Columns\TextColumn::make('user.unitKerja.nama')
                    ->label('Unit Kerja')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('tahunAjaran.nama')
                    ->sortable()
                    ->label('Tahun Ajaran'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray'    => 'draft',
                        'info'    => 'diajukan',
                        'warning' => 'direvisi',
                        'success' => 'disetujui',
                        'danger'  => 'ditolak',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft'     => 'Draft',
                        'diajukan'  => 'Diajukan',
                        'direvisi'  => 'Perlu Revisi',
                        'disetujui' => 'Disetujui',
                        'ditolak'   => 'Ditolak',
                        default     => $state,
                    })
                    ->label('Status'),
                Tables\Columns\TextColumn::make('items_sum_total_harga')
                    ->money('IDR')
                    ->sortable()
                    ->label('Total Anggaran')
                    ->summarize([
                        Sum::make()->money('IDR')->label('Grand Total'),
                    ]),
                Tables\Columns\TextColumn::make('tanggal_pengajuan')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->label('Tgl Pengajuan'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tahun_ajaran_id')
                    ->label('Tahun Ajaran')
                    ->relationship('tahunAjaran', 'nama'),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft'     => 'Draft',
                        'diajukan'  => 'Diajukan',
                        'direvisi'  => 'Perlu Revisi',
                        'disetujui' => 'Disetujui',
                        'ditolak'   => 'Ditolak',
                    ]),
            ])
            ->actions([
                Actions\Action::make('ajukan')
                    ->label('Ajukan')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Ajukan RAPBS')
                    ->modalDescription('Pastikan semua item kebutuhan sudah dimasukkan sebelum mengajukan.')
                    ->visible(fn (PengajuanRapbs $record): bool => $record->status === 'draft')
                    ->action(function (PengajuanRapbs $record): void {
                        if ($record->items()->doesntExist()) {
                            Notification::make()->danger()
                                ->title('Belum ada item')
                                ->body('Tambahkan minimal satu item kebutuhan sebelum mengajukan.')
                                ->send();
                            return;
                        }
                        // Reset status review item agar admin dapat mereview ulang
                        $record->items()->update(['status' => 'pending', 'catatan_reviewer' => null]);
                        $record->update(['status' => 'diajukan', 'tanggal_pengajuan' => now()]);
                        RiwayatPersetujuan::create([
                            'pengajuan_rapbs_id' => $record->id,
                            'user_id'            => auth()->id(),
                            'status'             => 'diajukan',
                        ]);
                        Notification::make()->success()->title('Pengajuan berhasil dikirim.')->send();
                    }),

                Actions\Action::make('setujui')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->schema([
                        Forms\Components\Textarea::make('catatan')
                            ->label('Catatan Persetujuan')->rows(3)->nullable(),
                    ])
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Pengajuan')
                    ->visible(fn (PengajuanRapbs $record): bool => $record->status === 'diajukan' && auth()->user()?->isAdmin())
                    ->action(function (PengajuanRapbs $record, array $data): void {
                        $record->update(['status' => 'disetujui', 'catatan' => $data['catatan'] ?? null]);
                        RiwayatPersetujuan::create([
                            'pengajuan_rapbs_id' => $record->id,
                            'user_id'            => auth()->id(),
                            'status'             => 'disetujui',
                            'catatan'            => $data['catatan'] ?? null,
                        ]);
                        Notification::make()->success()->title('Pengajuan disetujui.')->send();
                    }),

                Actions\Action::make('revisi')
                    ->label('Minta Revisi')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->schema([
                        Forms\Components\Textarea::make('catatan')
                            ->label('Catatan Revisi')->required()->rows(3),
                    ])
                    ->modalHeading('Minta Revisi')
                    ->visible(fn (PengajuanRapbs $record): bool => $record->status === 'diajukan' && auth()->user()?->isAdmin())
                    ->action(function (PengajuanRapbs $record, array $data): void {
                        $record->update(['status' => 'direvisi', 'catatan' => $data['catatan']]);
                        RiwayatPersetujuan::create([
                            'pengajuan_rapbs_id' => $record->id,
                            'user_id'            => auth()->id(),
                            'status'             => 'direvisi',
                            'catatan'            => $data['catatan'],
                        ]);
                        Notification::make()->warning()->title('Pengajuan dikembalikan untuk direvisi.')->send();
                    }),

                Actions\Action::make('tolak')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->schema([
                        Forms\Components\Textarea::make('catatan')
                            ->label('Alasan Penolakan')->required()->rows(3),
                    ])
                    ->modalHeading('Tolak Pengajuan')
                    ->visible(fn (PengajuanRapbs $record): bool => $record->status === 'diajukan' && auth()->user()?->isAdmin())
                    ->action(function (PengajuanRapbs $record, array $data): void {
                        $record->update(['status' => 'ditolak', 'catatan' => $data['catatan']]);
                        RiwayatPersetujuan::create([
                            'pengajuan_rapbs_id' => $record->id,
                            'user_id'            => auth()->id(),
                            'status'             => 'ditolak',
                            'catatan'            => $data['catatan'],
                        ]);
                        Notification::make()->danger()->title('Pengajuan ditolak.')->send();
                    }),

                Actions\Action::make('ajukan_ulang')
                    ->label('Ajukan Ulang')
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Ajukan Ulang RAPBS')
                    ->visible(fn (PengajuanRapbs $record): bool => $record->status === 'direvisi')
                    ->action(function (PengajuanRapbs $record): void {
                        // Reset status review semua item agar admin mereview ulang
                        $record->items()->update(['status' => 'pending', 'catatan_reviewer' => null]);
                        $record->update(['status' => 'diajukan', 'tanggal_pengajuan' => now()]);
                        RiwayatPersetujuan::create([
                            'pengajuan_rapbs_id' => $record->id,
                            'user_id'            => auth()->id(),
                            'status'             => 'diajukan',
                            'catatan'            => 'Diajukan ulang setelah revisi.',
                        ]);
                        Notification::make()->success()->title('Pengajuan berhasil dikirim ulang.')->send();
                    }),

                Actions\ViewAction::make()->label('Lihat Detail'),
                Actions\EditAction::make()->label('Edit')
                    ->visible(fn (PengajuanRapbs $record): bool =>
                        in_array($record->status, ['draft', 'direvisi'])
                    ),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->before(function (Actions\DeleteBulkAction $action, \Illuminate\Database\Eloquent\Collection $records) {
                            $locked = $records->whereNotIn('status', ['draft', 'ditolak']);
                            if ($locked->isNotEmpty()) {
                                $action->cancel();
                                Notification::make()
                                    ->danger()
                                    ->title('Tidak dapat dihapus')
                                    ->body('Hanya pengajuan berstatus Draft atau Ditolak yang dapat dihapus.')
                                    ->send();
                            }
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['user.unitKerja', 'tahunAjaran'])
            ->withSum(
                ['items as items_sum_total_harga' => fn ($q) => $q->where('status', '!=', 'ditolak')],
                'total_harga'
            );

        // Pegawai hanya dapat melihat pengajuan miliknya sendiri
        if (auth()->check() && auth()->user()->isPegawai()) {
            $query->where('user_id', auth()->id());
        }

        return $query;
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemPengajuansRelationManager::class,
            RelationManagers\RiwayatPersetujuansRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPengajuanRapbs::route('/'),
            'create' => Pages\CreatePengajuanRapbs::route('/create'),
            'view'   => Pages\ViewPengajuanRapbs::route('/{record}'),
            'edit'   => Pages\EditPengajuanRapbs::route('/{record}/edit'),
        ];
    }
}
