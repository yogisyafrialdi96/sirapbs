<?php

namespace App\Policies;

use App\Models\PengajuanRapbs;
use App\Models\User;

class PengajuanRapbsPolicy
{
    /** Semua user aktif bisa melihat daftar (query akan difilter di resource). */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /** Admin bisa lihat semua; pegawai hanya milik sendiri. */
    public function view(User $user, PengajuanRapbs $record): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $record->user_id === $user->id;
    }

    /** Pegawai dan admin boleh membuat pengajuan. */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Admin bisa edit semua.
     * Pegawai hanya bisa edit milik sendiri yang masih draft atau direvisi.
     */
    public function update(User $user, PengajuanRapbs $record): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $record->user_id === $user->id
            && in_array($record->status, ['draft', 'direvisi']);
    }

    /**
     * Admin bisa hapus semua.
     * Pegawai hanya bisa hapus milik sendiri yang masih draft atau ditolak.
     */
    public function delete(User $user, PengajuanRapbs $record): bool
    {
        if ($user->isAdmin()) {
            return in_array($record->status, ['draft', 'ditolak']);
        }

        return $record->user_id === $user->id
            && in_array($record->status, ['draft', 'ditolak']);
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }
}
