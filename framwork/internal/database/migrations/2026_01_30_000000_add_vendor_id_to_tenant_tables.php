<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'vendor_id')) {
                $table->unsignedBigInteger('vendor_id')->nullable()->after('id');
                $table->index('vendor_id');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'vendor_id')) {
                $table->unsignedBigInteger('vendor_id')->nullable()->after('id');
                $table->index('vendor_id');
            }
        });

        Schema::table('coupons', function (Blueprint $table) {
            if (!Schema::hasColumn('coupons', 'vendor_id')) {
                $table->unsignedBigInteger('vendor_id')->nullable()->after('id');
                $table->index('vendor_id');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'vendor_id')) {
                $table->unsignedBigInteger('vendor_id')->nullable()->after('id');
                $table->index('vendor_id');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'vendor_id')) {
                $table->unsignedBigInteger('vendor_id')->nullable()->after('id');
                $table->index('vendor_id');
            }
        });

        Schema::table('reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('reviews', 'vendor_id')) {
                $table->unsignedBigInteger('vendor_id')->nullable()->after('id');
                $table->index('vendor_id');
            }
        });

        Schema::table('comments', function (Blueprint $table) {
            if (!Schema::hasColumn('comments', 'vendor_id')) {
                $table->unsignedBigInteger('vendor_id')->nullable()->after('id');
                $table->index('vendor_id');
            }
        });

        Schema::table('inventory', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory', 'vendor_id')) {
                $table->unsignedBigInteger('vendor_id')->nullable()->after('id');
                $table->index('vendor_id');
            }
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_movements', 'vendor_id')) {
                $table->unsignedBigInteger('vendor_id')->nullable()->after('id');
                $table->index('vendor_id');
            }
        });

        Schema::table('cart_items', function (Blueprint $table) {
            if (!Schema::hasColumn('cart_items', 'vendor_id')) {
                $table->unsignedBigInteger('vendor_id')->nullable()->after('id');
                $table->index('vendor_id');
            }
        });

        Schema::table('shipments', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments', 'vendor_id')) {
                $table->unsignedBigInteger('vendor_id')->nullable()->after('id');
                $table->index('vendor_id');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'vendor_id')) {
                $table->unsignedBigInteger('vendor_id')->nullable()->after('id');
                $table->index('vendor_id');
            }
        });

        // Schema::table('referments', function (Blueprint $table) {
        //     if (!Schema::hasColumn('referments', 'vendor_id')) {
        //         $table->unsignedBigInteger('vendor_id')->nullable()->after('id');
        //         $table->index('vendor_id');
        //     }
        // });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'vendor_id')) {
                $table->dropColumn('vendor_id');
            }
        });

        // Schema::table('referments', function (Blueprint $table) {
        //     if (Schema::hasColumn('referments', 'vendor_id')) {
        //         $table->dropColumn('vendor_id');
        //     }
        // });

        Schema::table('shipments', function (Blueprint $table) {
            if (Schema::hasColumn('shipments', 'vendor_id')) {
                $table->dropColumn('vendor_id');
            }
        });

        Schema::table('cart_items', function (Blueprint $table) {
            if (Schema::hasColumn('cart_items', 'vendor_id')) {
                $table->dropColumn('vendor_id');
            }
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            if (Schema::hasColumn('stock_movements', 'vendor_id')) {
                $table->dropColumn('vendor_id');
            }
        });

        Schema::table('inventory', function (Blueprint $table) {
            if (Schema::hasColumn('inventory', 'vendor_id')) {
                $table->dropColumn('vendor_id');
            }
        });

        Schema::table('comments', function (Blueprint $table) {
            if (Schema::hasColumn('comments', 'vendor_id')) {
                $table->dropColumn('vendor_id');
            }
        });

        Schema::table('reviews', function (Blueprint $table) {
            if (Schema::hasColumn('reviews', 'vendor_id')) {
                $table->dropColumn('vendor_id');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'vendor_id')) {
                $table->dropColumn('vendor_id');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'vendor_id')) {
                $table->dropColumn('vendor_id');
            }
        });

        Schema::table('coupons', function (Blueprint $table) {
            if (Schema::hasColumn('coupons', 'vendor_id')) {
                $table->dropColumn('vendor_id');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'vendor_id')) {
                $table->dropColumn('vendor_id');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'vendor_id')) {
                $table->dropColumn('vendor_id');
            }
        });
    }
};
