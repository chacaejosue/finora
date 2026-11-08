<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('apellido', 100)->nullable();
            $table->string('telefono', 30)->nullable()->index();
            $table->string('email', 255)->nullable()->unique();
            $table->string('genero', 20)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->text('observaciones')->nullable();
            $table->string('password')->nullable();
            // Valores iniciales: CONSULTOR, COLABORADOR, CLIENTE.
            $table->string('rol', 30)->index();
            $table->boolean('activo')->default(true)->index();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('ultimo_acceso')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->index(['nombre', 'apellido']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
