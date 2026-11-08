<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proveedor_id')->constrained('proveedores')->restrictOnDelete();
            $table->foreignId('registrado_por_usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamp('fecha_solicitud')->useCurrent();
            $table->timestamp('fecha_recepcion')->nullable();
            // BORRADOR, SOLICITADA, EN_CAMINO, RECIBIDA, CANCELADA.
            $table->string('estado', 40)->default('BORRADOR')->index();
            $table->string('forma_pago', 30)->default('CONTADO')->index();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index(['proveedor_id', 'fecha_solicitud']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};
