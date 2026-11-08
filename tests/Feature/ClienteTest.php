<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClienteTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $consultor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->consultor = Usuario::create([
            'nombre' => 'Consultor',
            'apellido' => 'Admin',
            'email' => 'admin@finora.test',
            'password' => bcrypt('password'),
            'rol' => 'CONSULTOR',
            'activo' => true,
        ]);
    }

    public function test_consultor_puede_crear_cliente(): void
    {
        $response = $this->actingAs($this->consultor)->post(route('panel.clientes.store'), [
            'nombre' => 'María',
            'apellido' => 'Gómez',
            'telefono' => '71234567',
            'email' => 'maria@ejemplo.com',
            'direccion' => 'Calle 1 # 45',
            'observaciones' => 'Cliente VIP',
        ]);

        $response->assertRedirect(route('panel.clientes.index'));
        $this->assertDatabaseHas('usuarios', [
            'email' => 'maria@ejemplo.com',
            'rol' => 'CLIENTE',
            'observaciones' => 'Cliente VIP',
        ]);
    }

    public function test_consultor_puede_editar_cliente(): void
    {
        $cliente = Usuario::create([
            'nombre' => 'Pedro',
            'email' => 'pedro@test.com',
            'rol' => 'CLIENTE',
            'activo' => true,
        ]);

        $response = $this->actingAs($this->consultor)->put(route('panel.clientes.update', $cliente), [
            'nombre' => 'Pedro Antonio',
            'email' => 'pedro.antonio@test.com',
            'observaciones' => 'Actualizado',
        ]);

        $response->assertRedirect(route('panel.clientes.index'));
        $this->assertDatabaseHas('usuarios', [
            'id' => $cliente->id,
            'nombre' => 'Pedro Antonio',
            'email' => 'pedro.antonio@test.com',
            'observaciones' => 'Actualizado',
        ]);
    }

    public function test_consultor_puede_buscar_clientes(): void
    {
        Usuario::create([
            'nombre' => 'Lucía',
            'apellido' => 'Pérez',
            'email' => 'lucia@test.com',
            'rol' => 'CLIENTE',
            'activo' => true,
        ]);

        $response = $this->actingAs($this->consultor)->get(route('panel.clientes.index', ['search' => 'Lucía']));
        $response->assertStatus(200);
        $response->assertSee('Lucía');
    }

    public function test_consultor_puede_activar_y_desactivar_cliente(): void
    {
        $cliente = Usuario::create([
            'nombre' => 'Carlos',
            'rol' => 'CLIENTE',
            'activo' => true,
        ]);

        $this->actingAs($this->consultor)->patch(route('panel.clientes.toggle', $cliente));
        $this->assertDatabaseHas('usuarios', ['id' => $cliente->id, 'activo' => false]);
    }
}
