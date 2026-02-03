<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Revert cart_items back to user_id (it refers to customer, not seller)
        if (Schema::hasTable('cart_items') && Schema::hasColumn('cart_items', 'seller_id')) {
            Schema::table('cart_items', function (Blueprint $blueprint) {
                $blueprint->renameColumn('seller_id', 'user_id');
            });
        }
    }

    public function down(): void
    {
        // Rollback would rename it back to seller_id
        if (Schema::hasTable('cart_items') && Schema::hasColumn('cart_items', 'user_id')) {
            Schema::table('cart_items', function (Blueprint $blueprint) {
                $blueprint->renameColumn('user_id', 'seller_id');
            });
        }
    }
};
