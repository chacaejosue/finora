<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ReporteTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_invitado_no_puede_ver_reportes(): void
    {
        $this->get(route('panel.reportes.index'))->assertRedirect('/login');
    }

    public function test_el_consultor_puede_ver_reportes_sin_datos(): void
    {
        $consultor = Usuario::create([
            'nombre' => 'Consultor',
            'email' => 'reportes@test.com',
            'password' => Hash::make('Password123!@#'),
            'rol' => 'CONSULTOR',
            'activo' => true,
        ]);

        $this->actingAs($consultor)
            ->get(route('panel.reportes.index'))
            ->assertOk()
            ->assertSee('Reportes');
    }
}
