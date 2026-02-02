<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('products', 'user_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->after('id');
                $table->index('user_id');
            });
        }

        $defaultUserId = DB::table('users')->where('role', 'admin')->orderBy('id')->value('id');
        if (!$defaultUserId) {
            $defaultUserId = DB::table('users')->orderBy('id')->value('id');
        }

        if ($defaultUserId) {
            DB::table('products')->whereNull('user_id')->update(['user_id' => $defaultUserId]);
            DB::statement('ALTER TABLE products MODIFY user_id BIGINT UNSIGNED NOT NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('products', 'user_id')) {
            DB::statement('ALTER TABLE products MODIFY user_id BIGINT UNSIGNED NULL');
        }
    }
};
