<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->restrictOnDelete();
            $table->foreignId('registrado_por_usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->foreignId('detalle_compra_id')->nullable()->constrained('detalle_compras')->nullOnDelete();
            $table->foreignId('detalle_venta_id')->nullable()->constrained('detalle_ventas')->nullOnDelete();
            // ENTRADA_COMPRA, SALIDA_VENTA, AJUSTE_ENTRADA, AJUSTE_SALIDA, REVERSO, etc.
            $table->string('tipo', 40)->index();
            // Cantidad con signo: positiva para entrada, negativa para salida.
            $table->integer('cantidad');
            $table->decimal('costo_unitario', 12, 2)->default(0);
            $table->decimal('costo_total', 12, 2)->default(0);
            $table->timestamp('fecha')->useCurrent();
            $table->text('observacion')->nullable();
            $table->timestamps();

            $table->index(['producto_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};
