<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            if (!Schema::hasColumn('cart_items', 'coupon_id')) {
                $table->unsignedBigInteger('coupon_id')->nullable()->after('product_id');
                $table->foreign('coupon_id')->references('id')->on('coupons')->nullOnDelete();
            }
            if (!Schema::hasColumn('cart_items', 'coupon_code')) {
                $table->string('coupon_code')->nullable()->after('coupon_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            if (Schema::hasColumn('cart_items', 'coupon_code')) {
                $table->dropColumn('coupon_code');
            }
            if (Schema::hasColumn('cart_items', 'coupon_id')) {
                $table->dropForeign(['coupon_id']);
                $table->dropColumn('coupon_id');
            }
        });
    }
};
