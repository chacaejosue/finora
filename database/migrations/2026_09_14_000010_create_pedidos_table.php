<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('usuarios')->restrictOnDelete();
            $table->foreignId('registrado_por_usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamp('fecha')->useCurrent();
            $table->timestamp('fecha_entrega')->nullable();
            $table->foreignId('entregado_por_usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->string('recibido_por', 150)->nullable();
            $table->text('observaciones_entrega')->nullable();
            // PENDIENTE, RESERVADO, PENDIENTE_ABASTECIMIENTO, LISTO_ENTREGA, COMPLETADO, CANCELADO.
            $table->string('estado', 50)->default('PENDIENTE')->index();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index(['cliente_id', 'estado']);
            $table->index(['fecha', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
