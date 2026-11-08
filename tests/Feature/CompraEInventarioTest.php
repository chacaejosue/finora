<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Compra;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CompraEInventarioTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $consultor;

    private Proveedor $proveedor;

    private Producto $producto;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::forget('finora.tipo-cambio.usd-bob.oficial');
        Http::fake([
            config('finora.exchange_rate_url') => Http::response('ESTADOS UNIDOS DÓLAR USD 12.00'),
        ]);

        $this->consultor = Usuario::create([
            'nombre' => 'Admin',
            'email' => 'admin@finora.test',
            'password' => bcrypt('password'),
            'rol' => 'CONSULTOR',
            'activo' => true,
        ]);

        $this->proveedor = Proveedor::create(['nombre' => 'Natura Cosmeticos', 'activo' => true]);
        $cat = Categoria::create(['nombre' => 'Perfumería']);

        $this->producto = Producto::create([
            'proveedor_id' => $this->proveedor->id,
            'categoria_id' => $cat->id,
            'codigo' => 'PERF-001',
            'nombre' => 'Kaiak Vital',
            'precio_venta_actual' => 45.00,
            'activo' => true,
        ]);
    }

    public function test_crear_compra_en_borrador(): void
    {
        $response = $this->actingAs($this->consultor)->post(route('panel.compras.store'), [
            'proveedor_id' => $this->proveedor->id,
            'detalles' => [
                [
                    'producto_id' => $this->producto->id,
                    'cantidad' => 10,
                    'costo_unitario_usd' => 25.00,
                ],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('compras', ['proveedor_id' => $this->proveedor->id, 'estado' => 'BORRADOR']);
        $this->assertDatabaseHas('detalle_compras', ['producto_id' => $this->producto->id, 'cantidad' => 10]);
        $this->assertDatabaseHas('detalle_compras', ['producto_id' => $this->producto->id, 'costo_unitario' => 300.00]);
    }

    public function test_recepcion_de_compra_ingresa_inventario(): void
    {
        $compra = Compra::create([
            'proveedor_id' => $this->proveedor->id,
            'registrado_por_usuario_id' => $this->consultor->id,
            'estado' => 'EN_CAMINO',
        ]);

        $compra->detalles()->create([
            'producto_id' => $this->producto->id,
            'cantidad' => 15,
            'costo_unitario' => 20.00,
        ]);

        $response = $this->actingAs($this->consultor)->patch(route('panel.compras.estado', $compra), [
            'estado' => 'RECIBIDA',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('compras', ['id' => $compra->id, 'estado' => 'RECIBIDA']);
        $this->assertDatabaseHas('movimientos_inventario', [
            'producto_id' => $this->producto->id,
            'tipo' => 'ENTRADA_COMPRA',
            'cantidad' => 15,
        ]);

        $this->assertEquals(15, $this->producto->fresh()->stock_fisico);
    }
}
