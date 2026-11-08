<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsuarioTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $consultor;
    private Usuario $colaborador;

    protected function setUp(): void
    {
        parent::setUp();

        $this->consultor = Usuario::create([
            'nombre' => 'Consultor',
            'email' => 'admin@finora.test',
            'password' => bcrypt('password'),
            'rol' => 'CONSULTOR',
            'activo' => true,
        ]);

        $this->colaborador = Usuario::create([
            'nombre' => 'Colaborador',
            'email' => 'colab@finora.test',
            'password' => bcrypt('password'),
            'rol' => 'COLABORADOR',
            'activo' => true,
        ]);
    }

    public function test_consultor_puede_crear_usuario(): void
    {
        $response = $this->actingAs($this->consultor)->post(route('panel.usuarios.store'), [
            'nombre' => 'Nuevo',
            'apellido' => 'Usuario',
            'email' => 'nuevo@finora.test',
            'rol' => 'COLABORADOR',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('panel.usuarios.index'));
        $this->assertDatabaseHas('usuarios', ['email' => 'nuevo@finora.test', 'rol' => 'COLABORADOR']);
    }

    public function test_colaborador_no_puede_administrar_usuarios(): void
    {
        $response = $this->actingAs($this->colaborador)->get(route('panel.usuarios.index'));
        $response->assertStatus(403);
    }

    public function test_no_se_puede_desactivar_a_si_mismo(): void
    {
        $response = $this->actingAs($this->consultor)->patch(route('panel.usuarios.toggle', $this->consultor));
        $response->assertSessionHasErrors('error');
        $this->assertDatabaseHas('usuarios', ['id' => $this->consultor->id, 'activo' => true]);
    }
}
