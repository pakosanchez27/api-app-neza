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
        Schema::create('cupones', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('id_establecimiento');
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->enum('discount_type', ['fixed', 'percentage']);
            $table->decimal('discount_value', 10, 2);
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('claim_limit_per_user')->default(1);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->text('terms')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('id_establecimiento')
                ->references('id_establecimiento')
                ->on('establecimientos')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->index(['id_establecimiento', 'is_active']);
            $table->index(['starts_at', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cupones');
    }
};
