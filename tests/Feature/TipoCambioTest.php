<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TipoCambioTest extends TestCase
{
    use RefreshDatabase;

    public function test_official_rate_is_cached_and_returned_by_the_public_endpoint(): void
    {
        Cache::forget('finora.tipo-cambio.usd-bob.oficial');
        Http::fake([
            config('finora.exchange_rate_url') => Http::response('ESTADOS UNIDOS DÓLAR USD 6.96'),
        ]);

        $response = $this->get(route('tipo-cambio'));

        $response->assertOk()->assertJsonPath('bolivianos_por_dolar', 6.96)->assertJsonPath('fuente', 'OFICIAL');
        Http::assertSentCount(1);
    }

    public function test_consultor_can_activate_a_manual_rate(): void
    {
        $consultor = Usuario::create([
            'nombre' => 'Consultor',
            'email' => 'tipo-cambio@test.com',
            'password' => Hash::make('Password123!@#'),
            'rol' => 'CONSULTOR',
            'activo' => true,
        ]);

        $response = $this->actingAs($consultor)->patch(route('panel.tipo-cambio.update'), [
            'fuente' => 'MANUAL',
            'tasa_manual' => '11.97',
        ]);

        $response->assertRedirect();
        $this->get(route('tipo-cambio'))->assertJsonPath('bolivianos_por_dolar', 11.97)->assertJsonPath('fuente', 'MANUAL');
    }

    public function test_daily_update_command_stores_the_official_bcb_rate(): void
    {
        Cache::forget('finora.tipo-cambio.usd-bob.oficial');
        Http::fake([
            config('finora.exchange_rate_url') => Http::response(<<<'HTML'
                <table>
                    <tr><td>ESTADOS UNIDOS</td><td>DÓLAR</td><td>USD</td><td>11.97</td></tr>
                </table>
                HTML),
        ]);

        $this->artisan('finora:actualizar-tipo-cambio')
            ->assertSuccessful()
            ->expectsOutput('Tipo de cambio actualizado: 11.9700 Bs/USD.');

        $this->assertSame('11.970000', Cache::get('finora.exchange_rate.official'));
    }
}
