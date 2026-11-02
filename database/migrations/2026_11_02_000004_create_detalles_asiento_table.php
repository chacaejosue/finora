<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalles_asiento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asiento_id')->constrained('asientos')->cascadeOnDelete();
            $table->foreignId('cuenta_contable_id')->constrained('cuentas_contables')->restrictOnDelete();
            $table->string('glosa_detalle', 255)->nullable();
            $table->decimal('debe', 12, 2)->default(0);
            $table->decimal('haber', 12, 2)->default(0);
            $table->timestamps();

            $table->index(['asiento_id', 'cuenta_contable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalles_asiento');
    }
};
