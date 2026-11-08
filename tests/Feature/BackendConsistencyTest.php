<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\DetalleVenta;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Usuario;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BackendConsistencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_colaborador_puede_acceder_a_su_panel_operativo(): void
    {
        $colaborador = $this->crearUsuario('COLABORADOR', 'colaborador.panel@finora.test');

        $response = $this->actingAs($colaborador)->get(route('panel.colaborador'));

        $response->assertStatus(200);
        $response->assertSee('Panel del Colaborador');
        $response->assertSee('Operaciones disponibles');
    }

    public function test_colaborador_no_puede_acceder_a_contabilidad_ni_ajustar_inventario_ni_anular_ventas(): void
    {
        $colaborador = $this->crearUsuario('COLABORADOR', 'colaborador.restringido@finora.test');
        $cliente = $this->crearCliente('cliente.restringido@finora.test');
        $producto = $this->crearProducto();

        $venta = Venta::create([
            'cliente_id' => $cliente->id,
            'registrado_por_usuario_id' => $colaborador->id,
            'fecha' => now(),
            'forma_pago' => 'CONTADO',
            'estado' => 'CONFIRMADA',
            'descuento' => 0,
        ]);

        $this->actingAs($colaborador)->get(route('contabilidad.index'))->assertStatus(403);
        $this->actingAs($colaborador)->post(route('panel.inventario.ajuste'), [
            'producto_id' => $producto->id,
            'tipo' => 'AJUSTE_ENTRADA',
            'cantidad' => 5,
            'observacion' => 'No autorizado',
        ])->assertStatus(403);
        $this->actingAs($colaborador)->patch(route('panel.ventas.anular', $venta))->assertStatus(403);
        $this->actingAs($colaborador)->get(route('panel.proveedores.index'))->assertStatus(403);
        $this->actingAs($colaborador)->get(route('panel.categorias.index'))->assertStatus(403);
    }

    public function test_consultor_puede_ver_todas_las_vistas_de_contabilidad(): void
    {
        $consultor = $this->crearUsuario('CONSULTOR', 'consultor.contabilidad@finora.test');
        (new \Database\Seeders\ContabilidadBoliviaSeeder())->run();

        $this->actingAs($consultor)->get(route('contabilidad.index'))->assertStatus(200);
        $this->actingAs($consultor)->get(route('contabilidad.balance-apertura'))->assertStatus(200);
        $this->actingAs($consultor)->get(route('contabilidad.libro-diario'))->assertStatus(200);
        $this->actingAs($consultor)->get(route('contabilidad.asientos.crear'))->assertStatus(200);
        $this->actingAs($consultor)->get(route('contabilidad.libro-mayor'))->assertStatus(200);
        $this->actingAs($consultor)->get(route('contabilidad.sumas-saldos'))->assertStatus(200);
        $this->actingAs($consultor)->get(route('contabilidad.estados-financieros'))->assertStatus(200);
        $this->actingAs($consultor)->get(route('contabilidad.plan-cuentas'))->assertStatus(200);
    }

    public function test_cliente_ve_solo_sus_datos_en_el_dashboard(): void
    {
        $cliente = $this->crearCliente('cliente.dashboard@finora.test');
        $otroCliente = $this->crearCliente('otro.dashboard@finora.test');
        $producto = $this->crearProducto();

        Pedido::create([
            'cliente_id' => $cliente->id,
            'estado' => 'PENDIENTE',
            'fecha' => now(),
        ])->detalles()->create([
            'producto_id' => $producto->id,
            'cantidad' => 1,
            'precio_acordado' => 25,
        ]);

        Pedido::create([
            'cliente_id' => $otroCliente->id,
            'estado' => 'PENDIENTE',
            'fecha' => now(),
        ]);

        $response = $this->actingAs($cliente)->get(route('mi-cuenta'));

        $response->assertStatus(200);
        $response->assertSee('Mis pedidos');
        $response->assertSee('Pedidos recientes');
        $response->assertSee('Pedido #1');
    }

    public function test_venta_rechaza_un_pedido_de_otro_cliente(): void
    {
        $consultor = $this->crearUsuario('CONSULTOR', 'venta.validacion@finora.test');
        $cliente = $this->crearCliente('venta.cliente@finora.test');
        $otroCliente = $this->crearCliente('venta.otro@finora.test');
        $producto = $this->crearProducto();
        $pedido = Pedido::create([
            'cliente_id' => $cliente->id,
            'estado' => 'LISTO_ENTREGA',
            'fecha' => now(),
        ]);

        $response = $this->actingAs($consultor)->post(route('panel.ventas.store'), [
            'cliente_id' => $otroCliente->id,
            'pedido_id' => $pedido->id,
            'forma_pago' => 'CONTADO',
            'detalles' => [[
                'producto_id' => $producto->id,
                'cantidad' => 1,
                'precio_unitario' => 25,
            ]],
        ]);

        $response->assertSessionHasErrors('pedido_id');
        $this->assertDatabaseCount('ventas', 0);
    }

    public function test_no_se_puede_confirmar_una_venta_sin_stock(): void
    {
        $consultor = $this->crearUsuario('CONSULTOR', 'venta.stock@finora.test');
        $cliente = $this->crearCliente('stock.cliente@finora.test');
        $producto = $this->crearProducto();
        $venta = Venta::create([
            'cliente_id' => $cliente->id,
            'registrado_por_usuario_id' => $consultor->id,
            'fecha' => now(),
            'forma_pago' => 'CONTADO',
            'estado' => 'BORRADOR',
            'descuento' => 0,
        ]);
        DetalleVenta::create([
            'venta_id' => $venta->id,
            'producto_id' => $producto->id,
            'cantidad' => 1,
            'precio_unitario' => 25,
            'costo_unitario' => 10,
        ]);

        $response = $this->actingAs($consultor)->patch(route('panel.ventas.confirmar', $venta));

        $response->assertSessionHasErrors('stock');
        $this->assertDatabaseHas('ventas', ['id' => $venta->id, 'estado' => 'BORRADOR']);
    }

    public function test_ajuste_de_salida_no_deja_inventario_negativo(): void
    {
        $consultor = $this->crearUsuario('CONSULTOR', 'inventario.validacion@finora.test');
        $producto = $this->crearProducto();

        $response = $this->actingAs($consultor)->post(route('panel.inventario.ajuste'), [
            'producto_id' => $producto->id,
            'tipo' => 'AJUSTE_SALIDA',
            'cantidad' => 1,
            'observacion' => 'Ajuste de prueba',
        ]);

        $response->assertSessionHasErrors('cantidad');
        $this->assertDatabaseCount('movimientos_inventario', 0);
    }

    public function test_consultor_puede_crear_acceso_para_un_cliente_existente(): void
    {
        $consultor = $this->crearUsuario('CONSULTOR', 'cliente.acceso@finora.test');
        $cliente = Usuario::create([
            'nombre' => 'Cliente',
            'email' => 'cliente.sin.acceso@finora.test',
            'rol' => 'CLIENTE',
            'password' => null,
            'activo' => true,
        ]);

        $response = $this->actingAs($consultor)->post(route('panel.clientes.acceso', $cliente), [
            'email' => 'cliente.con.acceso@finora.test',
            'password' => 'Password123!@#',
            'password_confirmation' => 'Password123!@#',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('usuarios', [
            'id' => $cliente->id,
            'email' => 'cliente.con.acceso@finora.test',
            'rol' => 'CLIENTE',
            'activo' => true,
        ]);
        $this->assertNotNull($cliente->fresh()->password);
    }

    public function test_se_puede_registrar_la_entrega_de_un_pedido_listo(): void
    {
        $consultor = $this->crearUsuario('CONSULTOR', 'pedido.entrega@finora.test');
        $cliente = $this->crearCliente('pedido.entrega.cliente@finora.test');
        $pedido = Pedido::create([
            'cliente_id' => $cliente->id,
            'registrado_por_usuario_id' => $consultor->id,
            'estado' => 'LISTO_ENTREGA',
            'fecha' => now(),
        ]);

        $response = $this->actingAs($consultor)->patch(route('panel.pedidos.entregar', $pedido), [
            'recibido_por' => 'Ana Pérez',
            'observaciones_entrega' => 'Entregado en domicilio.',
        ]);

        $response->assertRedirect(route('panel.pedidos.show', $pedido));
        $this->assertDatabaseHas('pedidos', [
            'id' => $pedido->id,
            'estado' => 'COMPLETADO',
            'recibido_por' => 'Ana Pérez',
        ]);
    }

    private function crearUsuario(string $rol, string $email): Usuario
    {
        return Usuario::create([
            'nombre' => ucfirst(strtolower($rol)),
            'email' => $email,
            'password' => Hash::make('Password123!@#'),
            'rol' => $rol,
            'activo' => true,
        ]);
    }

    private function crearCliente(string $email): Usuario
    {
        return Usuario::create([
            'nombre' => 'Cliente',
            'email' => $email,
            'password' => Hash::make('Password123!@#'),
            'rol' => 'CLIENTE',
            'activo' => true,
        ]);
    }

    private function crearProducto(): Producto
    {
        $proveedor = Proveedor::create(['nombre' => 'Proveedor Test '.uniqid()]);
        $categoria = Categoria::create(['nombre' => 'Categoría Test '.uniqid()]);

        return Producto::create([
            'proveedor_id' => $proveedor->id,
            'categoria_id' => $categoria->id,
            'codigo' => 'PROD-'.uniqid(),
            'nombre' => 'Producto Test',
            'precio_venta_actual' => 25,
            'activo' => true,
        ]);
    }
}
