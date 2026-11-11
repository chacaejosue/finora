<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Compra;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Usuario;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlujoNegocioCompletoTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $consultor;

    private Usuario $cliente;

    private Producto $producto;

    private Proveedor $proveedor;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Usuario Consultor
        $this->consultor = Usuario::create([
            'nombre' => 'Josué',
            'apellido' => 'Chacae',
            'email' => 'josue@finora.test',
            'password' => bcrypt('password'),
            'rol' => 'CONSULTOR',
            'activo' => true,
        ]);

        // 2. Cliente
        $this->cliente = Usuario::create([
            'nombre' => 'Ana',
            'apellido' => 'Rios',
            'email' => 'ana@ejemplo.com',
            'telefono' => '78901234',
            'rol' => 'CLIENTE',
            'activo' => true,
        ]);

        // 3. Proveedor, Categoría y Producto
        $this->proveedor = Proveedor::create(['nombre' => 'Natura Cosmeticos', 'activo' => true]);
        $cat = Categoria::create(['nombre' => 'Cuidado Facial']);

        $this->producto = Producto::create([
            'proveedor_id' => $this->proveedor->id,
            'categoria_id' => $cat->id,
            'codigo' => 'NAT-CHRONOS-50',
            'nombre' => 'Chronos Suero Reductor de Arrugas',
            'precio_venta_actual' => 120.00,
            'activo' => true,
        ]);
    }

    public function test_flujo_integral_cliente_pedido_compra_recepcion_reserva_venta_pago(): void
    {
        // STEP 1: El cliente solicita un pedido de 5 unidades.
        $resPedido = $this->actingAs($this->consultor)->post(route('panel.pedidos.store'), [
            'cliente_id' => $this->cliente->id,
            'detalles' => [
                [
                    'producto_id' => $this->producto->id,
                    'cantidad' => 5,
                    'precio_acordado' => 120.00,
                ],
            ],
        ]);
        $resPedido->assertRedirect();
        $pedido = Pedido::first();
        $this->assertNotNull($pedido);
        // Sin stock disponible, queda en PENDIENTE_ABASTECIMIENTO
        $this->assertEquals('PENDIENTE_ABASTECIMIENTO', $pedido->estado);

        // STEP 2: El consultor realiza una compra al proveedor por 10 unidades.
        $resCompra = $this->actingAs($this->consultor)->post(route('panel.compras.store'), [
            'proveedor_id' => $this->proveedor->id,
            'detalles' => [
                [
                    'producto_id' => $this->producto->id,
                    'cantidad' => 10,
                    'costo_unitario_usd' => 70.00,
                ],
            ],
        ]);
        $compra = Compra::first();
        $this->assertNotNull($compra);

        // STEP 3: Se recibe la compra de mercadería.
        $this->actingAs($this->consultor)->patch(route('panel.compras.estado', $compra), [
            'estado' => 'RECIBIDA',
        ]);

        $this->assertEquals(10, $this->producto->fresh()->stock_fisico);

        // STEP 4: Se ejecuta la reserva sobre el pedido ahora que hay stock.
        $this->actingAs($this->consultor)->post(route('panel.pedidos.reservar', $pedido));
        $pedido->refresh();

        $this->assertEquals('LISTO_ENTREGA', $pedido->estado);
        $this->assertEquals(5, $pedido->detalles->first()->cantidad_reservada);

        // STEP 5: Se genera la venta asociada a este pedido.
        $resVenta = $this->actingAs($this->consultor)->post(route('panel.ventas.store'), [
            'cliente_id' => $this->cliente->id,
            'pedido_id' => $pedido->id,
            'forma_pago' => 'CREDITO',
            'metodo_pago' => null,
            'numero_cuotas' => 2,
            'descuento' => 20.00,
            'detalles' => [
                [
                    'producto_id' => $this->producto->id,
                    'cantidad' => 5,
                    'precio_unitario' => 120.00,
                ],
            ],
        ]);
        $venta = Venta::first();
        $this->assertNotNull($venta);
        $this->assertEquals(580.00, $venta->total); // (5 * 120) - 20 = 580

        // STEP 6: Se confirma la venta (lo que descuenta el inventario).
        $this->actingAs($this->consultor)->patch(route('panel.ventas.confirmar', $venta));
        $venta->refresh();

        $this->assertEquals('CONFIRMADA', $venta->estado);
        $this->assertEquals(5, $this->producto->fresh()->stock_fisico); // 10 entradas - 5 salidas = 5
        $this->assertDatabaseCount('cuotas', 2);

        // STEP 7: El cliente abona el monto total a crédito.
        $resPago = $this->actingAs($this->consultor)->post(route('panel.pagos.store'), [
            'venta_id' => $venta->id,
            'monto' => 580.00,
            'metodo' => 'TRANSFERENCIA',
            'observacion' => 'Pago total mediante transferencia bancaria',
        ]);
        $resPago->assertRedirect();

        $venta->refresh();
        $this->assertEquals(0.00, $venta->saldo_pendiente);
        $this->assertDatabaseHas('cuotas', ['venta_id' => $venta->id, 'estado' => 'PAGADA']);
    }

    /**
     * Prueba que se puede registrar una venta creando un cliente nuevo al vuelo (captura de WhatsApp).
     */
    public function test_registrar_venta_directa_con_nuevo_cliente_al_vuelo(): void
    {
        $resVenta = $this->actingAs($this->consultor)->post(route('panel.ventas.store'), [
            'nuevo_cliente_nombre' => 'Mariana',
            'nuevo_cliente_apellido' => 'Sosa',
            'nuevo_cliente_telefono' => '70112233',
            'forma_pago' => 'CONTADO',
            'metodo_pago' => 'EFECTIVO',
            'detalles' => [
                [
                    'producto_id' => $this->producto->id,
                    'cantidad' => 1,
                    'precio_unitario' => 120.00,
                ],
            ],
        ]);

        // Se verifica que el cliente se haya creado en usuarios
        $nuevoCliente = Usuario::where('email', null)->where('telefono', '70112233')->first();
        $this->assertNotNull($nuevoCliente);
        $this->assertEquals('MARIANA', $nuevoCliente->nombre);
        $this->assertEquals('CLIENTE', $nuevoCliente->rol);

        // Se verifica que la venta esté asociada a ese cliente nuevo
        $venta = Venta::where('cliente_id', $nuevoCliente->id)->first();
        $this->assertNotNull($venta);
        $this->assertEquals(120.00, $venta->total);
        $resVenta->assertRedirect(route('panel.ventas.show', $venta));
    }

    /**
     * Prueba que la vista de pedido muestra el acceso para facturar / generar venta.
     */
    public function test_pedido_muestra_boton_facturar_desde_pendiente(): void
    {
        $pedido = Pedido::create([
            'cliente_id' => $this->cliente->id,
            'registrado_por_usuario_id' => $this->consultor->id,
            'fecha' => now(),
            'estado' => 'PENDIENTE',
        ]);

        $response = $this->actingAs($this->consultor)->get(route('panel.pedidos.show', $pedido));
        $response->assertOk();
        $response->assertSee('Facturar / Generar Venta desde Pedido');
    }

    public function test_pedido_completado_sigue_mostrando_boton_facturar_si_no_tiene_venta(): void
    {
        $pedido = Pedido::create([
            'cliente_id' => $this->cliente->id,
            'registrado_por_usuario_id' => $this->consultor->id,
            'fecha' => now(),
            'estado' => 'COMPLETADO',
            'recibido_por' => 'Cliente Prueba',
            'fecha_entrega' => now(),
        ]);

        $response = $this->actingAs($this->consultor)->get(route('panel.pedidos.show', $pedido));
        $response->assertOk();
        $response->assertSee('Facturar / Generar Venta desde Pedido');
    }

    public function test_venta_en_borrador_puede_ser_editada_y_actualizada(): void
    {
        $venta = Venta::create([
            'cliente_id' => $this->cliente->id,
            'registrado_por_usuario_id' => $this->consultor->id,
            'fecha' => now(),
            'forma_pago' => 'CONTADO',
            'metodo_pago' => 'EFECTIVO',
            'estado' => 'BORRADOR',
            'descuento' => 0,
        ]);

        $venta->detalles()->create([
            'producto_id' => $this->producto->id,
            'cantidad' => 1,
            'precio_unitario' => 100.00,
            'costo_unitario' => 50.00,
        ]);

        // Ver vista de edición
        $resEdit = $this->actingAs($this->consultor)->get(route('panel.ventas.edit', $venta));
        $resEdit->assertOk();
        $resEdit->assertSee('Editar Venta #' . $venta->id);

        // Actualizar datos de la venta
        $resUpdate = $this->actingAs($this->consultor)->put(route('panel.ventas.update', $venta), [
            'cliente_id' => $this->cliente->id,
            'forma_pago' => 'CREDITO',
            'numero_cuotas' => 3,
            'descuento' => 10.00,
            'detalles' => [
                [
                    'producto_id' => $this->producto->id,
                    'cantidad' => 2,
                    'precio_unitario' => 95.00,
                ],
            ],
        ]);

        $resUpdate->assertRedirect(route('panel.ventas.show', $venta));
        $venta->refresh();
        $this->assertEquals('CREDITO', $venta->forma_pago);
        $this->assertEquals(3, $venta->numero_cuotas);
        $this->assertEquals(10.00, $venta->descuento);
        $this->assertEquals(180.00, $venta->total); // (2 * 95) - 10
    }

    public function test_venta_en_borrador_puede_ser_descartada_y_eliminada(): void
    {
        $venta = Venta::create([
            'cliente_id' => $this->cliente->id,
            'registrado_por_usuario_id' => $this->consultor->id,
            'fecha' => now(),
            'forma_pago' => 'CONTADO',
            'estado' => 'BORRADOR',
        ]);

        $venta->detalles()->create([
            'producto_id' => $this->producto->id,
            'cantidad' => 1,
            'precio_unitario' => 100.00,
            'costo_unitario' => 50.00,
        ]);

        $resDelete = $this->actingAs($this->consultor)->delete(route('panel.ventas.destroy', $venta));
        $resDelete->assertRedirect(route('panel.ventas.index'));
        $this->assertDatabaseMissing('ventas', ['id' => $venta->id]);
    }

    public function test_confirmacion_venta_con_stock_insuficiente_muestra_mensaje_detallado(): void
    {
        // Venta por 999 unidades sin stock
        $venta = Venta::create([
            'cliente_id' => $this->cliente->id,
            'registrado_por_usuario_id' => $this->consultor->id,
            'fecha' => now(),
            'forma_pago' => 'CONTADO',
            'metodo_pago' => 'EFECTIVO',
            'estado' => 'BORRADOR',
        ]);

        $venta->detalles()->create([
            'producto_id' => $this->producto->id,
            'cantidad' => 999,
            'precio_unitario' => 100.00,
            'costo_unitario' => 50.00,
        ]);

        $resConfirm = $this->actingAs($this->consultor)->from(route('panel.ventas.show', $venta))
            ->patch(route('panel.ventas.confirmar', $venta));

        $resConfirm->assertSessionHasErrors('stock');
        $this->assertEquals('BORRADOR', $venta->fresh()->estado);
    }

    public function test_saludo_y_genero_de_consultora(): void
    {
        $consultora = Usuario::create([
            'nombre' => 'Consultora',
            'apellido' => 'Demo',
            'email' => 'consultora.test@finora.test',
            'password' => bcrypt('Password123!'),
            'rol' => 'CONSULTOR',
            'genero' => 'FEMENINO',
            'activo' => true,
        ]);

        $this->assertEquals('Bienvenida', $consultora->saludo);
        $this->assertEquals('Consultora', $consultora->rol_nombre);
    }

    public function test_compra_guarda_forma_pago_y_metodo_pago(): void
    {
        $resCompra = $this->actingAs($this->consultor)->post(route('panel.compras.store'), [
            'proveedor_id' => $this->proveedor->id,
            'forma_pago' => 'CREDITO',
            'metodo_pago' => null,
            'detalles' => [
                [
                    'producto_id' => $this->producto->id,
                    'cantidad' => 5,
                    'costo_unitario' => 60.00,
                ],
            ],
        ]);

        $resCompra->assertRedirect();
        $this->assertDatabaseHas('compras', [
            'proveedor_id' => $this->proveedor->id,
            'forma_pago' => 'CREDITO',
            'estado' => 'BORRADOR',
        ]);
    }
}
