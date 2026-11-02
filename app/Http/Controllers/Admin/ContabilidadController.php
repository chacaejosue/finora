<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asiento;
use App\Models\CuentaContable;
use App\Models\DetalleAsiento;
use App\Models\ModeloContable;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\TipoComprobante;
use App\Services\ContabilidadService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ContabilidadController extends Controller
{
    public function __construct(private ContabilidadService $contabilidad) {}

    /**
     * 1. Dashboard / Resumen Contable en tiempo real.
     */
    public function index(Request $request)
    {
        $anio = $request->input('anio', now()->year);
        $mes = $request->input('mes', now()->month);

        // Sumas de saldos actuales por tipo de cuenta
        $saldosPorTipo = DB::table('detalles_asiento')
            ->join('cuentas_contables', 'detalles_asiento.cuenta_contable_id', '=', 'cuentas_contables.id')
            ->join('asientos', 'detalles_asiento.asiento_id', '=', 'asientos.id')
            ->where('asientos.estado', 'APROBADO')
            ->select(
                'cuentas_contables.tipo',
                DB::raw('SUM(detalles_asiento.debe) as total_debe'),
                DB::raw('SUM(detalles_asiento.haber) as total_haber')
            )
            ->groupBy('cuentas_contables.tipo')
            ->get()
            ->keyBy('tipo');

        // Cálculos patrimoniales
        $activoDebe = (float) ($saldosPorTipo->get('ACTIVO')?->total_debe ?? 0);
        $activoHaber = (float) ($saldosPorTipo->get('ACTIVO')?->total_haber ?? 0);
        $totalActivo = max(0, $activoDebe - $activoHaber);

        $pasivoDebe = (float) ($saldosPorTipo->get('PASIVO')?->total_debe ?? 0);
        $pasivoHaber = (float) ($saldosPorTipo->get('PASIVO')?->total_haber ?? 0);
        $totalPasivo = max(0, $pasivoHaber - $pasivoDebe);

        $patrimonioDebe = (float) ($saldosPorTipo->get('PATRIMONIO')?->total_debe ?? 0);
        $patrimonioHaber = (float) ($saldosPorTipo->get('PATRIMONIO')?->total_haber ?? 0);
        $totalPatrimonioBase = $patrimonioHaber - $patrimonioDebe;

        $ingresosHaber = (float) ($saldosPorTipo->get('INGRESO')?->total_haber ?? 0);
        $ingresosDebe = (float) ($saldosPorTipo->get('INGRESO')?->total_debe ?? 0);
        $totalIngresos = max(0, $ingresosHaber - $ingresosDebe);

        $costosDebe = (float) ($saldosPorTipo->get('COSTO')?->total_debe ?? 0);
        $costosHaber = (float) ($saldosPorTipo->get('COSTO')?->total_haber ?? 0);
        $totalCostos = max(0, $costosDebe - $costosHaber);

        $gastosDebe = (float) ($saldosPorTipo->get('GASTO')?->total_debe ?? 0);
        $gastosHaber = (float) ($saldosPorTipo->get('GASTO')?->total_haber ?? 0);
        $totalGastos = max(0, $gastosDebe - $gastosHaber);

        $resultadoGestion = $totalIngresos - ($totalCostos + $totalGastos);
        $patrimonioNeto = $totalPatrimonioBase + $resultadoGestion;

        // Indicadores tributarios específicos (IVA Crédito Fiscal vs Débito Fiscal)
        $cuentaCreditoFiscal = CuentaContable::where('codigo', '1.1.2.03')->first();
        $cuentaDebitoFiscal = CuentaContable::where('codigo', '2.1.2.01')->first();
        $cuentaItPorPagar = CuentaContable::where('codigo', '2.1.2.02')->first();

        $saldoCreditoFiscal = $cuentaCreditoFiscal
            ? (float) DB::table('detalles_asiento')
                ->join('asientos', 'detalles_asiento.asiento_id', '=', 'asientos.id')
                ->where('asientos.estado', 'APROBADO')
                ->where('detalles_asiento.cuenta_contable_id', $cuentaCreditoFiscal->id)
                ->selectRaw('SUM(debe) - SUM(haber) as saldo')
                ->value('saldo') ?? 0
            : 0;

        $saldoDebitoFiscalMes = $cuentaDebitoFiscal
            ? (float) DB::table('detalles_asiento')
                ->join('asientos', 'detalles_asiento.asiento_id', '=', 'asientos.id')
                ->where('asientos.estado', 'APROBADO')
                ->where('detalles_asiento.cuenta_contable_id', $cuentaDebitoFiscal->id)
                ->whereYear('asientos.fecha', $anio)
                ->whereMonth('asientos.fecha', $mes)
                ->selectRaw('SUM(haber) - SUM(debe) as saldo')
                ->value('saldo') ?? 0
            : 0;

        $saldoItMes = $cuentaItPorPagar
            ? (float) DB::table('detalles_asiento')
                ->join('asientos', 'detalles_asiento.asiento_id', '=', 'asientos.id')
                ->where('asientos.estado', 'APROBADO')
                ->where('detalles_asiento.cuenta_contable_id', $cuentaItPorPagar->id)
                ->whereYear('asientos.fecha', $anio)
                ->whereMonth('asientos.fecha', $mes)
                ->selectRaw('SUM(haber) - SUM(debe) as saldo')
                ->value('saldo') ?? 0
            : 0;

        // Últimos 8 asientos para monitor rápido
        $ultimosAsientos = Asiento::with(['tipoComprobante', 'usuario.persona'])
            ->latest('id')
            ->take(8)
            ->get();

        $totalAsientos = Asiento::count();
        $asientosAutomaticos = Asiento::whereNotNull('contabilizable_type')->count();
        $asientosManuales = Asiento::whereNull('contabilizable_type')->count();

        return view('panel.contabilidad.index', compact(
            'totalActivo',
            'totalPasivo',
            'patrimonioNeto',
            'totalIngresos',
            'totalCostos',
            'totalGastos',
            'resultadoGestion',
            'saldoCreditoFiscal',
            'saldoDebitoFiscalMes',
            'saldoItMes',
            'ultimosAsientos',
            'totalAsientos',
            'asientosAutomaticos',
            'asientosManuales',
            'anio',
            'mes'
        ));
    }

    /**
     * 2. Balance de Apertura inicial formal y formulario.
     */
    public function balanceApertura()
    {
        $tipoApertura = TipoComprobante::where('codigo', 'APE')->first();
        $asientoApertura = $tipoApertura
            ? Asiento::with(['detalles.cuentaContable'])
                ->where('tipo_comprobante_id', $tipoApertura->id)
                ->where('estado', 'APROBADO')
                ->latest('id')
                ->first()
            : null;

        $cuentasActivo = CuentaContable::where('tipo', 'ACTIVO')
            ->where('permite_movimiento', true)
            ->orderBy('codigo')
            ->get();

        $cuentasPasivo = CuentaContable::where('tipo', 'PASIVO')
            ->where('permite_movimiento', true)
            ->orderBy('codigo')
            ->get();

        $cuentaCapitalSocial = CuentaContable::where('codigo', '3.1.1.01')->first();

        return view('panel.contabilidad.balance-apertura', compact(
            'asientoApertura',
            'cuentasActivo',
            'cuentasPasivo',
            'cuentaCapitalSocial'
        ));
    }

    /**
     * Guarda el Balance de Apertura inicial.
     */
    public function storeBalanceApertura(Request $request)
    {
        $validated = $request->validate([
            'fecha' => ['required', 'date'],
            'glosa' => ['required', 'string', 'max:500'],
            'partidas' => ['required', 'array', 'min:1'],
            'partidas.*.cuenta_contable_id' => ['required', 'exists:cuentas_contables,id'],
            'partidas.*.monto' => ['required', 'numeric', 'min:0'],
        ]);

        $cuentaCapital = CuentaContable::where('codigo', '3.1.1.01')->firstOrFail();

        $detalles = [];
        $totalActivo = 0.0;
        $totalPasivo = 0.0;

        foreach ($validated['partidas'] as $item) {
            $monto = (float) $item['monto'];
            if ($monto <= 0) {
                continue;
            }

            $cuenta = CuentaContable::find($item['cuenta_contable_id']);
            if (! $cuenta) {
                continue;
            }

            if ($cuenta->tipo === 'ACTIVO') {
                $totalActivo += $monto;
                $detalles[] = [
                    'cuenta_contable_id' => $cuenta->id,
                    'glosa_detalle' => 'Saldo inicial ' . $cuenta->nombre,
                    'debe' => $monto,
                    'haber' => 0,
                ];
            } elseif ($cuenta->tipo === 'PASIVO') {
                $totalPasivo += $monto;
                $detalles[] = [
                    'cuenta_contable_id' => $cuenta->id,
                    'glosa_detalle' => 'Saldo inicial obligación ' . $cuenta->nombre,
                    'debe' => 0,
                    'haber' => $monto,
                ];
            }
        }

        // Capital Social = Activo - Pasivo
        $capitalSocial = round($totalActivo - $totalPasivo, 2);
        if ($capitalSocial < 0) {
            return back()->withErrors(['error' => 'El total del pasivo inicial no puede superar al activo para apertura de actividades.']);
        }

        if ($capitalSocial > 0) {
            $detalles[] = [
                'cuenta_contable_id' => $cuentaCapital->id,
                'glosa_detalle' => 'Aporte y conformación de Capital Social inicial',
                'debe' => 0,
                'haber' => $capitalSocial,
            ];
        }

        try {
            $this->contabilidad->registrarBalanceApertura([
                'fecha' => $validated['fecha'],
                'glosa' => $validated['glosa'],
            ], $detalles, auth()->id());

            return redirect()->route('contabilidad.balance-apertura')
                ->with('exito', 'Balance de Apertura registrado con éxito. Se generó el Comprobante de Apertura (APE).');
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Error al registrar balance de apertura: ' . $e->getMessage()]);
        }
    }

    /**
     * 3. Libro Diario general.
     */
    public function libroDiario(Request $request)
    {
        $query = Asiento::with(['tipoComprobante', 'detalles.cuentaContable', 'usuario.persona']);

        if ($request->filled('tipo_id')) {
            $query->where('tipo_comprobante_id', $request->input('tipo_id'));
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->input('fecha_desde'));
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->input('fecha_hasta'));
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('numero', 'like', "%{$s}%")
                    ->orWhere('glosa', 'like', "%{$s}%");
            });
        }

        $asientos = $query->orderBy('fecha', 'desc')->orderBy('id', 'desc')->paginate(15)->withQueryString();
        $tiposComprobante = TipoComprobante::where('activo', true)->get();

        return view('panel.contabilidad.libro-diario', compact('asientos', 'tiposComprobante'));
    }

    /**
     * Formulario de Nuevo Asiento Manual.
     */
    public function createAsiento()
    {
        $tiposComprobante = TipoComprobante::where('activo', true)->get();
        $cuentas = CuentaContable::where('permite_movimiento', true)
            ->where('activo', true)
            ->orderBy('codigo')
            ->get();

        return view('panel.contabilidad.asientos-crear', compact('tiposComprobante', 'cuentas'));
    }

    /**
     * Guarda un Asiento Manual con validación en vivo de partida doble.
     */
    public function storeAsiento(Request $request)
    {
        $validated = $request->validate([
            'tipo_comprobante_id' => ['required', 'exists:tipos_comprobante,id'],
            'fecha' => ['required', 'date'],
            'glosa' => ['required', 'string', 'max:600'],
            'detalles' => ['required', 'array', 'min:2'],
            'detalles.*.cuenta_contable_id' => ['required', 'exists:cuentas_contables,id'],
            'detalles.*.debe' => ['nullable', 'numeric', 'min:0'],
            'detalles.*.haber' => ['nullable', 'numeric', 'min:0'],
            'detalles.*.glosa_detalle' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $asiento = $this->contabilidad->registrarAsientoManual([
                'tipo_comprobante_id' => $validated['tipo_comprobante_id'],
                'fecha' => $validated['fecha'],
                'glosa' => $validated['glosa'],
            ], $validated['detalles'], auth()->id());

            return redirect()->route('contabilidad.libro-diario')
                ->with('exito', "Comprobante {$asiento->numero} registrado exitosamente.");
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['partida_doble' => $e->getMessage()]);
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['error' => 'Error al guardar comprobante: ' . $e->getMessage()]);
        }
    }

    /**
     * Anula un asiento contable generando el contra-asiento de reversión.
     */
    public function anularAsiento(Request $request, Asiento $asiento)
    {
        $motivo = $request->input('motivo', 'Anulación manual solicitada por el usuario');

        if ($asiento->estado === 'ANULADO') {
            return back()->withErrors(['error' => 'El comprobante ya se encuentra anulado.']);
        }

        try {
            $reverso = $this->contabilidad->revertirAsiento($asiento, $motivo, auth()->id());

            return redirect()->route('contabilidad.libro-diario')
                ->with('exito', "Comprobante {$asiento->numero} anulado. Se generó el contra-asiento de reversión {$reverso->numero}.");
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'No se pudo anular el comprobante: ' . $e->getMessage()]);
        }
    }

    /**
     * 4. Libro Mayor con cálculo progresivo de saldos (Deudora vs Acreedora).
     */
    public function libroMayor(Request $request)
    {
        $cuentas = CuentaContable::where('permite_movimiento', true)
            ->where('activo', true)
            ->orderBy('codigo')
            ->get();

        $cuentaId = $request->input('cuenta_id');
        $cuentaSeleccionada = $cuentaId ? CuentaContable::find($cuentaId) : $cuentas->first();

        $movimientos = collect();
        $totalDebe = 0.0;
        $totalHaber = 0.0;
        $saldoFinal = 0.0;

        if ($cuentaSeleccionada) {
            $query = DetalleAsiento::with(['asiento.tipoComprobante'])
                ->where('cuenta_contable_id', $cuentaSeleccionada->id)
                ->whereHas('asiento', function ($q) use ($request) {
                    $q->where('estado', 'APROBADO');

                    if ($request->filled('fecha_desde')) {
                        $q->whereDate('fecha', '>=', $request->input('fecha_desde'));
                    }
                    if ($request->filled('fecha_hasta')) {
                        $q->whereDate('fecha', '<=', $request->input('fecha_hasta'));
                    }
                })
                ->join('asientos', 'detalles_asiento.asiento_id', '=', 'asientos.id')
                ->orderBy('asientos.fecha', 'asc')
                ->orderBy('asientos.id', 'asc')
                ->select('detalles_asiento.*');

            $movimientos = $query->get();

            // Calcular saldo progresivo respetando naturaleza deudora o acreedora
            $saldoAcumulado = 0.0;
            $esDeudora = ($cuentaSeleccionada->naturaleza === 'DEUDORA');

            foreach ($movimientos as $mov) {
                $totalDebe += (float) $mov->debe;
                $totalHaber += (float) $mov->haber;

                if ($esDeudora) {
                    $saldoAcumulado += ((float) $mov->debe - (float) $mov->haber);
                } else {
                    $saldoAcumulado += ((float) $mov->haber - (float) $mov->debe);
                }

                $mov->saldo_progresivo = $saldoAcumulado;
            }

            $saldoFinal = $saldoAcumulado;
        }

        return view('panel.contabilidad.libro-mayor', compact(
            'cuentas',
            'cuentaSeleccionada',
            'movimientos',
            'totalDebe',
            'totalHaber',
            'saldoFinal'
        ));
    }

    /**
     * 5. Balance de Comprobación de Sumas y Saldos (6 columnas).
     */
    public function sumasSaldos(Request $request)
    {
        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');

        $cuentasImputables = CuentaContable::where('permite_movimiento', true)
            ->where('activo', true)
            ->orderBy('codigo')
            ->get();

        $filas = [];
        $totalSumasDebe = 0.0;
        $totalSumasHaber = 0.0;
        $totalSaldosDeudor = 0.0;
        $totalSaldosAcreedor = 0.0;

        foreach ($cuentasImputables as $cta) {
            $query = DetalleAsiento::where('cuenta_contable_id', $cta->id)
                ->whereHas('asiento', function ($q) use ($fechaDesde, $fechaHasta) {
                    $q->where('estado', 'APROBADO');
                    if ($fechaDesde) {
                        $q->whereDate('fecha', '>=', $fechaDesde);
                    }
                    if ($fechaHasta) {
                        $q->whereDate('fecha', '<=', $fechaHasta);
                    }
                });

            $sumaDebe = (float) $query->sum('debe');
            $sumaHaber = (float) $query->sum('haber');

            // Solo mostrar cuentas que tengan movimientos o saldos
            if ($sumaDebe == 0 && $sumaHaber == 0) {
                continue;
            }

            $saldoDeudor = 0.0;
            $saldoAcreedor = 0.0;

            if ($sumaDebe >= $sumaHaber) {
                $saldoDeudor = round($sumaDebe - $sumaHaber, 2);
            } else {
                $saldoAcreedor = round($sumaHaber - $sumaDebe, 2);
            }

            $totalSumasDebe += $sumaDebe;
            $totalSumasHaber += $sumaHaber;
            $totalSaldosDeudor += $saldoDeudor;
            $totalSaldosAcreedor += $saldoAcreedor;

            $filas[] = [
                'cuenta' => $cta,
                'debe' => $sumaDebe,
                'haber' => $sumaHaber,
                'saldo_deudor' => $saldoDeudor,
                'saldo_acreedor' => $saldoAcreedor,
            ];
        }

        $cuadranSumas = abs($totalSumasDebe - $totalSumasHaber) < 0.02;
        $cuadranSaldos = abs($totalSaldosDeudor - $totalSaldosAcreedor) < 0.02;

        return view('panel.contabilidad.sumas-saldos', compact(
            'filas',
            'totalSumasDebe',
            'totalSumasHaber',
            'totalSaldosDeudor',
            'totalSaldosAcreedor',
            'cuadranSumas',
            'cuadranSaldos'
        ));
    }

    /**
     * 6. Estados Financieros (Estado de Resultados y Balance General).
     */
    public function estadosFinancieros(Request $request)
    {
        $fechaDesde = $request->input('fecha_desde', now()->startOfYear()->toDateString());
        $fechaHasta = $request->input('fecha_hasta', now()->toDateString());

        // Helper para consultar saldos por prefijo de código
        $obtenerSaldo = function (string $prefijoCodigo, string $naturaleza = 'DEUDORA') use ($fechaDesde, $fechaHasta) {
            $query = DetalleAsiento::join('cuentas_contables', 'detalles_asiento.cuenta_contable_id', '=', 'cuentas_contables.id')
                ->join('asientos', 'detalles_asiento.asiento_id', '=', 'asientos.id')
                ->where('asientos.estado', 'APROBADO')
                ->where('cuentas_contables.codigo', 'like', "{$prefijoCodigo}%")
                ->whereBetween('asientos.fecha', [$fechaDesde, $fechaHasta]);

            $debe = (float) $query->sum('detalles_asiento.debe');
            $haber = (float) $query->sum('detalles_asiento.haber');

            return $naturaleza === 'DEUDORA' ? ($debe - $haber) : ($haber - $debe);
        };

        // --- ESTADO DE RESULTADOS ---
        $ventasNetas = max(0, $obtenerSaldo('4.1.1', 'ACREEDORA'));
        $otrosIngresos = max(0, $obtenerSaldo('4.2', 'ACREEDORA'));
        $totalIngresosOperativos = $ventasNetas + $otrosIngresos;

        $costoVentas = max(0, $obtenerSaldo('5.1.1', 'DEUDORA'));
        $utilidadBruta = $totalIngresosOperativos - $costoVentas;

        $itGasto = max(0, $obtenerSaldo('6.1.1.01', 'DEUDORA'));
        $sueldosGasto = max(0, $obtenerSaldo('6.1.1.02', 'DEUDORA'));
        $serviciosGasto = max(0, $obtenerSaldo('6.1.1.03', 'DEUDORA'));
        $alquileresGasto = max(0, $obtenerSaldo('6.1.1.04', 'DEUDORA'));
        $mermasGasto = max(0, $obtenerSaldo('6.3.1.01', 'DEUDORA'));
        $otrosGastos = max(0, $obtenerSaldo('6', 'DEUDORA') - ($itGasto + $sueldosGasto + $serviciosGasto + $alquileresGasto + $mermasGasto));

        $totalGastosOperativos = $itGasto + $sueldosGasto + $serviciosGasto + $alquileresGasto + $mermasGasto + $otrosGastos;

        $utilidadNetaAntesImpuestos = $utilidadBruta - $totalGastosOperativos;

        // IUE (25%) según Ley 843 si hay utilidad neta positiva
        $iueCalculado = $utilidadNetaAntesImpuestos > 0 ? round($utilidadNetaAntesImpuestos * 0.25, 2) : 0;
        $utilidadLiquidaGestion = $utilidadNetaAntesImpuestos - $iueCalculado;

        // --- BALANCE GENERAL ---
        // Activo Corriente
        $disponible = max(0, $obtenerSaldo('1.1.1', 'DEUDORA'));
        $exigible = max(0, $obtenerSaldo('1.1.2', 'DEUDORA'));
        $realizable = max(0, $obtenerSaldo('1.1.3', 'DEUDORA'));
        $anticipados = max(0, $obtenerSaldo('1.1.4', 'DEUDORA'));
        $totalActivoCorriente = $disponible + $exigible + $realizable + $anticipados;

        // Activo No Corriente
        $bienesUso = max(0, $obtenerSaldo('1.2.1', 'DEUDORA'));
        $deprecAcum = max(0, $obtenerSaldo('1.2.2', 'ACREEDORA'));
        $totalActivoNoCorriente = max(0, $bienesUso - $deprecAcum);
        $totalActivos = $totalActivoCorriente + $totalActivoNoCorriente;

        // Pasivo Corriente
        $cxpProveedores = max(0, $obtenerSaldo('2.1.1', 'ACREEDORA'));
        $fiscales = max(0, $obtenerSaldo('2.1.2', 'ACREEDORA'));
        $laborales = max(0, $obtenerSaldo('2.1.3', 'ACREEDORA'));
        $totalPasivoCorriente = $cxpProveedores + $fiscales + $laborales;

        // Pasivo No Corriente
        $prestamosLp = max(0, $obtenerSaldo('2.2.1', 'ACREEDORA'));
        $totalPasivoNoCorriente = $prestamosLp;
        $totalPasivos = $totalPasivoCorriente + $totalPasivoNoCorriente;

        // Patrimonio
        $capitalSocial = max(0, $obtenerSaldo('3.1.1.01', 'ACREEDORA'));
        $reservas = max(0, $obtenerSaldo('3.1.1.03', 'ACREEDORA'));
        $resultadosAcum = $obtenerSaldo('3.2.1.01', 'ACREEDORA');
        $resultadoDelEjercicio = $utilidadNetaAntesImpuestos; // Se integra dinámicamente

        $totalPatrimonio = $capitalSocial + $reservas + $resultadosAcum + $resultadoDelEjercicio;

        // Verificación de ecuación patrimonial
        $totalPasivoYPatrimonio = $totalPasivos + $totalPatrimonio;
        $ecuacionCuadrada = abs($totalActivos - $totalPasivoYPatrimonio) < 0.05;

        return view('panel.contabilidad.estados-financieros', compact(
            'fechaDesde',
            'fechaHasta',
            'ventasNetas',
            'otrosIngresos',
            'totalIngresosOperativos',
            'costoVentas',
            'utilidadBruta',
            'itGasto',
            'sueldosGasto',
            'serviciosGasto',
            'alquileresGasto',
            'mermasGasto',
            'otrosGastos',
            'totalGastosOperativos',
            'utilidadNetaAntesImpuestos',
            'iueCalculado',
            'utilidadLiquidaGestion',
            'disponible',
            'exigible',
            'realizable',
            'anticipados',
            'totalActivoCorriente',
            'bienesUso',
            'deprecAcum',
            'totalActivoNoCorriente',
            'totalActivos',
            'cxpProveedores',
            'fiscales',
            'laborales',
            'totalPasivoCorriente',
            'prestamosLp',
            'totalPasivoNoCorriente',
            'totalPasivos',
            'capitalSocial',
            'reservas',
            'resultadosAcum',
            'resultadoDelEjercicio',
            'totalPatrimonio',
            'totalPasivoYPatrimonio',
            'ecuacionCuadrada'
        ));
    }

    /**
     * 7. Kardex Físico-Valorado por Costo Promedio Ponderado (CPP).
     */
    public function kardexValorado(Request $request)
    {
        $productos = Producto::where('activo', true)->orderBy('nombre')->get();
        $productoId = $request->input('producto_id');
        $productoSeleccionado = $productoId ? Producto::find($productoId) : $productos->first();

        $movimientosKardex = collect();
        $saldoFisicoActual = 0;
        $saldoValoradoActual = 0.0;

        if ($productoSeleccionado) {
            $rawMovimientos = MovimientoInventario::with(['detalleCompra.compra', 'detalleVenta.venta'])
                ->where('producto_id', $productoSeleccionado->id)
                ->orderBy('fecha', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            $saldoFisico = 0;
            $saldoValorado = 0.0;
            $cppActual = (float) $productoSeleccionado->costo_promedio;

            foreach ($rawMovimientos as $mov) {
                $cantidad = (int) $mov->cantidad;
                $esEntrada = ($cantidad > 0);
                $cantAbs = abs($cantidad);

                $costoUnitario = (float) $mov->costo_unitario;
                if ($costoUnitario <= 0) {
                    $costoUnitario = $cppActual;
                }

                $costoTotal = (float) $mov->costo_total;
                if ($costoTotal <= 0) {
                    $costoTotal = round($cantAbs * $costoUnitario, 2);
                }

                if ($esEntrada) {
                    $saldoFisico += $cantAbs;
                    $saldoValorado += $costoTotal;
                } else {
                    $saldoFisico -= $cantAbs;
                    $saldoValorado = max(0, $saldoValorado - $costoTotal);
                }

                $cppFila = $saldoFisico > 0 ? round($saldoValorado / $saldoFisico, 2) : $costoUnitario;

                $movimientosKardex->push((object) [
                    'id' => $mov->id,
                    'fecha' => $mov->fecha,
                    'tipo' => $mov->tipo,
                    'observacion' => $mov->observacion,
                    'es_entrada' => $esEntrada,
                    'cantidad_entrada' => $esEntrada ? $cantAbs : 0,
                    'cantidad_salida' => ! $esEntrada ? $cantAbs : 0,
                    'costo_unitario' => $costoUnitario,
                    'costo_total_entrada' => $esEntrada ? $costoTotal : 0,
                    'costo_total_salida' => ! $esEntrada ? $costoTotal : 0,
                    'saldo_fisico' => $saldoFisico,
                    'saldo_valorado' => $saldoValorado,
                    'cpp' => $cppFila,
                ]);
            }

            $saldoFisicoActual = $saldoFisico;
            $saldoValoradoActual = $saldoValorado;
        }

        return view('panel.contabilidad.kardex-valorado', compact(
            'productos',
            'productoSeleccionado',
            'movimientosKardex',
            'saldoFisicoActual',
            'saldoValoradoActual'
        ));
    }

    /**
     * 8. Catálogo del Plan de Cuentas y Plantillas de Contabilización.
     */
    public function planCuentas()
    {
        $cuentasRaiz = CuentaContable::with(['hijos.hijos.hijos'])
            ->whereNull('padre_id')
            ->orderBy('codigo')
            ->get();

        $todasCuentas = CuentaContable::orderBy('codigo')->get();

        $modelosContables = ModeloContable::with(['tipoComprobante', 'detalles.cuentaContable'])
            ->where('activo', true)
            ->get();

        return view('panel.contabilidad.plan-cuentas', compact(
            'cuentasRaiz',
            'todasCuentas',
            'modelosContables'
        ));
    }
}
