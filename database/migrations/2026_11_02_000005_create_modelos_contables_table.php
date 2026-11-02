<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modelos_contables', function (Blueprint $table) {
            $table->id();
            // Identificador mnemotécnico: VENTA_CONTADO, VENTA_CREDITO, COMPRA_CONTADO, etc.
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 120);
            $table->text('descripcion')->nullable();
            $table->foreignId('tipo_comprobante_id')->constrained('tipos_comprobante')->restrictOnDelete();
            $table->boolean('activo')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modelos_contables');
    }
};
