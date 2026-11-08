<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PedidoPublicoTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_invitado_puede_ver_el_carrito(): void
    {
        $this->get(route('carrito'))->assertOk()->assertSee('Arma tu pedido');
    }

    public function test_un_invitado_puede_enviar_un_pedido_con_varios_productos(): void
    {
        $proveedor = Proveedor::create(['nombre' => 'Natura Test', 'activo' => true]);
        $categoria = Categoria::create(['nombre' => 'Cuidado Test', 'activo' => true]);
        $productoA = Producto::create([
            'proveedor_id' => $proveedor->id,
            'categoria_id' => $categoria->id,
            'codigo' => 'WEB-001',
            'nombre' => 'Producto web A',
            'precio_venta_actual' => 25,
            'publicado' => true,
            'activo' => true,
        ]);
        $productoB = Producto::create([
            'proveedor_id' => $proveedor->id,
            'categoria_id' => $categoria->id,
            'codigo' => 'WEB-002',
            'nombre' => 'Producto web B',
            'precio_venta_actual' => 15,
            'publicado' => true,
            'activo' => true,
        ]);

        config(['services.whatsapp.phone' => '59170000000']);

        $response = $this->post(route('carrito.pedido'), [
            'nombre' => 'Cliente Web',
            'apellido' => 'Prueba',
            'telefono' => '70000000',
            'email' => 'cliente.web@test.com',
            'preferencia_pago' => 'CREDITO',
            'carrito' => [
                ['producto_id' => $productoA->id, 'cantidad' => 2],
                ['producto_id' => $productoB->id, 'cantidad' => 1],
            ],
        ]);

        $response->assertOk()->assertSee('Solicitud registrada')->assertSee('Pedido #1');
        $this->assertDatabaseHas('pedidos', ['estado' => 'PENDIENTE', 'cliente_id' => Usuario::clientes()->first()->id]);
        $this->assertDatabaseCount('detalle_pedidos', 2);
        $this->assertDatabaseHas('detalle_pedidos', ['producto_id' => $productoA->id, 'cantidad' => 2, 'precio_acordado' => 25]);
        $this->assertDatabaseCount('movimientos_inventario', 0);
    }

    public function test_no_se_puede_enviar_un_producto_no_publicado(): void
    {
        $proveedor = Proveedor::create(['nombre' => 'Proveedor Test', 'activo' => true]);
        $categoria = Categoria::create(['nombre' => 'Categoría Test', 'activo' => true]);
        $producto = Producto::create([
            'proveedor_id' => $proveedor->id,
            'categoria_id' => $categoria->id,
            'codigo' => 'WEB-003',
            'nombre' => 'Producto oculto',
            'precio_venta_actual' => 20,
            'publicado' => false,
            'activo' => true,
        ]);

        $this->post(route('carrito.pedido'), [
            'nombre' => 'Cliente Web',
            'telefono' => '71111111',
            'preferencia_pago' => 'POR_CONFIRMAR',
            'carrito' => [['producto_id' => $producto->id, 'cantidad' => 1]],
        ])->assertSessionHasErrors('carrito');
    }

    public function test_rechaza_nombres_y_telefonos_con_caracteres_invalidos(): void
    {
        $proveedor = Proveedor::create(['nombre' => 'Proveedor Validación', 'activo' => true]);
        $categoria = Categoria::create(['nombre' => 'Categoría Validación', 'activo' => true]);
        $producto = Producto::create([
            'proveedor_id' => $proveedor->id,
            'categoria_id' => $categoria->id,
            'codigo' => 'WEB-004',
            'nombre' => 'Producto validación',
            'precio_venta_actual' => 20,
            'publicado' => true,
            'activo' => true,
        ]);

        $this->post(route('carrito.pedido'), [
            'nombre' => '1234',
            'apellido' => '@@@',
            'telefono' => 'teléfono inválido',
            'preferencia_pago' => 'POR_CONFIRMAR',
            'carrito' => [['producto_id' => $producto->id, 'cantidad' => 1]],
        ])->assertSessionHasErrors(['nombre', 'apellido', 'telefono']);
    }
}
