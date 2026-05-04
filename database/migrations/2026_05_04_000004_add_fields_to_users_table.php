<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip', 30)->unique()->nullable()->after('name');
            $table->foreignId('jabatan_id')->nullable()->after('nip')->constrained('jabatans')->nullOnDelete();
            $table->foreignId('unit_kerja_id')->nullable()->after('jabatan_id')->constrained('unit_kerjas')->nullOnDelete();
            $table->boolean('is_active')->default(true)->after('unit_kerja_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['jabatan_id']);
            $table->dropForeign(['unit_kerja_id']);
            $table->dropColumn(['nip', 'jabatan_id', 'unit_kerja_id', 'is_active']);
        });
    }
};
