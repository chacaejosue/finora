<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('id="backToTopLanding"', false);
        $response->assertSee('me gusta');
        $response->assertDontSee('2.3k');
        $response->assertSee('Consultora Independiente Natura');
        $response->assertSee('Finora • Gestión Comercial');
        $response->assertSee('Aviso de transparencia comercial');
    }
}
