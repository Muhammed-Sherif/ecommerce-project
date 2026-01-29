<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_id');
            $table->enum('type', ['in', 'out', 'adjustment', 'reserved', 'released']);
            $table->integer('quantity');
            $table->string('reason');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('created_at');

            $table->foreign('inventory_id')
                  ->references('id')
                  ->on('inventory')
                  ->onDelete('cascade');

            $table->index('inventory_id');
            $table->index('type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
