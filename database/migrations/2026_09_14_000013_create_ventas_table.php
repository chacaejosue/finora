<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('usuarios')->restrictOnDelete();
            $table->foreignId('pedido_id')->nullable()->constrained('pedidos')->nullOnDelete();
            $table->foreignId('registrado_por_usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamp('fecha')->useCurrent();
            // CONTADO o CREDITO.
            $table->string('forma_pago', 30)->index();
            // BORRADOR, CONFIRMADA, CANCELADA, ANULADA.
            $table->string('estado', 30)->default('BORRADOR')->index();
            // Descuento monetario aplicado a la venta completa.
            $table->decimal('descuento', 12, 2)->default(0);
            $table->timestamps();

            $table->index(['cliente_id', 'fecha']);
            $table->index(['pedido_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
