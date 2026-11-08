<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->cascadeOnDelete();
            $table->unsignedSmallInteger('numero');
            $table->decimal('monto', 12, 2);
            $table->decimal('monto_pagado', 12, 2)->default(0);
            $table->date('fecha_vencimiento');
            // PENDIENTE, PARCIAL, VENCIDA, PAGADA.
            $table->string('estado', 30)->default('PENDIENTE')->index();
            $table->timestamps();

            $table->unique(['venta_id', 'numero']);
            $table->index(['estado', 'fecha_vencimiento']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuotas');
    }
};
