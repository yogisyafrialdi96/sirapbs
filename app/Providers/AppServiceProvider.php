<?php

namespace App\Providers;

use App\Models\Departemen;
use App\Models\Jabatan;
use App\Models\KategoriBelanjas;
use App\Models\TahunAjaran;
use App\Models\UnitKerja;
use App\Models\User;
use App\Policies\DepartemenPolicy;
use App\Policies\JabatanPolicy;
use App\Policies\KatBelanjaPolicy;
use App\Policies\TahunAjaranPolicy;
use App\Policies\UnitKerjaPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Departemen::class, DepartemenPolicy::class);
        Gate::policy(Jabatan::class, JabatanPolicy::class);
        Gate::policy(UnitKerja::class, UnitKerjaPolicy::class);
        Gate::policy(KategoriBelanjas::class, KatBelanjaPolicy::class);
        Gate::policy(TahunAjaran::class, TahunAjaranPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
    }
}
