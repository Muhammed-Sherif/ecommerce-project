<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
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
            'cart_items',
            'shipments',
            'payments',
        ];

        foreach ($tables as $table) {
            if (Schema::hasColumn($table, 'vendor_id') && !Schema::hasColumn($table, 'user_id')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->unsignedBigInteger('user_id')->nullable()->after('id');
                    $blueprint->index('user_id');
                });
            }

            if (Schema::hasColumn($table, 'vendor_id') && Schema::hasColumn($table, 'user_id')) {
                DB::table($table)
                    ->whereNull('user_id')
                    ->whereNotNull('vendor_id')
                    ->update(['user_id' => DB::raw('vendor_id')]);
            }
        }

        foreach ($tables as $table) {
            if (Schema::hasColumn($table, 'vendor_id')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->dropColumn('vendor_id');
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
            'cart_items',
            'shipments',
            'payments',
        ];

        foreach ($tables as $table) {
            if (Schema::hasColumn($table, 'user_id') && !Schema::hasColumn($table, 'vendor_id')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->unsignedBigInteger('vendor_id')->nullable()->after('id');
                    $blueprint->index('vendor_id');
                });
            }

            if (Schema::hasColumn($table, 'user_id') && Schema::hasColumn($table, 'vendor_id')) {
                DB::table($table)
                    ->whereNull('vendor_id')
                    ->whereNotNull('user_id')
                    ->update(['vendor_id' => DB::raw('user_id')]);
            }
        }

        foreach ($tables as $table) {
            if (Schema::hasColumn($table, 'user_id')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->dropColumn('user_id');
                });
            }
        }
    }
};
