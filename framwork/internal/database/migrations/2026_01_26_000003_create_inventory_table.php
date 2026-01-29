<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->unique();
            $table->integer('quantity')->default(0);
            $table->integer('reserved_quantity')->default(0);
            $table->string('warehouse_location')->default('default');
            $table->timestamps();

            $table->index('product_id');
            $table->index('warehouse_location');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
