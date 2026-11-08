<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asientos', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo_comprobante', ['ING', 'EGR', 'TRS', 'APE'])->default('TRS')->index();
            $table->string('numero', 50)->index(); // Correlativo: ej. CI-2026-00001
            $table->date('fecha')->index();
            $table->text('glosa');
            // BORRADOR, APROBADO, ANULADO
            $table->string('estado', 30)->default('APROBADO')->index();
            // Relación polimórfica (Venta, Compra, Pago, MovimientoInventario, etc.)
            $table->nullableMorphs('contabilizable');
            $table->decimal('total_debe', 12, 2)->default(0);
            $table->decimal('total_haber', 12, 2)->default(0);
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamps();

            $table->index(['fecha', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asientos');
    }
};
