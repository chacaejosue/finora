<?php

namespace App\Services;

use App\Models\Asiento;
use App\Models\Compra;
use App\Models\CuentaContable;
use App\Models\DetalleAsiento;
use App\Models\MovimientoInventario;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ContabilidadService
{
    /**
     * Métodos de pago considerados bancarios/electrónicos.
     */
    private const METODOS_BANCARIOS = [
        'QR',
        'TRANSFERENCIA',
        'TARJETA',
        'BANCO',
        'CHEQUE',
        'DEPOSITO',
        'LINEA',
    ];

    /**
     * Determina si un método de pago es bancario o electrónico.
     */
    public function esMetodoBancario(?string $metodo): bool
    {
        if (empty($metodo)) {
            return false;
        }

        return in_array(strtoupper(trim($metodo)), self::METODOS_BANCARIOS, true);
    }

    /**
     * Devuelve las plantillas contables parametrizadas bajo normativa boliviana (Ley 843).
     */
    public function obtenerModelosContables(): Collection
    {
        if (CuentaContable::count() === 0) {
            (new \Database\Seeders\ContabilidadBoliviaSeeder())->run();
        }

        $cuentas = CuentaContable::all()->keyBy('codigo');

        $definiciones = [
            [
                'codigo' => 'VENTA_CONTADO',
                'nombre' => 'Venta de Mercaderías al Contado (Ley 843)',
                'descripcion' => 'Genera asiento de ingreso por venta al contado aplicando 87% valor neto, 13% Débito Fiscal IVA y 3% Impuesto a las Transacciones.',
                'tipo_comprobante' => 'ING',
                'detalles' => [
                    ['cuenta_codigo' => '1.1.1.01', 'lado' => 'DEBE', 'porcentaje' => 100.00, 'tipo_monto' => 'TOTAL', 'glosa_defecto' => 'Ingreso a caja por venta al contado'],
                    ['cuenta_codigo' => '6.1.1.01', 'lado' => 'DEBE', 'porcentaje' => 3.00, 'tipo_monto' => 'TOTAL', 'glosa_defecto' => 'Impuesto a las transacciones (3%)'],
                    ['cuenta_codigo' => '4.1.1.01', 'lado' => 'HABER', 'porcentaje' => 87.00, 'tipo_monto' => 'TOTAL', 'glosa_defecto' => 'Ventas de mercaderías netas (87%)'],
                    ['cuenta_codigo' => '2.1.2.01', 'lado' => 'HABER', 'porcentaje' => 13.00, 'tipo_monto' => 'TOTAL', 'glosa_defecto' => 'Débito Fiscal IVA (13%)'],
                    ['cuenta_codigo' => '2.1.2.02', 'lado' => 'HABER', 'porcentaje' => 3.00, 'tipo_monto' => 'TOTAL', 'glosa_defecto' => 'Impuesto a las transacciones por pagar (3%)'],
                ],
            ],
            [
                'codigo' => 'VENTA_CREDITO',
                'nombre' => 'Venta de Mercaderías a Crédito (Ley 843)',
                'descripcion' => 'Genera comprobante de traspaso reconociendo la cuenta por cobrar, devengando 87% venta, 13% Débito Fiscal y 3% IT.',
                'tipo_comprobante' => 'TRS',
                'detalles' => [
                    ['cuenta_codigo' => '1.1.2.01', 'lado' => 'DEBE', 'porcentaje' => 100.00, 'tipo_monto' => 'TOTAL', 'glosa_defecto' => 'Cuentas por cobrar comerciales cliente'],
                    ['cuenta_codigo' => '6.1.1.01', 'lado' => 'DEBE', 'porcentaje' => 3.00, 'tipo_monto' => 'TOTAL', 'glosa_defecto' => 'Impuesto a las transacciones (3%)'],
                    ['cuenta_codigo' => '4.1.1.01', 'lado' => 'HABER', 'porcentaje' => 87.00, 'tipo_monto' => 'TOTAL', 'glosa_defecto' => 'Ventas de mercaderías netas (87%)'],
                    ['cuenta_codigo' => '2.1.2.01', 'lado' => 'HABER', 'porcentaje' => 13.00, 'tipo_monto' => 'TOTAL', 'glosa_defecto' => 'Débito Fiscal IVA (13%)'],
                    ['cuenta_codigo' => '2.1.2.02', 'lado' => 'HABER', 'porcentaje' => 3.00, 'tipo_monto' => 'TOTAL', 'glosa_defecto' => 'Impuesto a las transacciones por pagar (3%)'],
                ],
            ],
            [
                'codigo' => 'COSTO_VENTAS',
                'nombre' => 'Costo de Mercaderías Vendidas (CPP)',
                'descripcion' => 'Genera comprobante de traspaso registrando el costo de ventas y descargando el almacén al Costo Promedio Ponderado.',
                'tipo_comprobante' => 'TRS',
                'detalles' => [
                    ['cuenta_codigo' => '5.1.1.01', 'lado' => 'DEBE', 'porcentaje' => 100.00, 'tipo_monto' => 'COSTO', 'glosa_defecto' => 'Costo de mercaderías vendidas'],
                    ['cuenta_codigo' => '1.1.3.01', 'lado' => 'HABER', 'porcentaje' => 100.00, 'tipo_monto' => 'COSTO', 'glosa_defecto' => 'Salida de almacén valuada al CPP'],
                ],
            ],
            [
                'codigo' => 'COMPRA_CONTADO',
                'nombre' => 'Compra de Mercaderías al Contado (Ley 843)',
                'descripcion' => 'Genera comprobante de egreso ingresando el 87% a inventarios y el 13% a Crédito Fiscal IVA contra desembolso de Caja/Bancos.',
                'tipo_comprobante' => 'EGR',
                'detalles' => [
                    ['cuenta_codigo' => '1.1.3.01', 'lado' => 'DEBE', 'porcentaje' => 87.00, 'tipo_monto' => 'TOTAL', 'glosa_defecto' => 'Ingreso de mercaderías al almacén (87% neto)'],
                    ['cuenta_codigo' => '1.1.2.03', 'lado' => 'DEBE', 'porcentaje' => 13.00, 'tipo_monto' => 'TOTAL', 'glosa_defecto' => 'Crédito Fiscal IVA (13%) s/ factura proveedor'],
                    ['cuenta_codigo' => '1.1.1.01', 'lado' => 'HABER', 'porcentaje' => 100.00, 'tipo_monto' => 'TOTAL', 'glosa_defecto' => 'Desembolso de efectivo por compra al contado'],
                ],
            ],
            [
                'codigo' => 'COMPRA_CREDITO',
                'nombre' => 'Compra de Mercaderías a Crédito (Ley 843)',
                'descripcion' => 'Genera comprobante de traspaso ingresando el 87% a inventarios y el 13% a Crédito Fiscal IVA contra pasivo a proveedores.',
                'tipo_comprobante' => 'TRS',
                'detalles' => [
                    ['cuenta_codigo' => '1.1.3.01', 'lado' => 'DEBE', 'porcentaje' => 87.00, 'tipo_monto' => 'TOTAL', 'glosa_defecto' => 'Ingreso de mercaderías al almacén (87% neto)'],
                    ['cuenta_codigo' => '1.1.2.03', 'lado' => 'DEBE', 'porcentaje' => 13.00, 'tipo_monto' => 'TOTAL', 'glosa_defecto' => 'Crédito Fiscal IVA (13%) s/ factura proveedor'],
                    ['cuenta_codigo' => '2.1.1.01', 'lado' => 'HABER', 'porcentaje' => 100.00, 'tipo_monto' => 'TOTAL', 'glosa_defecto' => 'Obligación con proveedor a crédito'],
                ],
            ],
            [
                'codigo' => 'COBRO_VENTA',
                'nombre' => 'Cobranza de Clientes / Cancelación de Cuotas',
                'descripcion' => 'Genera comprobante de ingreso cancelando la cuenta por cobrar contra entrada efectiva de fondos a Caja/Bancos.',
                'tipo_comprobante' => 'ING',
                'detalles' => [
                    ['cuenta_codigo' => '1.1.1.01', 'lado' => 'DEBE', 'porcentaje' => 100.00, 'tipo_monto' => 'TOTAL', 'glosa_defecto' => 'Ingreso de fondos por cobranza'],
                    ['cuenta_codigo' => '1.1.2.01', 'lado' => 'HABER', 'porcentaje' => 100.00, 'tipo_monto' => 'TOTAL', 'glosa_defecto' => 'Cancelación de cuenta por cobrar del cliente'],
                ],
            ],
            [
                'codigo' => 'AJUSTE_INVENTARIO_MERMA',
                'nombre' => 'Ajuste de Inventario por Merma o Faltante',
                'descripcion' => 'Genera comprobante de traspaso registrando la pérdida contra la baja física en inventarios valuada al CPP.',
                'tipo_comprobante' => 'TRS',
                'detalles' => [
                    ['cuenta_codigo' => '6.3.1.01', 'lado' => 'DEBE', 'porcentaje' => 100.00, 'tipo_monto' => 'COSTO', 'glosa_defecto' => 'Pérdida por ajuste / merma de inventario'],
                    ['cuenta_codigo' => '1.1.3.01', 'lado' => 'HABER', 'porcentaje' => 100.00, 'tipo_monto' => 'COSTO', 'glosa_defecto' => 'Baja de inventario por ajuste'],
                ],
            ],
            [
                'codigo' => 'AJUSTE_INVENTARIO_SOBRANTE',
                'nombre' => 'Ajuste de Inventario por Sobrante',
                'descripcion' => 'Genera comprobante de traspaso registrando el alta en almacén contra otros ingresos operativos.',
                'tipo_comprobante' => 'TRS',
                'detalles' => [
                    ['cuenta_codigo' => '1.1.3.01', 'lado' => 'DEBE', 'porcentaje' => 100.00, 'tipo_monto' => 'COSTO', 'glosa_defecto' => 'Alta de inventario por sobrante'],
                    ['cuenta_codigo' => '4.2.1.01', 'lado' => 'HABER', 'porcentaje' => 100.00, 'tipo_monto' => 'COSTO', 'glosa_defecto' => 'Ingreso extraordinario por sobrante de inventario'],
                ],
            ],
        ];

        return collect($definiciones)->map(function (array $def) use ($cuentas) {
            $detalles = collect($def['detalles'])->map(function (array $det) use ($cuentas) {
                $cuenta = $cuentas->get($det['cuenta_codigo']);

                return (object) [
                    'cuenta_contable_id' => $cuenta?->id,
                    'cuentaContable' => $cuenta,
                    'lado' => $det['lado'],
                    'porcentaje' => $det['porcentaje'],
                    'tipo_monto' => $det['tipo_monto'],
                    'glosa_defecto' => $det['glosa_defecto'],
                ];
            })->filter(fn (object $det) => $det->cuenta_contable_id !== null)->values();

            return (object) [
                'codigo' => $def['codigo'],
                'nombre' => $def['nombre'],
                'descripcion' => $def['descripcion'],
                'tipo_comprobante' => $def['tipo_comprobante'],
                'tipoComprobante' => Asiento::obtenerTipoComprobante($def['tipo_comprobante']),
                'detalles' => $detalles,
            ];
        });
    }

    /**
     * Genera el siguiente número correlativo de comprobante por año y tipo.
     * Ejemplo: CI-2026-00001, CE-2026-00001, CT-2026-00001, CA-2026-00001
     */
    public function generarNumeroComprobante(string $codigoTipo, ?string $fecha = null): string
    {
        $anio = $fecha ? Carbon::parse($fecha)->year : now()->year;
        $codigoTipo = strtoupper(trim($codigoTipo));

        $ultimoNumero = Asiento::whereYear('fecha', $anio)
            ->where('tipo_comprobante', $codigoTipo)
            ->where('numero', 'like', "C{$codigoTipo[0]}-{$anio}-%")
            ->orderByDesc('id')
            ->value('numero');

        $correlativo = 1;
        if ($ultimoNumero && preg_match('/-(\d+)$/', $ultimoNumero, $coincidencias)) {
            $correlativo = ((int) $coincidencias[1]) + 1;
        }

        $prefijo = 'C' . substr($codigoTipo, 0, 1); // CI, CE, CT, CA

        return sprintf('%s-%d-%05d', $prefijo, $anio, $correlativo);
    }

    /**
     * Genera un Asiento contable evaluando dinámicamente una plantilla contable parametrizada.
     * Aplica normativa de Bolivia y compensación de centavos por redondeo (Partida Doble exacta).
     */
    public function generarAsientoDesdeModelo(string $codigoModelo, float $montoTotal, array $opciones = []): ?Asiento
    {
        $modelo = $this->obtenerModelosContables()->firstWhere('codigo', $codigoModelo);

        if (! $modelo) {
            return null;
        }

        $fecha = $opciones['fecha'] ?? now()->toDateString();
        $glosa = $opciones['glosa'] ?? $modelo->nombre;
        $contabilizable = $opciones['contabilizable'] ?? null;
        $metodoPago = $opciones['metodo_pago'] ?? null;
        $montoCosto = (float) ($opciones['monto_costo'] ?? 0);
        $usuarioId = $opciones['usuario_id'] ?? (auth()->id() ?? null);

        // Pre-cargar cuentas de liquidez para intercambio dinámico Caja <-> Bancos
        $cuentaCaja = CuentaContable::where('codigo', '1.1.1.01')->first();
        $cuentaBancos = CuentaContable::where('codigo', '1.1.1.02')->first();
        $esBanco = $this->esMetodoBancario($metodoPago);

        $lineas = [];
        $totalDebe = 0.0;
        $totalHaber = 0.0;

        foreach ($modelo->detalles as $det) {
            $cuentaId = $det->cuenta_contable_id;

            // REGLA 1: Ajuste dinámico de cuenta de liquidez (Caja M/N vs Bancos M/N)
            if ($esBanco && $cuentaCaja && $cuentaBancos && $cuentaId === $cuentaCaja->id) {
                $cuentaId = $cuentaBancos->id;
            } elseif (! $esBanco && $cuentaCaja && $cuentaBancos && $cuentaId === $cuentaBancos->id && strtoupper((string) $metodoPago) === 'EFECTIVO') {
                $cuentaId = $cuentaCaja->id;
            }

            // Seleccionar monto base según tipo_monto
            $base = ($det->tipo_monto === 'COSTO') ? $montoCosto : $montoTotal;
            $montoCalculado = round($base * ((float) $det->porcentaje / 100), 2);

            if ($montoCalculado <= 0) {
                continue;
            }

            $debe = 0.0;
            $haber = 0.0;

            if (strtoupper($det->lado) === 'DEBE') {
                $debe = $montoCalculado;
                $totalDebe += $debe;
            } else {
                $haber = $montoCalculado;
                $totalHaber += $haber;
            }

            $lineas[] = [
                'cuenta_contable_id' => $cuentaId,
                'glosa_detalle' => $det->glosa_defecto ?: $glosa,
                'debe' => $debe,
                'haber' => $haber,
                'tipo_monto' => $det->tipo_monto,
                'porcentaje' => (float) $det->porcentaje,
            ];
        }

        // REGLA 2: Cuadre de Centavos por Redondeo (87% + 13% = 100% y Partida Doble al centavo)
        $diferencia = round($totalDebe - $totalHaber, 2);
        if (abs($diferencia) > 0 && abs($diferencia) <= 0.05 && count($lineas) > 0) {
            if ($diferencia > 0) {
                // El Debe es mayor que el Haber -> Ajustamos sumando la diferencia en la línea Haber de mayor valor
                $indiceMayorHaber = null;
                $mayorHaber = -1;
                foreach ($lineas as $idx => $linea) {
                    if ($linea['haber'] > $mayorHaber) {
                        $mayorHaber = $linea['haber'];
                        $indiceMayorHaber = $idx;
                    }
                }
                if ($indiceMayorHaber !== null) {
                    $lineas[$indiceMayorHaber]['haber'] = round($lineas[$indiceMayorHaber]['haber'] + $diferencia, 2);
                    $totalHaber = round($totalHaber + $diferencia, 2);
                }
            } else {
                // El Haber es mayor que el Debe -> Ajustamos sumando abs(diferencia) en la línea Debe de mayor valor
                $diffAbs = abs($diferencia);
                $indiceMayorDebe = null;
                $mayorDebe = -1;
                foreach ($lineas as $idx => $linea) {
                    if ($linea['debe'] > $mayorDebe) {
                        $mayorDebe = $linea['debe'];
                        $indiceMayorDebe = $idx;
                    }
                }
                if ($indiceMayorDebe !== null) {
                    $lineas[$indiceMayorDebe]['debe'] = round($lineas[$indiceMayorDebe]['debe'] + $diffAbs, 2);
                    $totalDebe = round($totalDebe + $diffAbs, 2);
                }
            }
        }

        $numeroComprobante = $this->generarNumeroComprobante($modelo->tipo_comprobante, $fecha);

        return DB::transaction(function () use ($modelo, $numeroComprobante, $fecha, $glosa, $contabilizable, $totalDebe, $totalHaber, $usuarioId, $lineas) {
            $asiento = Asiento::create([
                'tipo_comprobante' => $modelo->tipo_comprobante,
                'numero' => $numeroComprobante,
                'fecha' => $fecha,
                'glosa' => $glosa,
                'estado' => 'APROBADO',
                'contabilizable_type' => $contabilizable ? $contabilizable::class : null,
                'contabilizable_id' => $contabilizable?->getKey(),
                'total_debe' => $totalDebe,
                'total_haber' => $totalHaber,
                'usuario_id' => $usuarioId,
            ]);

            foreach ($lineas as $l) {
                DetalleAsiento::create([
                    'asiento_id' => $asiento->id,
                    'cuenta_contable_id' => $l['cuenta_contable_id'],
                    'glosa_detalle' => $l['glosa_detalle'],
                    'debe' => $l['debe'],
                    'haber' => $l['haber'],
                ]);
            }

            return $asiento;
        });
    }

    /**
     * Contabiliza una Venta confirmada:
     * 1. Asiento de Venta (87% Ingreso, 13% Débito Fiscal, 3% IT y 3% IT por pagar).
     * 2. Asiento de Costo de Ventas valuado al Costo Promedio Ponderado (CPP).
     */
    public function contabilizarVenta(Venta $venta): array
    {
        // Evitar duplicidad si ya tiene asiento de venta generado
        $yaContabilizada = $venta->asientos()
            ->where('glosa', 'like', "%Venta #{$venta->id}%")
            ->exists();

        if ($yaContabilizada) {
            return [];
        }

        $asientosGenerados = [];
        $esContado = ($venta->forma_pago === 'CONTADO');
        $codigoModelo = $esContado ? 'VENTA_CONTADO' : 'VENTA_CREDITO';
        $clienteNombre = $venta->cliente?->persona?->nombre_completo ?? 'Cliente General';

        $glosaVenta = sprintf(
            'Venta #%d [%s] - Cliente: %s - Total Bs %s',
            $venta->id,
            $venta->forma_pago,
            $clienteNombre,
            number_format($venta->total, 2)
        );

        // 1. Asiento de Venta
        $asientoVenta = $this->generarAsientoDesdeModelo(
            $codigoModelo,
            (float) $venta->total,
            [
                'fecha' => $venta->fecha ? Carbon::parse($venta->fecha)->toDateString() : now()->toDateString(),
                'glosa' => $glosaVenta,
                'contabilizable' => $venta,
                'metodo_pago' => $venta->metodo_pago,
                'usuario_id' => $venta->registrado_por_usuario_id,
            ]
        );
        if ($asientoVenta) {
            $asientosGenerados[] = $asientoVenta;
        }

        // 2. Asiento de Costo de Ventas (Devengamiento de Inventario al CPP)
        $costoTotalVenta = 0.0;
        $venta->loadMissing('detalles.producto');

        foreach ($venta->detalles as $detalle) {
            $costoUnitarioCpp = (float) $detalle->producto?->costo_promedio;

            // Inicialización de respaldo si el CPP aún no había sido calculado
            if ($costoUnitarioCpp <= 0) {
                $costoUnitarioCpp = (float) $detalle->costo_unitario;
            }

            $costoTotalVenta += round($detalle->cantidad * $costoUnitarioCpp, 2);
        }

        if ($costoTotalVenta > 0) {
            $glosaCosto = sprintf(
                'Costo de Ventas - Venta #%d - Descargo de inventario al CPP por Bs %s',
                $venta->id,
                number_format($costoTotalVenta, 2)
            );

            $asientoCosto = $this->generarAsientoDesdeModelo(
                'COSTO_VENTAS',
                0,
                [
                    'fecha' => $venta->fecha ? Carbon::parse($venta->fecha)->toDateString() : now()->toDateString(),
                    'glosa' => $glosaCosto,
                    'contabilizable' => $venta,
                    'monto_costo' => $costoTotalVenta,
                    'usuario_id' => $venta->registrado_por_usuario_id,
                ]
            );
            if ($asientoCosto) {
                $asientosGenerados[] = $asientoCosto;
            }
        }

        return $asientosGenerados;
    }

    /**
     * Procesa la recepción de una compra:
     * 1. Recalcula el Costo Promedio Ponderado (CPP) de cada producto al 87% neto.
     * 2. Genera el Asiento contable de compra (87% Inventario, 13% Crédito Fiscal).
     */
    public function procesarRecepcionCompra(Compra $compra): ?Asiento
    {
        // Evitar duplicidad si ya tiene asiento
        if ($compra->asientos()->exists()) {
            return null;
        }

        $compra->loadMissing(['detalles.producto', 'proveedor']);

        // REGLA 3: Recálculo de Costo Promedio Ponderado (CPP) al 87% neto
        foreach ($compra->detalles as $detalle) {
            $producto = $detalle->producto;
            if (! $producto) {
                continue;
            }

            // En CompraController, la compra pasa a RECIBIDA antes de crear los MovimientoInventario,
            // por lo que $producto->stock_fisico en este instante es exactamente el stock previo.
            $stockPrevio = (int) $producto->stock_fisico;
            $cantidadComprada = (int) $detalle->cantidad;

            // Costo neto sin impuestos (87% del costo unitario facturado)
            $costoUnitarioNeto = round((float) $detalle->costo_unitario * 0.87, 2);

            $cppPrevio = (float) $producto->costo_promedio;

            if ($stockPrevio <= 0 || $cppPrevio <= 0) {
                $nuevoCpp = $costoUnitarioNeto;
            } else {
                $valorStockPrevio = $stockPrevio * $cppPrevio;
                $valorCompraNueva = $cantidadComprada * $costoUnitarioNeto;
                $stockTotal = $stockPrevio + $cantidadComprada;

                $nuevoCpp = $stockTotal > 0
                    ? round(($valorStockPrevio + $valorCompraNueva) / $stockTotal, 2)
                    : $costoUnitarioNeto;
            }

            $producto->update(['costo_promedio' => $nuevoCpp]);
        }

        // Generar asiento contable de Compra
        $esContado = ($compra->forma_pago !== 'CREDITO');
        $codigoModelo = $esContado ? 'COMPRA_CONTADO' : 'COMPRA_CREDITO';
        $proveedorNombre = $compra->proveedor?->nombre ?? 'Proveedor';

        $glosa = sprintf(
            'Compra #%d [%s] - Proveedor: %s - Total Bs %s (87%% Inventario, 13%% Crédito Fiscal)',
            $compra->id,
            $compra->forma_pago,
            $proveedorNombre,
            number_format($compra->total, 2)
        );

        return $this->generarAsientoDesdeModelo(
            $codigoModelo,
            (float) $compra->total,
            [
                'fecha' => $compra->fecha_recepcion ? Carbon::parse($compra->fecha_recepcion)->toDateString() : now()->toDateString(),
                'glosa' => $glosa,
                'contabilizable' => $compra,
                'metodo_pago' => $esContado ? ($compra->metodo_pago ?: 'EFECTIVO') : null,
                'usuario_id' => $compra->registrado_por_usuario_id,
            ]
        );
    }

    /**
     * Contabiliza un cobro a cliente:
     * REGLA: Si la venta fue de CONTADO, se ignora para evitar duplicidad, ya que el ingreso
     * a Caja/Bancos fue reconocido directamente en el asiento de venta.
     */
    public function contabilizarPago(Pago $pago): ?Asiento
    {
        $pago->loadMissing('venta.cliente');
        $venta = $pago->venta;

        // Si la venta es de contado, no duplicar el asiento de cobro
        if ($venta && $venta->forma_pago === 'CONTADO') {
            return null;
        }

        // Evitar duplicidad si ya tiene asiento
        if ($pago->asientos()->exists()) {
            return null;
        }

        $clienteNombre = $venta?->cliente?->persona?->nombre_completo ?? 'Cliente';
        $glosa = sprintf(
            'Cobranza Pago #%d - Venta a Crédito #%d - Cliente: %s - Método: %s - Monto Bs %s',
            $pago->id,
            $pago->venta_id,
            $clienteNombre,
            $pago->metodo,
            number_format($pago->monto, 2)
        );

        return $this->generarAsientoDesdeModelo(
            'COBRO_VENTA',
            (float) $pago->monto,
            [
                'fecha' => $pago->fecha ? Carbon::parse($pago->fecha)->toDateString() : now()->toDateString(),
                'glosa' => $glosa,
                'contabilizable' => $pago,
                'metodo_pago' => $pago->metodo,
                'usuario_id' => $pago->registrado_por_usuario_id,
            ]
        );
    }

    /**
     * Contabiliza un ajuste manual de inventario (Merma / Sobrante).
     */
    public function contabilizarAjusteInventario(MovimientoInventario $movimiento): ?Asiento
    {
        // Ignorar movimientos que provienen de compras o ventas
        if ($movimiento->detalle_compra_id || $movimiento->detalle_venta_id) {
            return null;
        }

        if ($movimiento->asientos()->exists()) {
            return null;
        }

        $movimiento->loadMissing('producto');
        $producto = $movimiento->producto;
        if (! $producto) {
            return null;
        }

        $codigoModelo = ($movimiento->tipo === 'AJUSTE_ENTRADA')
            ? 'AJUSTE_INVENTARIO_SOBRANTE'
            : 'AJUSTE_INVENTARIO_MERMA';

        $costoBase = (float) $movimiento->costo_total;
        if ($costoBase <= 0) {
            $costoUnitario = (float) $producto->costo_promedio;
            $costoBase = round(abs($movimiento->cantidad) * $costoUnitario, 2);
        }

        if ($costoBase <= 0) {
            return null;
        }

        $glosa = sprintf(
            '%s Producto [%s] %s: Cantidad %d - Valuado al CPP en Bs %s. Obs: %s',
            $movimiento->tipo === 'AJUSTE_ENTRADA' ? 'Ajuste de Entrada (Sobrante)' : 'Ajuste de Salida (Merma)',
            $producto->codigo,
            $producto->nombre,
            abs($movimiento->cantidad),
            number_format($costoBase, 2),
            $movimiento->observacion ?: 'Sin observación'
        );

        return $this->generarAsientoDesdeModelo(
            $codigoModelo,
            0,
            [
                'fecha' => $movimiento->fecha ? Carbon::parse($movimiento->fecha)->toDateString() : now()->toDateString(),
                'glosa' => $glosa,
                'contabilizable' => $movimiento,
                'monto_costo' => $costoBase,
                'usuario_id' => $movimiento->registrado_por_usuario_id,
            ]
        );
    }

    /**
     * Revierte un Asiento contable generando el contra-asiento de reversión (invirtiendo Debe y Haber).
     * Garantiza que los mayores queden en cero matemáticamente bajo normativa de Bolivia.
     */
    public function revertirAsiento(Asiento $asientoOriginal, string $motivo, ?int $usuarioId = null): Asiento
    {
        $asientoOriginal->loadMissing(['detalles']);

        $codigoTipo = 'TRS';
        $numeroReversion = $this->generarNumeroComprobante($codigoTipo);
        $glosaReversion = sprintf(
            'REVERSIÓN / ANULACIÓN de Comprobante %s: %s',
            $asientoOriginal->numero,
            $motivo
        );

        return DB::transaction(function () use ($asientoOriginal, $codigoTipo, $numeroReversion, $glosaReversion, $usuarioId) {
            $asientoOriginal->update(['estado' => 'ANULADO']);

            $asientoReversion = Asiento::create([
                'tipo_comprobante' => $codigoTipo,
                'numero' => $numeroReversion,
                'fecha' => now()->toDateString(),
                'glosa' => $glosaReversion,
                'estado' => 'APROBADO',
                'contabilizable_type' => $asientoOriginal->contabilizable_type,
                'contabilizable_id' => $asientoOriginal->contabilizable_id,
                'total_debe' => $asientoOriginal->total_haber,
                'total_haber' => $asientoOriginal->total_debe,
                'usuario_id' => $usuarioId ?? auth()->id(),
            ]);

            foreach ($asientoOriginal->detalles as $det) {
                DetalleAsiento::create([
                    'asiento_id' => $asientoReversion->id,
                    'cuenta_contable_id' => $det->cuenta_contable_id,
                    'glosa_detalle' => 'Reverso: ' . ($det->glosa_detalle ?: $asientoOriginal->glosa),
                    'debe' => $det->haber, // Inversión: el haber pasa al debe
                    'haber' => $det->debe, // Inversión: el debe pasa al haber
                ]);
            }

            return $asientoReversion;
        });
    }

    /**
     * Registra un Asiento manual validando la Partida Doble (total_debe === total_haber).
     */
    public function registrarAsientoManual(array $datos, array $detalles, ?int $usuarioId = null): Asiento
    {
        $totalDebe = 0.0;
        $totalHaber = 0.0;

        foreach ($detalles as $det) {
            $totalDebe += (float) ($det['debe'] ?? 0);
            $totalHaber += (float) ($det['haber'] ?? 0);
        }

        $totalDebe = round($totalDebe, 2);
        $totalHaber = round($totalHaber, 2);

        if (abs($totalDebe - $totalHaber) >= 0.01) {
            throw new InvalidArgumentException(
                sprintf('El asiento no cumple con la partida doble: Debe (Bs %s) != Haber (Bs %s).', $totalDebe, $totalHaber)
            );
        }

        $codigoTipo = strtoupper((string) ($datos['tipo_comprobante'] ?? $datos['tipo_comprobante_id'] ?? 'TRS'));
        if (! array_key_exists($codigoTipo, Asiento::TIPOS_COMPROBANTE)) {
            $codigoTipo = 'TRS';
        }
        $fecha = $datos['fecha'] ?? now()->toDateString();
        $numero = $datos['numero'] ?? $this->generarNumeroComprobante($codigoTipo, $fecha);

        return DB::transaction(function () use ($datos, $codigoTipo, $fecha, $numero, $totalDebe, $totalHaber, $usuarioId, $detalles) {
            $asiento = Asiento::create([
                'tipo_comprobante' => $codigoTipo,
                'numero' => $numero,
                'fecha' => $fecha,
                'glosa' => $datos['glosa'],
                'estado' => $datos['estado'] ?? 'APROBADO',
                'total_debe' => $totalDebe,
                'total_haber' => $totalHaber,
                'usuario_id' => $usuarioId ?? auth()->id(),
            ]);

            foreach ($detalles as $det) {
                DetalleAsiento::create([
                    'asiento_id' => $asiento->id,
                    'cuenta_contable_id' => $det['cuenta_contable_id'],
                    'glosa_detalle' => $det['glosa_detalle'] ?? null,
                    'debe' => round((float) ($det['debe'] ?? 0), 2),
                    'haber' => round((float) ($det['haber'] ?? 0), 2),
                ]);
            }

            return $asiento;
        });
    }

    /**
     * Registra el Balance de Apertura inicial (Comprobante N° 1 tipo APE).
     * Valida que Activo = Pasivo + Capital Social.
     */
    public function registrarBalanceApertura(array $datos, array $detallesApertura, ?int $usuarioId = null): Asiento
    {
        $fecha = $datos['fecha'] ?? now()->startOfYear()->toDateString();
        $numero = $datos['numero'] ?? $this->generarNumeroComprobante('APE', $fecha);

        $datos['tipo_comprobante'] = 'APE';
        $datos['fecha'] = $fecha;
        $datos['numero'] = $numero;
        $datos['glosa'] = $datos['glosa'] ?? 'Balance de Apertura e Inicio de Actividades Contables';

        return $this->registrarAsientoManual($datos, $detallesApertura, $usuarioId);
    }
}
