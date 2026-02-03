<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Only rename user_id to seller_id in tables where user_id refers to the seller/vendor
        // NOT in cart_items where user_id refers to the customer
        $tables = [
            'products',
            'coupons',
            'orders',
            'order_items',
            'reviews',
            'comments',
            'inventory',
            'stock_movements',
            // 'cart_items', // Excluded: user_id refers to customer, not seller
            'shipments',
            'payments',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'user_id')) {
                Schema::table($table, function (Blueprint $blueprint) use ($table) {
                    $blueprint->renameColumn('user_id', 'seller_id');
                });
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'products',
            'coupons',
            'orders',
            'order_items',
            'reviews',
            'comments',
            'inventory',
            'stock_movements',
            // 'cart_items', // Excluded: user_id refers to customer, not seller
            'shipments',
            'payments',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'seller_id')) {
                Schema::table($table, function (Blueprint $blueprint) use ($table) {
                    $blueprint->renameColumn('seller_id', 'user_id');
                });
            }
        }
    }
};
