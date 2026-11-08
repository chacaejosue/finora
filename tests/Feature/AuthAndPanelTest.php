<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthAndPanelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Un visitante sin sesión no puede entrar al panel y es redirigido al login.
     */
    public function test_invitado_no_puede_acceder_al_panel(): void
    {
        $response = $this->get('/panel');

        $response->assertRedirect('/login');
    }

    public function test_pantalla_de_login_no_se_puede_guardar_en_cache_del_navegador(): void
    {
        $response = $this->get('/login');

        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
        $response->assertHeader('Pragma', 'no-cache');
    }

    /**
     * Un CONSULTOR autenticado activo puede acceder al panel.
     */
    public function test_consultor_autenticado_puede_acceder_al_panel(): void
    {
        $usuario = Usuario::create([
            'nombre' => 'Josué',
            'apellido' => 'Chacae',
            'email' => 'consultor@finora.test',
            'password' => Hash::make('Password123!@#'),
            'rol' => 'CONSULTOR',
            'activo' => true,
        ]);

        $response = $this->actingAs($usuario)->get('/panel');

        $response->assertStatus(200);
        $response->assertSee('¡Bienvenido, Josué!');
        $response->assertSee('CONSULTOR');
    }

    /**
     * Las credenciales incorrectas son rechazadas.
     */
    public function test_credenciales_incorrectas_son_rechazadas(): void
    {
        Usuario::create([
            'nombre' => 'Test',
            'email' => 'prueba@finora.test',
            'password' => Hash::make('ClaveCorrecta123!'),
            'rol' => 'CONSULTOR',
            'activo' => true,
        ]);

        $response = $this->post('/login', [
            'identity' => 'prueba@finora.test',
            'password' => 'ClaveIncorrecta',
        ]);

        $response->assertSessionHasErrors('identity');
        $this->assertGuest();
    }

    /**
     * Las cuentas inactivas no pueden iniciar sesión.
     */
    public function test_usuario_inactivo_no_puede_iniciar_sesion(): void
    {
        Usuario::create([
            'nombre' => 'Inactivo',
            'email' => 'inactivo@finora.test',
            'password' => Hash::make('Password123!@#'),
            'rol' => 'CONSULTOR',
            'activo' => false,
        ]);

        $response = $this->post('/login', [
            'identity' => 'inactivo@finora.test',
            'password' => 'Password123!@#',
        ]);

        $response->assertSessionHasErrors('identity');
        $this->assertGuest();
    }

    /**
     * El cierre de sesión funciona correctamente.
     */
    public function test_cierre_de_sesion_funciona_correctamente(): void
    {
        $usuario = Usuario::create([
            'nombre' => 'Consultor',
            'email' => 'logout@finora.test',
            'password' => Hash::make('Password123!@#'),
            'rol' => 'CONSULTOR',
            'activo' => true,
        ]);

        $response = $this->actingAs($usuario)->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    /**
     * Los usuarios con otros roles (COLABORADOR, CLIENTE) no pueden acceder al panel del consultor.
     */
    public function test_otros_roles_no_pueden_acceder_al_panel_del_consultor(): void
    {
        $usuarioColaborador = Usuario::create([
            'nombre' => 'Colaborador',
            'email' => 'colaborador@finora.test',
            'password' => Hash::make('Password123!@#'),
            'rol' => 'COLABORADOR',
            'activo' => true,
        ]);

        $usuarioCliente = Usuario::create([
            'nombre' => 'Cliente',
            'email' => 'cliente@finora.test',
            'password' => Hash::make('Password123!@#'),
            'rol' => 'CLIENTE',
            'activo' => true,
        ]);

        // Intentar acceder como COLABORADOR
        $responseColaborador = $this->actingAs($usuarioColaborador)->get('/panel');
        $responseColaborador->assertStatus(403);

        // Intentar acceder como CLIENTE
        $responseCliente = $this->actingAs($usuarioCliente)->get('/panel');
        $responseCliente->assertStatus(403);
    }

    /**
     * Un consultor autenticado que accede a /login es redirigido al panel.
     */
    public function test_consultor_autenticado_visitando_login_es_redirigido_al_panel(): void
    {
        $usuario = Usuario::create([
            'nombre' => 'Consultor',
            'email' => 'redirect@finora.test',
            'password' => Hash::make('Password123!@#'),
            'rol' => 'CONSULTOR',
            'activo' => true,
        ]);

        $response = $this->actingAs($usuario)->get('/login');

        $response->assertRedirect('/panel');
    }

    /**
     * Un cliente autenticado que accede a /login es redirigido a su portal /mi-cuenta.
     */
    public function test_cliente_visitando_login_es_redirigido_a_mi_cuenta(): void
    {
        $usuario = Usuario::create([
            'nombre' => 'Cliente',
            'email' => 'cliente_redirect@finora.test',
            'password' => Hash::make('Password123!@#'),
            'rol' => 'CLIENTE',
            'activo' => true,
        ]);

        $response = $this->actingAs($usuario)->get('/login');

        $response->assertRedirect(route('mi-cuenta'));
    }

    /**
     * Un cliente puede acceder a su portal personal /mi-cuenta.
     */
    public function test_cliente_puede_acceder_a_su_portal_mi_cuenta(): void
    {
        $usuario = Usuario::create([
            'nombre' => 'Ana',
            'apellido' => 'Ríos',
            'email' => 'ana_portal@finora.test',
            'password' => Hash::make('Password123!@#'),
            'rol' => 'CLIENTE',
            'activo' => true,
        ]);

        $response = $this->actingAs($usuario)->get(route('mi-cuenta'));

        $response->assertStatus(200);
        $response->assertSee('¡Bienvenido, Ana!');
        $response->assertSee('Mi Cuenta');
        $response->assertSee('Portal de Cliente en Desarrollo');
    }

    /**
     * Un colaborador autenticado que accede a /login es redirigido al panel del colaborador.
     */
    public function test_colaborador_visitando_login_es_redirigido_a_panel_productos(): void
    {
        $usuario = Usuario::create([
            'nombre' => 'Colaborador',
            'email' => 'colab_redirect@finora.test',
            'password' => Hash::make('Password123!@#'),
            'rol' => 'COLABORADOR',
            'activo' => true,
        ]);

        $response = $this->actingAs($usuario)->get('/login');

        $response->assertRedirect(route('panel.colaborador'));
    }
}
