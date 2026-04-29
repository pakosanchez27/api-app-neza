<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('usuarios_cupones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('coupon_id')->constrained('cupones')->cascadeOnUpdate()->cascadeOnDelete();
            $table->enum('status', ['claimed', 'redeemed', 'expired', 'cancelled'])->default('claimed');
            $table->string('unique_code', 64)->unique();
            $table->timestamp('claimed_at')->nullable();
            $table->timestamp('redeemed_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['coupon_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios_cupones');
    }
};
