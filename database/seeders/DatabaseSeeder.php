<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin user
        User::firstOrCreate(
            ['email' => 'admin@sirapbs.id'],
            [
                'name'      => 'Administrator',
                'password'  => bcrypt('password'),
                'role'      => 'admin',
                'is_active' => true,
            ]
        );

        $this->call([
            DepartemenSeeder::class,
            UnitKerjaSeeder::class,
            JabatanSeeder::class,
            KategoriBelanjaSeeder::class,
            UserPegawaiSeeder::class,
        ]);
    }
}
