<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalles_modelo_contable', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modelo_contable_id')->constrained('modelos_contables')->cascadeOnDelete();
            $table->foreignId('cuenta_contable_id')->constrained('cuentas_contables')->restrictOnDelete();
            // DEBE o HABER
            $table->string('lado', 10);
            // Porcentaje aplicable: 100.00, 87.00, 13.00, 3.00
            $table->decimal('porcentaje', 5, 2)->default(0);
            // TOTAL, COSTO, SUBTOTAL, IT, etc.
            $table->string('tipo_monto', 30)->default('TOTAL');
            $table->string('glosa_defecto', 255)->nullable();
            $table->timestamps();

            $table->index(['modelo_contable_id', 'lado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalles_modelo_contable');
    }
};
