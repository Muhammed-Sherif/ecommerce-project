<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('referrer_id'); // Who referred
            $table->unsignedBigInteger('referee_id')->nullable(); // Who was referred
            $table->string('referral_code')->unique();
            $table->string('status')->default('pending'); // pending, completed
            $table->decimal('reward_amount', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('referrer_id')->references('id')->on('users');
            $table->foreign('referee_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
