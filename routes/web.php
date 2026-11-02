<?php

use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\ClienteController;
use App\Http\Controllers\Admin\CompraController;
use App\Http\Controllers\Admin\ContabilidadController;
use App\Http\Controllers\Admin\InventarioController;
use App\Http\Controllers\Admin\LineaController;
use App\Http\Controllers\Admin\PagoController;
use App\Http\Controllers\Admin\PedidoController;
use App\Http\Controllers\Admin\ProductoController;
use App\Http\Controllers\Admin\ProveedorController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\VentaController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\PedidoPublicoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\TipoCambioController;
use Illuminate\Support\Facades\Route;

// Landing pública
Route::get('/', [CatalogoController::class, 'landing'])->name('landing');

// Catálogo público conectado a MySQL
Route::get('/categorias', [CatalogoController::class, 'index'])->name('categorias');
Route::get('/carrito', [PedidoPublicoController::class, 'create'])->name('carrito');
Route::get('/tipo-cambio', TipoCambioController::class)->name('tipo-cambio');
Route::post('/carrito/pedido', [PedidoPublicoController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('carrito.pedido');

// Rutas de autenticación (solo para visitantes sin sesión)
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return response()
            ->view('login')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    })->name('login');

    Route::post('/login', [LoginController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.authenticate');
});

// Cierre de sesión (para usuarios autenticados)
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Mi Cuenta / Portal de Cliente (para usuarios autenticados)
Route::middleware(['auth', 'rol.cliente'])->get('/mi-cuenta', [PanelController::class, 'cliente'])->name('mi-cuenta');

// Panel privado para usuarios autenticados
Route::middleware('auth')->prefix('panel')->group(function () {

    // Rutas exclusivas del CONSULTOR
    Route::middleware('rol.consultor')->group(function () {
        Route::get('/', [PanelController::class, 'index'])->name('panel');
        Route::name('panel.')->group(function () {
            Route::get('/index', [PanelController::class, 'index'])->name('index');
            Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
            Route::get('/reportes/exportar', [ReporteController::class, 'exportar'])->name('reportes.exportar');
            Route::patch('/tipo-cambio', [TipoCambioController::class, 'update'])->name('tipo-cambio.update');
            Route::post('/tipo-cambio/actualizar', [TipoCambioController::class, 'refresh'])->name('tipo-cambio.refresh');

            // Usuarios (Reservado exclusivamente a CONSULTOR)
            Route::patch('usuarios/{usuario}/toggle', [UsuarioController::class, 'toggleStatus'])->name('usuarios.toggle');
            Route::resource('usuarios', UsuarioController::class)->except(['destroy', 'show']);
        });
    });

    // Rutas para roles operativos (CONSULTOR / COLABORADOR)
    Route::middleware('rol.operativo')->name('panel.')->group(function () {
        Route::get('/colaborador', [PanelController::class, 'operativo'])->name('colaborador');

        // CRUD de Productos
        Route::patch('productos/{producto}/toggle', [ProductoController::class, 'toggleStatus'])->name('productos.toggle');
        Route::resource('productos', ProductoController::class);

        // CRUD de Proveedores, Categorías y Líneas
        Route::resource('proveedores', ProveedorController::class)->except(['create', 'show', 'edit']);
        Route::resource('categorias', CategoriaController::class)->except(['create', 'show', 'edit']);
        Route::resource('lineas', LineaController::class)->except(['create', 'show', 'edit']);

        // Clientes
        Route::patch('clientes/{cliente}/toggle', [ClienteController::class, 'toggleStatus'])->name('clientes.toggle');
        Route::post('clientes/{cliente}/acceso', [ClienteController::class, 'crearAcceso'])->name('clientes.acceso');
        Route::resource('clientes', ClienteController::class);

        // Compras / Abastecimiento
        Route::patch('compras/{compra}/estado', [CompraController::class, 'cambiarEstado'])->name('compras.estado');
        Route::resource('compras', CompraController::class)->except(['destroy', 'edit', 'update']);

        // Inventario
        Route::get('inventario', [InventarioController::class, 'index'])->name('inventario.index');
        Route::get('inventario/movimientos', [InventarioController::class, 'movimientos'])->name('inventario.movimientos');
        Route::post('inventario/ajuste', [InventarioController::class, 'ajuste'])->name('inventario.ajuste');

        // Pedidos
        Route::post('pedidos/{pedido}/reservar', [PedidoController::class, 'reservarStock'])->name('pedidos.reservar');
        Route::patch('pedidos/{pedido}/entregar', [PedidoController::class, 'entregar'])->name('pedidos.entregar');
        Route::patch('pedidos/{pedido}/estado', [PedidoController::class, 'cambiarEstado'])->name('pedidos.estado');
        Route::resource('pedidos', PedidoController::class)->except(['destroy', 'edit', 'update']);

        // Ventas
        Route::patch('ventas/{venta}/confirmar', [VentaController::class, 'confirmar'])->name('ventas.confirmar');
        Route::patch('ventas/{venta}/anular', [VentaController::class, 'anular'])->name('ventas.anular');
        Route::resource('ventas', VentaController::class)->except(['destroy', 'edit', 'update']);

        // Pagos
        Route::resource('pagos', PagoController::class)->only(['index', 'create', 'store']);
    });

    // Alias amigable dentro de panel si el usuario navega a /panel/contabilidad
    Route::middleware('rol.operativo')->prefix('contabilidad')->group(function () {
        Route::get('/', fn () => redirect()->route('contabilidad.index'));
    });
});

// Módulo Contable (Normativa Bolivia - Ciclo Contable Completo)
Route::middleware(['auth', 'rol.operativo'])->prefix('contabilidad')->name('contabilidad.')->group(function () {
    Route::get('/', [ContabilidadController::class, 'index'])->name('index');
    Route::get('/balance-apertura', [ContabilidadController::class, 'balanceApertura'])->name('balance-apertura');
    Route::post('/balance-apertura', [ContabilidadController::class, 'storeBalanceApertura'])->name('balance-apertura.store');
    Route::get('/libro-diario', [ContabilidadController::class, 'libroDiario'])->name('libro-diario');
    Route::get('/asientos/crear', [ContabilidadController::class, 'createAsiento'])->name('asientos.crear');
    Route::post('/asientos', [ContabilidadController::class, 'storeAsiento'])->name('asientos.store');
    Route::post('/asientos/{asiento}/anular', [ContabilidadController::class, 'anularAsiento'])->name('asientos.anular');
    Route::get('/libro-mayor', [ContabilidadController::class, 'libroMayor'])->name('libro-mayor');
    Route::get('/sumas-saldos', [ContabilidadController::class, 'sumasSaldos'])->name('sumas-saldos');
    Route::get('/estados-financieros', [ContabilidadController::class, 'estadosFinancieros'])->name('estados-financieros');
    Route::get('/kardex-valorado', [ContabilidadController::class, 'kardexValorado'])->name('kardex-valorado');
    Route::get('/plan-cuentas', [ContabilidadController::class, 'planCuentas'])->name('plan-cuentas');
});
