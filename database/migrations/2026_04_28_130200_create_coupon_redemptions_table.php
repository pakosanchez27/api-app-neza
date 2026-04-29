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
        Schema::create('redenciones_cupones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_coupon_id')->constrained('usuarios_cupones')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedInteger('id_establecimiento');
            $table->foreignId('redeemed_by_user_id')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamp('redeemed_at')->useCurrent();
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->foreign('id_establecimiento')
                ->references('id_establecimiento')
                ->on('establecimientos')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unique('user_coupon_id');
            $table->index(['id_establecimiento', 'redeemed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('redenciones_cupones');
    }
};
