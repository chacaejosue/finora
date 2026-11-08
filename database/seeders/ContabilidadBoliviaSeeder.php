<?php

namespace Database\Seeders;

use App\Models\CuentaContable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContabilidadBoliviaSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $this->seedPlanDeCuentas();
        });
    }

    private function seedPlanDeCuentas(): void
    {
        $cuentas = [
            // ==================== NIVEL 1 ====================
            ['codigo' => '1', 'nombre' => 'ACTIVO', 'nivel' => 1, 'tipo' => 'ACTIVO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => null, 'permite_movimiento' => false],
            ['codigo' => '2', 'nombre' => 'PASIVO', 'nivel' => 1, 'tipo' => 'PASIVO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => null, 'permite_movimiento' => false],
            ['codigo' => '3', 'nombre' => 'PATRIMONIO', 'nivel' => 1, 'tipo' => 'PATRIMONIO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => null, 'permite_movimiento' => false],
            ['codigo' => '4', 'nombre' => 'INGRESOS', 'nivel' => 1, 'tipo' => 'INGRESO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => null, 'permite_movimiento' => false],
            ['codigo' => '5', 'nombre' => 'COSTOS', 'nivel' => 1, 'tipo' => 'COSTO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => null, 'permite_movimiento' => false],
            ['codigo' => '6', 'nombre' => 'GASTOS', 'nivel' => 1, 'tipo' => 'GASTO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => null, 'permite_movimiento' => false],

            // ==================== NIVEL 2: ACTIVO ====================
            ['codigo' => '1.1', 'nombre' => 'ACTIVO CORRIENTE', 'nivel' => 2, 'tipo' => 'ACTIVO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '1', 'permite_movimiento' => false],
            ['codigo' => '1.2', 'nombre' => 'ACTIVO NO CORRIENTE', 'nivel' => 2, 'tipo' => 'ACTIVO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '1', 'permite_movimiento' => false],

            // NIVEL 3: ACTIVO CORRIENTE
            ['codigo' => '1.1.1', 'nombre' => 'DISPONIBLE', 'nivel' => 3, 'tipo' => 'ACTIVO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '1.1', 'permite_movimiento' => false],
            ['codigo' => '1.1.2', 'nombre' => 'EXIGIBLE', 'nivel' => 3, 'tipo' => 'ACTIVO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '1.1', 'permite_movimiento' => false],
            ['codigo' => '1.1.3', 'nombre' => 'REALIZABLE (INVENTARIOS)', 'nivel' => 3, 'tipo' => 'ACTIVO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '1.1', 'permite_movimiento' => false],
            ['codigo' => '1.1.4', 'nombre' => 'PAGOS ANTICIPADOS', 'nivel' => 3, 'tipo' => 'ACTIVO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '1.1', 'permite_movimiento' => false],

            // NIVEL 4: CUENTAS IMPUTABLES DISPONIBLE
            ['codigo' => '1.1.1.01', 'nombre' => 'Caja Moneda Nacional', 'nivel' => 4, 'tipo' => 'ACTIVO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '1.1.1', 'permite_movimiento' => true],
            ['codigo' => '1.1.1.02', 'nombre' => 'Bancos Moneda Nacional', 'nivel' => 4, 'tipo' => 'ACTIVO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '1.1.1', 'permite_movimiento' => true],
            ['codigo' => '1.1.1.03', 'nombre' => 'Caja Chica', 'nivel' => 4, 'tipo' => 'ACTIVO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '1.1.1', 'permite_movimiento' => true],

            // NIVEL 4: CUENTAS IMPUTABLES EXIGIBLE
            ['codigo' => '1.1.2.01', 'nombre' => 'Cuentas por Cobrar Clientes', 'nivel' => 4, 'tipo' => 'ACTIVO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '1.1.2', 'permite_movimiento' => true],
            ['codigo' => '1.1.2.02', 'nombre' => 'Documentos por Cobrar', 'nivel' => 4, 'tipo' => 'ACTIVO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '1.1.2', 'permite_movimiento' => true],
            ['codigo' => '1.1.2.03', 'nombre' => 'Crédito Fiscal IVA (13%)', 'nivel' => 4, 'tipo' => 'ACTIVO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '1.1.2', 'permite_movimiento' => true],

            // NIVEL 4: CUENTAS IMPUTABLES REALIZABLE
            ['codigo' => '1.1.3.01', 'nombre' => 'Inventario de Mercaderías', 'nivel' => 4, 'tipo' => 'ACTIVO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '1.1.3', 'permite_movimiento' => true],

            // NIVEL 4: CUENTAS IMPUTABLES PAGOS ANTICIPADOS
            ['codigo' => '1.1.4.01', 'nombre' => 'Alquileres Pagados por Adelantado', 'nivel' => 4, 'tipo' => 'ACTIVO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '1.1.4', 'permite_movimiento' => true],
            ['codigo' => '1.1.4.02', 'nombre' => 'Seguros Pagados por Adelantado', 'nivel' => 4, 'tipo' => 'ACTIVO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '1.1.4', 'permite_movimiento' => true],

            // NIVEL 3: ACTIVO NO CORRIENTE
            ['codigo' => '1.2.1', 'nombre' => 'BIENES DE USO', 'nivel' => 3, 'tipo' => 'ACTIVO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '1.2', 'permite_movimiento' => false],
            ['codigo' => '1.2.2', 'nombre' => 'DEPRECIACIÓN ACUMULADA BIENES DE USO', 'nivel' => 3, 'tipo' => 'ACTIVO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => '1.2', 'permite_movimiento' => false],

            // NIVEL 4: CUENTAS BIENES DE USO
            ['codigo' => '1.2.1.01', 'nombre' => 'Terrenos', 'nivel' => 4, 'tipo' => 'ACTIVO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '1.2.1', 'permite_movimiento' => true],
            ['codigo' => '1.2.1.02', 'nombre' => 'Edificios', 'nivel' => 4, 'tipo' => 'ACTIVO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '1.2.1', 'permite_movimiento' => true],
            ['codigo' => '1.2.1.03', 'nombre' => 'Muebles y Enseres', 'nivel' => 4, 'tipo' => 'ACTIVO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '1.2.1', 'permite_movimiento' => true],
            ['codigo' => '1.2.1.04', 'nombre' => 'Equipos de Computación', 'nivel' => 4, 'tipo' => 'ACTIVO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '1.2.1', 'permite_movimiento' => true],
            ['codigo' => '1.2.1.05', 'nombre' => 'Vehículos Automotores', 'nivel' => 4, 'tipo' => 'ACTIVO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '1.2.1', 'permite_movimiento' => true],

            // NIVEL 4: DEPRECIACIONES ACUMULADAS
            ['codigo' => '1.2.2.01', 'nombre' => 'Depreciación Acumulada Edificios', 'nivel' => 4, 'tipo' => 'ACTIVO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => '1.2.2', 'permite_movimiento' => true],
            ['codigo' => '1.2.2.02', 'nombre' => 'Depreciación Acumulada Muebles y Enseres', 'nivel' => 4, 'tipo' => 'ACTIVO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => '1.2.2', 'permite_movimiento' => true],
            ['codigo' => '1.2.2.03', 'nombre' => 'Depreciación Acumulada Equipos de Computación', 'nivel' => 4, 'tipo' => 'ACTIVO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => '1.2.2', 'permite_movimiento' => true],
            ['codigo' => '1.2.2.04', 'nombre' => 'Depreciación Acumulada Vehículos', 'nivel' => 4, 'tipo' => 'ACTIVO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => '1.2.2', 'permite_movimiento' => true],

            // ==================== NIVEL 2: PASIVO ====================
            ['codigo' => '2.1', 'nombre' => 'PASIVO CORRIENTE', 'nivel' => 2, 'tipo' => 'PASIVO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => '2', 'permite_movimiento' => false],
            ['codigo' => '2.2', 'nombre' => 'PASIVO NO CORRIENTE', 'nivel' => 2, 'tipo' => 'PASIVO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => '2', 'permite_movimiento' => false],

            // NIVEL 3: PASIVO CORRIENTE
            ['codigo' => '2.1.1', 'nombre' => 'OBLIGACIONES COMERCIALES', 'nivel' => 3, 'tipo' => 'PASIVO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => '2.1', 'permite_movimiento' => false],
            ['codigo' => '2.1.2', 'nombre' => 'OBLIGACIONES FISCALES Y TRIBUTARIAS', 'nivel' => 3, 'tipo' => 'PASIVO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => '2.1', 'permite_movimiento' => false],
            ['codigo' => '2.1.3', 'nombre' => 'OBLIGACIONES LABORALES Y SOCIALES', 'nivel' => 3, 'tipo' => 'PASIVO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => '2.1', 'permite_movimiento' => false],

            // NIVEL 4: CUENTAS IMPUTABLES PASIVO CORRIENTE
            ['codigo' => '2.1.1.01', 'nombre' => 'Cuentas por Pagar Proveedores', 'nivel' => 4, 'tipo' => 'PASIVO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => '2.1.1', 'permite_movimiento' => true],
            ['codigo' => '2.1.1.02', 'nombre' => 'Documentos por Pagar', 'nivel' => 4, 'tipo' => 'PASIVO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => '2.1.1', 'permite_movimiento' => true],
            ['codigo' => '2.1.2.01', 'nombre' => 'Débito Fiscal IVA (13%)', 'nivel' => 4, 'tipo' => 'PASIVO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => '2.1.2', 'permite_movimiento' => true],
            ['codigo' => '2.1.2.02', 'nombre' => 'Impuesto a las Transacciones por Pagar (3%)', 'nivel' => 4, 'tipo' => 'PASIVO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => '2.1.2', 'permite_movimiento' => true],
            ['codigo' => '2.1.2.03', 'nombre' => 'IUE por Pagar (25%)', 'nivel' => 4, 'tipo' => 'PASIVO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => '2.1.2', 'permite_movimiento' => true],
            ['codigo' => '2.1.2.04', 'nombre' => 'Retenciones IUE por Pagar', 'nivel' => 4, 'tipo' => 'PASIVO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => '2.1.2', 'permite_movimiento' => true],
            ['codigo' => '2.1.2.05', 'nombre' => 'Retenciones IT por Pagar', 'nivel' => 4, 'tipo' => 'PASIVO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => '2.1.2', 'permite_movimiento' => true],
            ['codigo' => '2.1.3.01', 'nombre' => 'Sueldos y Salarios por Pagar', 'nivel' => 4, 'tipo' => 'PASIVO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => '2.1.3', 'permite_movimiento' => true],
            ['codigo' => '2.1.3.02', 'nombre' => 'Aportes y Retenciones Patronales por Pagar', 'nivel' => 4, 'tipo' => 'PASIVO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => '2.1.3', 'permite_movimiento' => true],

            // NIVEL 3 Y 4: PASIVO NO CORRIENTE
            ['codigo' => '2.2.1', 'nombre' => 'OBLIGACIONES A LARGO PLAZO', 'nivel' => 3, 'tipo' => 'PASIVO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => '2.2', 'permite_movimiento' => false],
            ['codigo' => '2.2.1.01', 'nombre' => 'Préstamos Bancarios a Largo Plazo', 'nivel' => 4, 'tipo' => 'PASIVO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => '2.2.1', 'permite_movimiento' => true],

            // ==================== NIVEL 2: PATRIMONIO ====================
            ['codigo' => '3.1', 'nombre' => 'CAPITAL Y RESERVAS', 'nivel' => 2, 'tipo' => 'PATRIMONIO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => '3', 'permite_movimiento' => false],
            ['codigo' => '3.2', 'nombre' => 'RESULTADOS', 'nivel' => 2, 'tipo' => 'PATRIMONIO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => '3', 'permite_movimiento' => false],

            // NIVEL 3 Y 4: PATRIMONIO
            ['codigo' => '3.1.1', 'nombre' => 'CAPITAL SOCIAL Y RESERVAS', 'nivel' => 3, 'tipo' => 'PATRIMONIO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => '3.1', 'permite_movimiento' => false],
            ['codigo' => '3.1.1.01', 'nombre' => 'Capital Social', 'nivel' => 4, 'tipo' => 'PATRIMONIO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => '3.1.1', 'permite_movimiento' => true],
            ['codigo' => '3.1.1.02', 'nombre' => 'Ajuste de Capital', 'nivel' => 4, 'tipo' => 'PATRIMONIO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => '3.1.1', 'permite_movimiento' => true],
            ['codigo' => '3.1.1.03', 'nombre' => 'Reserva Legal', 'nivel' => 4, 'tipo' => 'PATRIMONIO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => '3.1.1', 'permite_movimiento' => true],

            ['codigo' => '3.2.1', 'nombre' => 'RESULTADOS DE GESTIÓN', 'nivel' => 3, 'tipo' => 'PATRIMONIO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => '3.2', 'permite_movimiento' => false],
            ['codigo' => '3.2.1.01', 'nombre' => 'Resultados Acumulados', 'nivel' => 4, 'tipo' => 'PATRIMONIO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => '3.2.1', 'permite_movimiento' => true],
            ['codigo' => '3.2.1.02', 'nombre' => 'Resultado del Ejercicio', 'nivel' => 4, 'tipo' => 'PATRIMONIO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => '3.2.1', 'permite_movimiento' => true],

            // ==================== NIVEL 2: INGRESOS ====================
            ['codigo' => '4.1', 'nombre' => 'INGRESOS OPERATIVOS', 'nivel' => 2, 'tipo' => 'INGRESO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => '4', 'permite_movimiento' => false],
            ['codigo' => '4.2', 'nombre' => 'OTROS INGRESOS', 'nivel' => 2, 'tipo' => 'INGRESO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => '4', 'permite_movimiento' => false],

            ['codigo' => '4.1.1', 'nombre' => 'VENTAS NETAS', 'nivel' => 3, 'tipo' => 'INGRESO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => '4.1', 'permite_movimiento' => false],
            ['codigo' => '4.1.1.01', 'nombre' => 'Ventas de Mercaderías (87%)', 'nivel' => 4, 'tipo' => 'INGRESO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => '4.1.1', 'permite_movimiento' => true],

            ['codigo' => '4.2.1', 'nombre' => 'OTROS INGRESOS OPERATIVOS', 'nivel' => 3, 'tipo' => 'INGRESO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => '4.2', 'permite_movimiento' => false],
            ['codigo' => '4.2.1.01', 'nombre' => 'Ingresos por Ajuste de Inventario', 'nivel' => 4, 'tipo' => 'INGRESO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => '4.2.1', 'permite_movimiento' => true],
            ['codigo' => '4.2.1.02', 'nombre' => 'Descuentos Obtenidos en Compras', 'nivel' => 4, 'tipo' => 'INGRESO', 'naturaleza' => 'ACREEDORA', 'padre_codigo' => '4.2.1', 'permite_movimiento' => true],

            // ==================== NIVEL 2: COSTOS ====================
            ['codigo' => '5.1', 'nombre' => 'COSTO DE VENTAS', 'nivel' => 2, 'tipo' => 'COSTO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '5', 'permite_movimiento' => false],
            ['codigo' => '5.1.1', 'nombre' => 'COSTO DE MERCADERÍAS VENDIDAS', 'nivel' => 3, 'tipo' => 'COSTO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '5.1', 'permite_movimiento' => false],
            ['codigo' => '5.1.1.01', 'nombre' => 'Costo de Mercaderías Vendidas', 'nivel' => 4, 'tipo' => 'COSTO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '5.1.1', 'permite_movimiento' => true],

            // ==================== NIVEL 2: GASTOS ====================
            ['codigo' => '6.1', 'nombre' => 'GASTOS DE ADMINISTRACIÓN Y OPERACIÓN', 'nivel' => 2, 'tipo' => 'GASTO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '6', 'permite_movimiento' => false],
            ['codigo' => '6.2', 'nombre' => 'GASTOS DE COMERCIALIZACIÓN Y VENTAS', 'nivel' => 2, 'tipo' => 'GASTO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '6', 'permite_movimiento' => false],
            ['codigo' => '6.3', 'nombre' => 'OTROS GASTOS Y PÉRDIDAS', 'nivel' => 2, 'tipo' => 'GASTO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '6', 'permite_movimiento' => false],

            ['codigo' => '6.1.1', 'nombre' => 'GASTOS ADMINISTRATIVOS GENERALES', 'nivel' => 3, 'tipo' => 'GASTO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '6.1', 'permite_movimiento' => false],
            ['codigo' => '6.1.1.01', 'nombre' => 'Impuesto a las Transacciones (3%)', 'nivel' => 4, 'tipo' => 'GASTO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '6.1.1', 'permite_movimiento' => true],
            ['codigo' => '6.1.1.02', 'nombre' => 'Sueldos y Salarios', 'nivel' => 4, 'tipo' => 'GASTO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '6.1.1', 'permite_movimiento' => true],
            ['codigo' => '6.1.1.03', 'nombre' => 'Servicios Básicos (Agua, Luz, Teléfono)', 'nivel' => 4, 'tipo' => 'GASTO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '6.1.1', 'permite_movimiento' => true],
            ['codigo' => '6.1.1.04', 'nombre' => 'Alquileres Pagados', 'nivel' => 4, 'tipo' => 'GASTO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '6.1.1', 'permite_movimiento' => true],
            ['codigo' => '6.1.1.05', 'nombre' => 'Depreciación Bienes de Uso', 'nivel' => 4, 'tipo' => 'GASTO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '6.1.1', 'permite_movimiento' => true],

            ['codigo' => '6.2.1', 'nombre' => 'GASTOS DE VENTAS', 'nivel' => 3, 'tipo' => 'GASTO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '6.2', 'permite_movimiento' => false],
            ['codigo' => '6.2.1.01', 'nombre' => 'Fletes y Transporte en Ventas', 'nivel' => 4, 'tipo' => 'GASTO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '6.2.1', 'permite_movimiento' => true],
            ['codigo' => '6.2.1.02', 'nombre' => 'Publicidad y Propaganda', 'nivel' => 4, 'tipo' => 'GASTO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '6.2.1', 'permite_movimiento' => true],

            ['codigo' => '6.3.1', 'nombre' => 'PÉRDIDAS OPERATIVAS Y AJUSTES', 'nivel' => 3, 'tipo' => 'GASTO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '6.3', 'permite_movimiento' => false],
            ['codigo' => '6.3.1.01', 'nombre' => 'Pérdida por Mermas y Ajustes de Inventario', 'nivel' => 4, 'tipo' => 'GASTO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '6.3.1', 'permite_movimiento' => true],
            ['codigo' => '6.3.1.02', 'nombre' => 'Mantenimiento y Reparaciones', 'nivel' => 4, 'tipo' => 'GASTO', 'naturaleza' => 'DEUDORA', 'padre_codigo' => '6.3.1', 'permite_movimiento' => true],
        ];

        // Se insertan respetando la jerarquía para asignar padre_id correctamente
        $cuentasCache = [];

        foreach ($cuentas as $data) {
            $padreId = null;
            if (! empty($data['padre_codigo']) && isset($cuentasCache[$data['padre_codigo']])) {
                $padreId = $cuentasCache[$data['padre_codigo']];
            }

            $cuenta = CuentaContable::updateOrCreate(
                ['codigo' => $data['codigo']],
                [
                    'nombre' => $data['nombre'],
                    'nivel' => $data['nivel'],
                    'tipo' => $data['tipo'],
                    'naturaleza' => $data['naturaleza'],
                    'padre_id' => $padreId,
                    'permite_movimiento' => $data['permite_movimiento'],
                    'activo' => true,
                ]
            );

            $cuentasCache[$data['codigo']] = $cuenta->id;
        }
    }
}
