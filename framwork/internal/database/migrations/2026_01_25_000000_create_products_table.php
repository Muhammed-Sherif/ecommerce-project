<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->string('category'); // Living Room, Office, etc.
            $table->string('image')->nullable();
            // JSON column for multiple images in a real app, or a separate table.
            // For simplicity in this monolith, we'll store main image here and maybe a JSON array for others or leave as single.
            $table->json('images')->nullable(); 
            $table->json('features')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
