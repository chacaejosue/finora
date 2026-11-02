<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Agregar costo_promedio en productos para Costo Promedio Ponderado (CPP)
        if (Schema::hasTable('productos')) {
            Schema::table('productos', function (Blueprint $table) {
                if (! Schema::hasColumn('productos', 'costo_promedio')) {
                    $table->decimal('costo_promedio', 12, 2)->default(0)->after('precio_venta_actual');
                }
            });
        }

        // 2. Agregar costo_unitario y costo_total en movimientos_inventario para Kardex valorado
        if (Schema::hasTable('movimientos_inventario')) {
            Schema::table('movimientos_inventario', function (Blueprint $table) {
                if (! Schema::hasColumn('movimientos_inventario', 'costo_unitario')) {
                    $table->decimal('costo_unitario', 12, 2)->default(0)->after('cantidad');
                }
                if (! Schema::hasColumn('movimientos_inventario', 'costo_total')) {
                    $table->decimal('costo_total', 12, 2)->default(0)->after('costo_unitario');
                }
            });
        }

        // 3. Agregar forma_pago en compras (CONTADO o CREDITO)
        if (Schema::hasTable('compras')) {
            Schema::table('compras', function (Blueprint $table) {
                if (! Schema::hasColumn('compras', 'forma_pago')) {
                    $table->string('forma_pago', 30)->default('CONTADO')->after('estado');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('productos') && Schema::hasColumn('productos', 'costo_promedio')) {
            Schema::table('productos', function (Blueprint $table) {
                $table->dropColumn('costo_promedio');
            });
        }

        if (Schema::hasTable('movimientos_inventario')) {
            Schema::table('movimientos_inventario', function (Blueprint $table) {
                $columnsToDrop = [];
                if (Schema::hasColumn('movimientos_inventario', 'costo_unitario')) {
                    $columnsToDrop[] = 'costo_unitario';
                }
                if (Schema::hasColumn('movimientos_inventario', 'costo_total')) {
                    $columnsToDrop[] = 'costo_total';
                }
                if (! empty($columnsToDrop)) {
                    $table->dropColumn($columnsToDrop);
                }
            });
        }

        if (Schema::hasTable('compras') && Schema::hasColumn('compras', 'forma_pago')) {
            Schema::table('compras', function (Blueprint $table) {
                $table->dropColumn('forma_pago');
            });
        }
    }
};
