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
}
