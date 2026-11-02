<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_comprobante', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 10)->unique(); // ING, EGR, TRS, APE
            $table->string('nombre', 60);           // Ingreso, Egreso, Traspaso, Apertura
            $table->string('descripcion', 255)->nullable();
            $table->boolean('activo')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_comprobante');
    }
};
