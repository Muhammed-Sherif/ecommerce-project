<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('orders')) {
            return;
        }

        $connection = DB::connection()->getDriverName();
        if ($connection === 'mysql') {
            // Allow both values during transition.
            DB::statement("ALTER TABLE orders MODIFY status ENUM('pending','confirmed','paid','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending'");
        }

        DB::table('orders')->where('status', 'confirmed')->update(['status' => 'paid']);

        if ($connection === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY status ENUM('pending','paid','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('orders')) {
            return;
        }

        $connection = DB::connection()->getDriverName();
        if ($connection === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY status ENUM('pending','confirmed','paid','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending'");
        }
        DB::table('orders')->where('status', 'paid')->update(['status' => 'confirmed']);
        if ($connection === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY status ENUM('pending','confirmed','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending'");
        }
    }
};
