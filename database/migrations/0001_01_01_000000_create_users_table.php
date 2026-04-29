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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('nombre_p', 60)->nullable();
            $table->string('app_p', 60)->nullable();
            $table->string('apm_p', 60)->nullable();
            $table->string('email', 80)->unique();
            $table->string('telefono', 15)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('is_password_templ')->nullable()->default(false);
            $table->string('password')->nullable();
            $table->date('ultimo_acceso')->nullable();
            $table->string('estatus', 45)->nullable();
            $table->boolean('activo')->nullable()->default(false);
            $table->string('token_activacion', 255)->nullable();
            $table->string('foto_perfil')->nullable(); // Agrega el ->nullable()
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
