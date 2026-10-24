<?php

namespace Database\Seeders;

use App\Models\AplicacionPago;
use App\Models\Auditoria;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Compra;
use App\Models\Credito;
use App\Models\Cuota;
use App\Models\DetalleCompra;
use App\Models\DetallePedido;
use App\Models\DetalleVenta;
use App\Models\Linea;
use App\Models\MovimientoInventario;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\Persona;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Usuario;
use App\Models\Venta;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $consultorEmail = (string) config('finora.demo_consultor_email', 'consultora.demo@finora.test');

        if (Persona::where('email', $consultorEmail)->exists()) {
            $this->command?->warn('Los datos de demostración ya existen en la base de datos.');
            $this->command?->line('Para reiniciar todos los datos desde cero, ejecuta: <comment>php artisan migrate:fresh --seed</comment>');

            return;
        }

        DB::transaction(function (): void {
            $consultor = $this->crearUsuario('CONSULTOR', config('finora.demo_consultor_email'), 'Maricel Demo');
            $colaborador = $this->crearUsuario('COLABORADOR', config('finora.demo_colaborador_email'), 'Alex Operaciones');
            $clientes = $this->crearClientes();
            $catalogo = $this->crearCatalogo();

            $this->crearComprasEInventario($consultor, $catalogo['productos']);
            $this->crearPedidos($consultor, $clientes, $catalogo['productos']);
            $this->crearVentas($consultor, $clientes, $catalogo['productos']);

            Auditoria::create([
                'usuario_id' => $consultor->id,
                'accion' => 'CARGAR_DATOS_DEMO',
                'entidad' => self::class,
                'descripcion' => 'Se cargó el conjunto de datos demostrativos de Finora.',
                'valores_nuevos' => [
                    'consultor_id' => $consultor->id,
                    'colaborador_id' => $colaborador->id,
                    'clientes' => count($clientes),
                    'productos' => count($catalogo['productos']),
                ],
                'ip' => '127.0.0.1',
            ]);
        });
    }

    private function crearUsuario(string $rol, string $email, string $nombre): Usuario
    {
        $persona = Persona::create([
            'nombre' => $nombre,
            'email' => $email,
            'telefono' => '70000000',
        ]);

        return Usuario::create([
            'persona_id' => $persona->id,
            'password' => Hash::make(config($rol === 'CONSULTOR' ? 'finora.demo_consultor_password' : 'finora.demo_colaborador_password')),
            'rol' => $rol,
            'activo' => true,
        ]);
    }

    /**
     * @return array<int, Cliente>
     */
    private function crearClientes(): array
    {
        $datos = [
            ['nombre' => 'Ana', 'apellido' => 'López', 'email' => config('finora.demo_cliente_email'), 'telefono' => '71100001'],
            ['nombre' => 'Bruno', 'apellido' => 'Mendoza', 'email' => 'bruno.cliente@finora.test', 'telefono' => '71100002'],
            ['nombre' => 'Carla', 'apellido' => 'Ríos', 'email' => 'carla.cliente@finora.test', 'telefono' => '71100003'],
            ['nombre' => 'Diego', 'apellido' => 'Salas', 'email' => 'diego.cliente@finora.test', 'telefono' => '71100004'],
            ['nombre' => 'Elena', 'apellido' => 'Vargas', 'email' => 'elena.cliente@finora.test', 'telefono' => '71100005'],
        ];

        $clientes = [];

        foreach ($datos as $indice => $dato) {
            $persona = Persona::create($dato);
            $cliente = Cliente::create([
                'persona_id' => $persona->id,
                'activo' => true,
                'observaciones' => $indice === 0 ? 'Cliente demo con acceso al portal.' : 'Cliente demo para pruebas.',
            ]);

            if ($indice === 0) {
                Usuario::create([
                    'persona_id' => $persona->id,
                    'password' => Hash::make(config('finora.demo_cliente_password')),
                    'rol' => 'CLIENTE',
                    'activo' => true,
                ]);
            }

            $clientes[] = $cliente;
        }

        return $clientes;
    }

    /**
     * @return array{productos: array<string, array<string, mixed>>}
     */
    private function crearCatalogo(): array
    {
        $proveedores = [];

        foreach ([
            ['nombre' => 'Aroma Andino', 'email' => 'ventas@aroma-andino.test'],
            ['nombre' => 'Belleza Verde', 'email' => 'contacto@belleza-verde.test'],
            ['nombre' => 'Casa Botánica', 'email' => 'pedidos@casa-botanica.test'],
        ] as $dato) {
            $proveedores[$dato['nombre']] = Proveedor::create([...$dato, 'activo' => true]);
        }

        $categorias = [];
        foreach ([
            ['nombre' => 'Perfumería', 'descripcion' => 'Fragancias para uso diario y ocasiones especiales.'],
            ['nombre' => 'Cuidado facial', 'descripcion' => 'Limpieza, hidratación y tratamientos faciales.'],
            ['nombre' => 'Cuidado corporal', 'descripcion' => 'Cremas, lociones y productos para el cuerpo.'],
            ['nombre' => 'Maquillaje', 'descripcion' => 'Productos básicos para maquillaje y belleza.'],
            ['nombre' => 'Higiene personal', 'descripcion' => 'Productos de higiene y cuidado diario.'],
        ] as $dato) {
            $categorias[$dato['nombre']] = Categoria::create([...$dato, 'activo' => true]);
        }

        $lineas = [];
        foreach (['Aromas', 'Esencia', 'Botánica', 'Rostro', 'Cotidiano'] as $nombre) {
            $lineas[$nombre] = Linea::create([
                'nombre' => $nombre,
                'descripcion' => "Línea demo de productos {$nombre}.",
                'activo' => true,
            ]);
        }

        $productos = [];
        $datosProductos = [
            ['codigo' => 'DEM-ARO-001', 'nombre' => 'Brisa de Montaña 50 ml', 'categoria' => 'Perfumería', 'linea' => 'Aromas', 'proveedor' => 'Aroma Andino', 'precio' => 24.00, 'costo' => 12.00, 'stock' => 0, 'imagen' => 'https://images.unsplash.com/photo-1541643600914-78b084683601?w=600&q=80'],
            ['codigo' => 'DEM-ARO-002', 'nombre' => 'Luz de Jardín 50 ml', 'categoria' => 'Perfumería', 'linea' => 'Esencia', 'proveedor' => 'Aroma Andino', 'precio' => 29.00, 'costo' => 15.00, 'stock' => 2, 'imagen' => 'https://images.unsplash.com/photo-1594035910387-fea47794261f?w=600&q=80'],
            ['codigo' => 'DEM-BOT-001', 'nombre' => 'Crema Facial Hidratación 50 g', 'categoria' => 'Cuidado facial', 'linea' => 'Botánica', 'proveedor' => 'Belleza Verde', 'precio' => 18.50, 'costo' => 9.00, 'stock' => 5, 'imagen' => 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?w=600&q=80'],
            ['codigo' => 'DEM-ROS-001', 'nombre' => 'Gel Limpiador Suave 120 ml', 'categoria' => 'Cuidado facial', 'linea' => 'Rostro', 'proveedor' => 'Belleza Verde', 'precio' => 16.00, 'costo' => 7.50, 'stock' => 12, 'imagen' => 'https://images.unsplash.com/photo-1571781926291-c477ebfd024b?w=600&q=80'],
            ['codigo' => 'DEM-COT-001', 'nombre' => 'Loción Corporal Cacao 200 ml', 'categoria' => 'Cuidado corporal', 'linea' => 'Cotidiano', 'proveedor' => 'Casa Botánica', 'precio' => 14.00, 'costo' => 6.00, 'stock' => 25, 'imagen' => 'https://images.unsplash.com/photo-1608248543803-ba4f8c70ae0b?w=600&q=80'],
            ['codigo' => 'DEM-COT-002', 'nombre' => 'Jabón Cremoso Avena', 'categoria' => 'Cuidado corporal', 'linea' => 'Cotidiano', 'proveedor' => 'Casa Botánica', 'precio' => 8.50, 'costo' => 3.50, 'stock' => 8, 'imagen' => 'https://images.unsplash.com/photo-1584302179602-e4c3d3fd629d?w=600&q=80'],
            ['codigo' => 'DEM-MAQ-001', 'nombre' => 'Labial Coral Suave', 'categoria' => 'Maquillaje', 'linea' => 'Esencia', 'proveedor' => 'Belleza Verde', 'precio' => 12.00, 'costo' => 5.00, 'stock' => 1, 'imagen' => 'https://images.unsplash.com/photo-1586495777744-4413f21062fa?w=600&q=80'],
            ['codigo' => 'DEM-MAQ-002', 'nombre' => 'Máscara Pestañas Volumen', 'categoria' => 'Maquillaje', 'linea' => 'Esencia', 'proveedor' => 'Belleza Verde', 'precio' => 15.50, 'costo' => 7.00, 'stock' => 15, 'imagen' => 'https://images.unsplash.com/photo-1631214524020-7e18db9a8f92?w=600&q=80'],
            ['codigo' => 'DEM-HIG-001', 'nombre' => 'Desodorante Frescura Diaria', 'categoria' => 'Higiene personal', 'linea' => 'Cotidiano', 'proveedor' => 'Casa Botánica', 'precio' => 9.00, 'costo' => 4.00, 'stock' => 4, 'imagen' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?w=600&q=80'],
            ['codigo' => 'DEM-HIG-002', 'nombre' => 'Kit Higiene de Viaje', 'categoria' => 'Higiene personal', 'linea' => 'Cotidiano', 'proveedor' => 'Casa Botánica', 'precio' => 11.50, 'costo' => 5.50, 'stock' => 30, 'imagen' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?w=600&q=80'],
            ['codigo' => 'DEM-ARO-003', 'nombre' => 'Cítrico de Amanecer 100 ml', 'categoria' => 'Perfumería', 'linea' => 'Aromas', 'proveedor' => 'Aroma Andino', 'precio' => 32.00, 'costo' => 17.00, 'stock' => 6, 'imagen' => 'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?w=600&q=80'],
            ['codigo' => 'DEM-BOT-002', 'nombre' => 'Aceite Corporal Semillas 100 ml', 'categoria' => 'Cuidado corporal', 'linea' => 'Botánica', 'proveedor' => 'Casa Botánica', 'precio' => 19.00, 'costo' => 9.50, 'stock' => 10, 'imagen' => 'https://images.unsplash.com/photo-1611930022073-b7a4ba5fcccd?w=600&q=80'],
        ];

        foreach ($datosProductos as $dato) {
            $producto = Producto::create([
                'proveedor_id' => $proveedores[$dato['proveedor']]->id,
                'linea_id' => $lineas[$dato['linea']]->id,
                'categoria_id' => $categorias[$dato['categoria']]->id,
                'codigo' => $dato['codigo'],
                'nombre' => $dato['nombre'],
                'descripcion' => 'Producto ficticio para pruebas de catálogo, inventario y ventas.',
                'precio_venta_actual' => $dato['precio'],
                'imagen_principal' => $dato['imagen'],
                'publicado' => true,
                'activo' => true,
            ]);
            $dato['producto'] = $producto;
            $productos[$dato['codigo']] = $dato;
        }

        return ['productos' => $productos];
    }

    /**
     * @param  array<string, array<string, mixed>>  $productos
     */
    private function crearComprasEInventario(Usuario $consultor, array $productos): void
    {
        $proveedor = Proveedor::where('nombre', 'Casa Botánica')->firstOrFail();
        $compra = Compra::create([
            'proveedor_id' => $proveedor->id,
            'registrado_por_usuario_id' => $consultor->id,
            'fecha_solicitud' => now()->subDays(10),
            'fecha_recepcion' => now()->subDays(8),
            'estado' => 'RECIBIDA',
            'observaciones' => 'Compra demo recibida para carga inicial de inventario.',
        ]);

        foreach ($productos as $dato) {
            if ($dato['stock'] <= 0) {
                continue;
            }

            $detalle = DetalleCompra::create([
                'compra_id' => $compra->id,
                'producto_id' => $dato['producto']->id,
                'cantidad' => $dato['stock'],
                'costo_unitario' => $dato['costo'],
            ]);

            MovimientoInventario::create([
                'producto_id' => $dato['producto']->id,
                'registrado_por_usuario_id' => $consultor->id,
                'detalle_compra_id' => $detalle->id,
                'tipo' => 'ENTRADA_COMPRA',
                'cantidad' => $dato['stock'],
                'fecha' => now()->subDays(8),
                'observacion' => "Entrada demo por compra #{$compra->id}.",
            ]);
        }
    }

    /**
     * @param  array<int, Cliente>  $clientes
     * @param  array<string, array<string, mixed>>  $productos
     */
    private function crearPedidos(Usuario $consultor, array $clientes, array $productos): void
    {
        $pedidoAbastecimiento = Pedido::create([
            'cliente_id' => $clientes[1]->id,
            'registrado_por_usuario_id' => $consultor->id,
            'fecha' => now()->subDays(2),
            'estado' => 'PENDIENTE_ABASTECIMIENTO',
            'observaciones' => 'Pedido demo de un producto agotado para probar reabastecimiento.',
        ]);
        DetallePedido::create([
            'pedido_id' => $pedidoAbastecimiento->id,
            'producto_id' => $productos['DEM-ARO-001']['producto']->id,
            'cantidad' => 2,
            'cantidad_reservada' => 0,
            'precio_acordado' => $productos['DEM-ARO-001']['precio'],
            'estado' => 'PENDIENTE',
        ]);

        $pedidoReservado = Pedido::create([
            'cliente_id' => $clientes[2]->id,
            'registrado_por_usuario_id' => $consultor->id,
            'fecha' => now()->subDay(),
            'estado' => 'RESERVADO',
            'observaciones' => 'Pedido demo reservado para atención del colaborador.',
        ]);
        DetallePedido::create([
            'pedido_id' => $pedidoReservado->id,
            'producto_id' => $productos['DEM-ARO-002']['producto']->id,
            'cantidad' => 1,
            'cantidad_reservada' => 1,
            'precio_acordado' => $productos['DEM-ARO-002']['precio'],
            'estado' => 'RESERVADO',
        ]);
    }

    /**
     * @param  array<int, Cliente>  $clientes
     * @param  array<string, array<string, mixed>>  $productos
     */
    private function crearVentas(Usuario $consultor, array $clientes, array $productos): void
    {
        $ventaContado = Venta::create([
            'cliente_id' => $clientes[0]->id,
            'registrado_por_usuario_id' => $consultor->id,
            'fecha' => now()->subDays(4),
            'forma_pago' => 'CONTADO',
            'estado' => 'CONFIRMADA',
            'descuento' => 0,
        ]);
        $detalleContado = DetalleVenta::create([
            'venta_id' => $ventaContado->id,
            'producto_id' => $productos['DEM-COT-001']['producto']->id,
            'cantidad' => 1,
            'precio_unitario' => $productos['DEM-COT-001']['precio'],
            'costo_unitario' => $productos['DEM-COT-001']['costo'],
        ]);
        MovimientoInventario::create([
            'producto_id' => $detalleContado->producto_id,
            'registrado_por_usuario_id' => $consultor->id,
            'detalle_venta_id' => $detalleContado->id,
            'tipo' => 'SALIDA_VENTA',
            'cantidad' => -1,
            'fecha' => now()->subDays(4),
            'observacion' => "Salida demo por venta #{$ventaContado->id}.",
        ]);
        Pago::create([
            'venta_id' => $ventaContado->id,
            'registrado_por_usuario_id' => $consultor->id,
            'monto' => $ventaContado->total,
            'fecha' => now()->subDays(4),
            'metodo' => 'EFECTIVO',
            'estado' => 'REGISTRADO',
            'observacion' => 'Pago demo de contado.',
        ]);

        $ventaCredito = Venta::create([
            'cliente_id' => $clientes[3]->id,
            'registrado_por_usuario_id' => $consultor->id,
            'fecha' => now()->subDays(3),
            'forma_pago' => 'CREDITO',
            'numero_cuotas' => 3,
            'estado' => 'CONFIRMADA',
            'descuento' => 0,
        ]);
        foreach ([['codigo' => 'DEM-ARO-003', 'cantidad' => 1], ['codigo' => 'DEM-HIG-002', 'cantidad' => 1]] as $item) {
            $producto = $productos[$item['codigo']];
            $detalle = DetalleVenta::create([
                'venta_id' => $ventaCredito->id,
                'producto_id' => $producto['producto']->id,
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $producto['precio'],
                'costo_unitario' => $producto['costo'],
            ]);
            MovimientoInventario::create([
                'producto_id' => $detalle->producto_id,
                'registrado_por_usuario_id' => $consultor->id,
                'detalle_venta_id' => $detalle->id,
                'tipo' => 'SALIDA_VENTA',
                'cantidad' => -$item['cantidad'],
                'fecha' => now()->subDays(3),
                'observacion' => "Salida demo por venta a crédito #{$ventaCredito->id}.",
            ]);
        }

        $credito = Credito::create([
            'venta_id' => $ventaCredito->id,
            'monto_financiado' => $ventaCredito->total,
            'numero_cuotas' => 3,
            'interes_porcentaje' => 0,
            'fecha_inicio' => now()->subDays(3),
            'fecha_fin' => now()->addDays(87),
            'estado' => 'ACTIVO',
        ]);
        $montoCuota = round($ventaCredito->total / 3, 2);
        $cuotas = [];
        for ($numero = 1; $numero <= 3; $numero++) {
            $cuotas[] = Cuota::create([
                'credito_id' => $credito->id,
                'numero' => $numero,
                'monto' => $numero === 3 ? $ventaCredito->total - ($montoCuota * 2) : $montoCuota,
                'fecha_vencimiento' => now()->addDays(30 * $numero),
                'estado' => $numero === 1 ? 'PARCIAL' : 'PENDIENTE',
            ]);
        }
        $pago = Pago::create([
            'venta_id' => $ventaCredito->id,
            'registrado_por_usuario_id' => $consultor->id,
            'monto' => round($cuotas[0]->monto / 2, 2),
            'fecha' => now()->subDay(),
            'metodo' => 'TRANSFERENCIA',
            'estado' => 'REGISTRADO',
            'observacion' => 'Abono demo de la primera cuota.',
        ]);
        AplicacionPago::create([
            'pago_id' => $pago->id,
            'cuota_id' => $cuotas[0]->id,
            'monto_aplicado' => $pago->monto,
        ]);

        $ventaBorrador = Venta::create([
            'cliente_id' => $clientes[4]->id,
            'registrado_por_usuario_id' => $consultor->id,
            'fecha' => now(),
            'forma_pago' => 'CONTADO',
            'estado' => 'BORRADOR',
            'descuento' => 0,
        ]);
        DetalleVenta::create([
            'venta_id' => $ventaBorrador->id,
            'producto_id' => $productos['DEM-MAQ-002']['producto']->id,
            'cantidad' => 1,
            'precio_unitario' => $productos['DEM-MAQ-002']['precio'],
            'costo_unitario' => $productos['DEM-MAQ-002']['costo'],
        ]);
    }
}
