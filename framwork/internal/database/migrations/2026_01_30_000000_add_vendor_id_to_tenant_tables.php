<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('vendor_id')->nullable()->after('id');
            $table->index('vendor_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('vendor_id')->nullable()->after('id');
            $table->index('vendor_id');
        });

        Schema::table('coupons', function (Blueprint $table) {
            $table->unsignedBigInteger('vendor_id')->nullable()->after('id');
            $table->index('vendor_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('vendor_id')->nullable()->after('id');
            $table->index('vendor_id');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('vendor_id')->nullable()->after('id');
            $table->index('vendor_id');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->unsignedBigInteger('vendor_id')->nullable()->after('id');
            $table->index('vendor_id');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->unsignedBigInteger('vendor_id')->nullable()->after('id');
            $table->index('vendor_id');
        });

        Schema::table('inventory', function (Blueprint $table) {
            $table->unsignedBigInteger('vendor_id')->nullable()->after('id');
            $table->index('vendor_id');
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->unsignedBigInteger('vendor_id')->nullable()->after('id');
            $table->index('vendor_id');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->unsignedBigInteger('vendor_id')->nullable()->after('id');
            $table->index('vendor_id');
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->unsignedBigInteger('vendor_id')->nullable()->after('id');
            $table->index('vendor_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('vendor_id')->nullable()->after('id');
            $table->index('vendor_id');
        });

        Schema::table('referments', function (Blueprint $table) {
            $table->unsignedBigInteger('vendor_id')->nullable()->after('id');
            $table->index('vendor_id');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('vendor_id');
        });

        Schema::table('referments', function (Blueprint $table) {
            $table->dropColumn('vendor_id');
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn('vendor_id');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn('vendor_id');
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropColumn('vendor_id');
        });

        Schema::table('inventory', function (Blueprint $table) {
            $table->dropColumn('vendor_id');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn('vendor_id');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('vendor_id');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('vendor_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('vendor_id');
        });

        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn('vendor_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('vendor_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('vendor_id');
        });
    }
};
