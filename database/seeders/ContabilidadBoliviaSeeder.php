<?php

namespace Database\Seeders;

use App\Models\CuentaContable;
use App\Models\DetalleModeloContable;
use App\Models\ModeloContable;
use App\Models\TipoComprobante;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContabilidadBoliviaSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $this->seedTiposComprobante();
            $this->seedPlanDeCuentas();
            $this->seedModelosContables();
        });
    }

    private function seedTiposComprobante(): void
    {
        $tipos = [
            [
                'codigo' => 'ING',
                'nombre' => 'Comprobante de Ingreso',
                'descripcion' => 'Registra las entradas reales y efectivas de fondos a Caja o Bancos.',
                'activo' => true,
            ],
            [
                'codigo' => 'EGR',
                'nombre' => 'Comprobante de Egreso',
                'descripcion' => 'Registra las salidas reales de fondos por desembolsos o pagos mediante caja o banco.',
                'activo' => true,
            ],
            [
                'codigo' => 'TRS',
                'nombre' => 'Comprobante de Traspaso',
                'descripcion' => 'Registra transacciones devengadas que no mueven efectivo de inmediato (ventas a crédito, compras a crédito, costo de ventas, ajustes).',
                'activo' => true,
            ],
            [
                'codigo' => 'APE',
                'nombre' => 'Comprobante de Apertura',
                'descripcion' => 'Registra el balance inicial de apertura o reapertura de gestión contable.',
                'activo' => true,
            ],
        ];

        foreach ($tipos as $tipo) {
            TipoComprobante::updateOrCreate(['codigo' => $tipo['codigo']], $tipo);
        }
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

    private function seedModelosContables(): void
    {
        $tipoIngreso = TipoComprobante::where('codigo', 'ING')->firstOrFail();
        $tipoEgreso = TipoComprobante::where('codigo', 'EGR')->firstOrFail();
        $tipoTraspaso = TipoComprobante::where('codigo', 'TRS')->firstOrFail();

        $caja = CuentaContable::where('codigo', '1.1.1.01')->firstOrFail();
        $bancos = CuentaContable::where('codigo', '1.1.1.02')->firstOrFail();
        $cxc = CuentaContable::where('codigo', '1.1.2.01')->firstOrFail();
        $creditoFiscal = CuentaContable::where('codigo', '1.1.2.03')->firstOrFail();
        $inventario = CuentaContable::where('codigo', '1.1.3.01')->firstOrFail();

        $cxp = CuentaContable::where('codigo', '2.1.1.01')->firstOrFail();
        $debitoFiscal = CuentaContable::where('codigo', '2.1.2.01')->firstOrFail();
        $itPorPagar = CuentaContable::where('codigo', '2.1.2.02')->firstOrFail();

        $ventasNetas = CuentaContable::where('codigo', '4.1.1.01')->firstOrFail();
        $ajusteInvIngreso = CuentaContable::where('codigo', '4.2.1.01')->firstOrFail();

        $costoVentas = CuentaContable::where('codigo', '5.1.1.01')->firstOrFail();

        $itGasto = CuentaContable::where('codigo', '6.1.1.01')->firstOrFail();
        $mermaGasto = CuentaContable::where('codigo', '6.3.1.01')->firstOrFail();

        // 1. VENTA AL CONTADO (Normativa Bolivia: 100% Caja, 3% IT Gasto | 87% Venta, 13% Débito Fiscal, 3% IT por Pagar)
        $modeloVentaContado = ModeloContable::updateOrCreate(
            ['codigo' => 'VENTA_CONTADO'],
            [
                'nombre' => 'Venta de Mercaderías al Contado (Ley 843)',
                'descripcion' => 'Genera asiento de ingreso por venta al contado aplicando 87% valor neto, 13% Débito Fiscal IVA y 3% Impuesto a las Transacciones.',
                'tipo_comprobante_id' => $tipoIngreso->id,
                'activo' => true,
            ]
        );

        $modeloVentaContado->detalles()->delete();
        $modeloVentaContado->detalles()->createMany([
            ['cuenta_contable_id' => $caja->id, 'lado' => 'DEBE', 'porcentaje' => 100.00, 'tipo_monto' => 'TOTAL', 'glosa_defecto' => 'Ingreso a caja por venta al contado'],
            ['cuenta_contable_id' => $itGasto->id, 'lado' => 'DEBE', 'porcentaje' => 3.00, 'tipo_monto' => 'TOTAL', 'glosa_defecto' => 'Impuesto a las transacciones (3%)'],
            ['cuenta_contable_id' => $ventasNetas->id, 'lado' => 'HABER', 'porcentaje' => 87.00, 'tipo_monto' => 'TOTAL', 'glosa_defecto' => 'Ventas de mercaderías netas (87%)'],
            ['cuenta_contable_id' => $debitoFiscal->id, 'lado' => 'HABER', 'porcentaje' => 13.00, 'tipo_monto' => 'TOTAL', 'glosa_defecto' => 'Débito Fiscal IVA (13%)'],
            ['cuenta_contable_id' => $itPorPagar->id, 'lado' => 'HABER', 'porcentaje' => 3.00, 'tipo_monto' => 'TOTAL', 'glosa_defecto' => 'Impuesto a las transacciones por pagar (3%)'],
        ]);

        // 2. VENTA A CRÉDITO (Normativa Bolivia: 100% CxC, 3% IT Gasto | 87% Venta, 13% Débito Fiscal, 3% IT por Pagar)
        $modeloVentaCredito = ModeloContable::updateOrCreate(
            ['codigo' => 'VENTA_CREDITO'],
            [
                'nombre' => 'Venta de Mercaderías a Crédito (Ley 843)',
                'descripcion' => 'Genera comprobante de traspaso reconociendo la cuenta por cobrar, devengando 87% venta, 13% Débito Fiscal y 3% IT.',
                'tipo_comprobante_id' => $tipoTraspaso->id,
                'activo' => true,
            ]
        );

        $modeloVentaCredito->detalles()->delete();
        $modeloVentaCredito->detalles()->createMany([
            ['cuenta_contable_id' => $cxc->id, 'lado' => 'DEBE', 'porcentaje' => 100.00, 'tipo_monto' => 'TOTAL', 'glosa_defecto' => 'Cuentas por cobrar comerciales cliente'],
            ['cuenta_contable_id' => $itGasto->id, 'lado' => 'DEBE', 'porcentaje' => 3.00, 'tipo_monto' => 'TOTAL', 'glosa_defecto' => 'Impuesto a las transacciones (3%)'],
            ['cuenta_contable_id' => $ventasNetas->id, 'lado' => 'HABER', 'porcentaje' => 87.00, 'tipo_monto' => 'TOTAL', 'glosa_defecto' => 'Ventas de mercaderías netas (87%)'],
            ['cuenta_contable_id' => $debitoFiscal->id, 'lado' => 'HABER', 'porcentaje' => 13.00, 'tipo_monto' => 'TOTAL', 'glosa_defecto' => 'Débito Fiscal IVA (13%)'],
            ['cuenta_contable_id' => $itPorPagar->id, 'lado' => 'HABER', 'porcentaje' => 3.00, 'tipo_monto' => 'TOTAL', 'glosa_defecto' => 'Impuesto a las transacciones por pagar (3%)'],
        ]);

        // 3. COSTO DE VENTAS (Devengamiento del inventario al Costo Promedio Ponderado CPP)
        $modeloCostoVentas = ModeloContable::updateOrCreate(
            ['codigo' => 'COSTO_VENTAS'],
            [
                'nombre' => 'Costo de Mercaderías Vendidas (CPP)',
                'descripcion' => 'Genera comprobante de traspaso registrando el costo de ventas y descargando el almacén al Costo Promedio Ponderado.',
                'tipo_comprobante_id' => $tipoTraspaso->id,
                'activo' => true,
            ]
        );

        $modeloCostoVentas->detalles()->delete();
        $modeloCostoVentas->detalles()->createMany([
            ['cuenta_contable_id' => $costoVentas->id, 'lado' => 'DEBE', 'porcentaje' => 100.00, 'tipo_monto' => 'COSTO', 'glosa_defecto' => 'Costo de mercaderías vendidas'],
            ['cuenta_contable_id' => $inventario->id, 'lado' => 'HABER', 'porcentaje' => 100.00, 'tipo_monto' => 'COSTO', 'glosa_defecto' => 'Salida de almacén valuada al CPP'],
        ]);

        // 4. COMPRA DE MERCADERÍAS AL CONTADO (87% Inventario neto, 13% Crédito Fiscal | 100% Caja/Banco)
        $modeloCompraContado = ModeloContable::updateOrCreate(
            ['codigo' => 'COMPRA_CONTADO'],
            [
                'nombre' => 'Compra de Mercaderías al Contado (Ley 843)',
                'descripcion' => 'Genera comprobante de egreso ingresando el 87% a inventarios y el 13% a Crédito Fiscal IVA contra desembolso de Caja/Bancos.',
                'tipo_comprobante_id' => $tipoEgreso->id,
                'activo' => true,
            ]
        );

        $modeloCompraContado->detalles()->delete();
        $modeloCompraContado->detalles()->createMany([
            ['cuenta_contable_id' => $inventario->id, 'lado' => 'DEBE', 'porcentaje' => 87.00, 'tipo_monto' => 'TOTAL', 'glosa_defecto' => 'Ingreso de mercaderías al almacén (87% neto)'],
            ['cuenta_contable_id' => $creditoFiscal->id, 'lado' => 'DEBE', 'porcentaje' => 13.00, 'tipo_monto' => 'TOTAL', 'glosa_defecto' => 'Crédito Fiscal IVA (13%) s/ factura proveedor'],
            ['cuenta_contable_id' => $caja->id, 'lado' => 'HABER', 'porcentaje' => 100.00, 'tipo_monto' => 'TOTAL', 'glosa_defecto' => 'Desembolso de efectivo por compra al contado'],
        ]);

        // 5. COMPRA DE MERCADERÍAS A CRÉDITO (87% Inventario neto, 13% Crédito Fiscal | 100% Cuentas por Pagar)
        $modeloCompraCredito = ModeloContable::updateOrCreate(
            ['codigo' => 'COMPRA_CREDITO'],
            [
                'nombre' => 'Compra de Mercaderías a Crédito (Ley 843)',
                'descripcion' => 'Genera comprobante de traspaso ingresando el 87% a inventarios y el 13% a Crédito Fiscal IVA contra pasivo a proveedores.',
                'tipo_comprobante_id' => $tipoTraspaso->id,
                'activo' => true,
            ]
        );

        $modeloCompraCredito->detalles()->delete();
        $modeloCompraCredito->detalles()->createMany([
            ['cuenta_contable_id' => $inventario->id, 'lado' => 'DEBE', 'porcentaje' => 87.00, 'tipo_monto' => 'TOTAL', 'glosa_defecto' => 'Ingreso de mercaderías al almacén (87% neto)'],
            ['cuenta_contable_id' => $creditoFiscal->id, 'lado' => 'DEBE', 'porcentaje' => 13.00, 'tipo_monto' => 'TOTAL', 'glosa_defecto' => 'Crédito Fiscal IVA (13%) s/ factura proveedor'],
            ['cuenta_contable_id' => $cxp->id, 'lado' => 'HABER', 'porcentaje' => 100.00, 'tipo_monto' => 'TOTAL', 'glosa_defecto' => 'Obligación con proveedor a crédito'],
        ]);

        // 6. COBRO DE VENTAS / AMORTIZACIÓN DE CUOTAS
        $modeloCobroVenta = ModeloContable::updateOrCreate(
            ['codigo' => 'COBRO_VENTA'],
            [
                'nombre' => 'Cobranza de Clientes / Cancelación de Cuotas',
                'descripcion' => 'Genera comprobante de ingreso cancelando la cuenta por cobrar contra entrada efectiva de fondos a Caja/Bancos.',
                'tipo_comprobante_id' => $tipoIngreso->id,
                'activo' => true,
            ]
        );

        $modeloCobroVenta->detalles()->delete();
        $modeloCobroVenta->detalles()->createMany([
            ['cuenta_contable_id' => $caja->id, 'lado' => 'DEBE', 'porcentaje' => 100.00, 'tipo_monto' => 'TOTAL', 'glosa_defecto' => 'Ingreso de fondos por cobranza'],
            ['cuenta_contable_id' => $cxc->id, 'lado' => 'HABER', 'porcentaje' => 100.00, 'tipo_monto' => 'TOTAL', 'glosa_defecto' => 'Cancelación de cuenta por cobrar del cliente'],
        ]);

        // 7. AJUSTE DE INVENTARIO - MERMA O FALTANTE
        $modeloAjusteMerma = ModeloContable::updateOrCreate(
            ['codigo' => 'AJUSTE_INVENTARIO_MERMA'],
            [
                'nombre' => 'Ajuste de Inventario por Merma o Faltante',
                'descripcion' => 'Genera comprobante de traspaso registrando la pérdida contra la baja física en inventarios valuada al CPP.',
                'tipo_comprobante_id' => $tipoTraspaso->id,
                'activo' => true,
            ]
        );

        $modeloAjusteMerma->detalles()->delete();
        $modeloAjusteMerma->detalles()->createMany([
            ['cuenta_contable_id' => $mermaGasto->id, 'lado' => 'DEBE', 'porcentaje' => 100.00, 'tipo_monto' => 'COSTO', 'glosa_defecto' => 'Pérdida por ajuste / merma de inventario'],
            ['cuenta_contable_id' => $inventario->id, 'lado' => 'HABER', 'porcentaje' => 100.00, 'tipo_monto' => 'COSTO', 'glosa_defecto' => 'Baja de inventario por ajuste'],
        ]);

        // 8. AJUSTE DE INVENTARIO - SOBRANTE
        $modeloAjusteSobrante = ModeloContable::updateOrCreate(
            ['codigo' => 'AJUSTE_INVENTARIO_SOBRANTE'],
            [
                'nombre' => 'Ajuste de Inventario por Sobrante',
                'descripcion' => 'Genera comprobante de traspaso registrando el alta en almacén contra otros ingresos operativos.',
                'tipo_comprobante_id' => $tipoTraspaso->id,
                'activo' => true,
            ]
        );

        $modeloAjusteSobrante->detalles()->delete();
        $modeloAjusteSobrante->detalles()->createMany([
            ['cuenta_contable_id' => $inventario->id, 'lado' => 'DEBE', 'porcentaje' => 100.00, 'tipo_monto' => 'COSTO', 'glosa_defecto' => 'Alta de inventario por sobrante'],
            ['cuenta_contable_id' => $ajusteInvIngreso->id, 'lado' => 'HABER', 'porcentaje' => 100.00, 'tipo_monto' => 'COSTO', 'glosa_defecto' => 'Ingreso extraordinario por sobrante de inventario'],
        ]);
    }
}
