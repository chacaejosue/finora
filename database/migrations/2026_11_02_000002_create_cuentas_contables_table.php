<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuentas_contables', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique()->index(); // ej. '1', '1.1', '1.1.1', '1.1.1.01'
            $table->string('nombre', 150);
            $table->unsignedTinyInteger('nivel')->default(1); // 1 a 5
            // ACTIVO, PASIVO, PATRIMONIO, INGRESO, GASTO, COSTO
            $table->string('tipo', 30)->index();
            // DEUDORA, ACREEDORA
            $table->string('naturaleza', 20)->default('DEUDORA');
            $table->foreignId('padre_id')->nullable()->constrained('cuentas_contables')->nullOnDelete();
            // true = cuenta de imputación/movimiento (recibe asientos). false = cuenta de grupo/totalizadora
            $table->boolean('permite_movimiento')->default(false)->index();
            $table->boolean('activo')->default(true)->index();
            $table->timestamps();

            $table->index(['tipo', 'permite_movimiento']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuentas_contables');
    }
};
