<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class TipoCambioService
{
    private const MODE_KEY = 'finora.exchange_rate.mode';

    private const MANUAL_KEY = 'finora.exchange_rate.manual';

    private const OFFICIAL_KEY = 'finora.exchange_rate.official';

    private const UPDATED_AT_KEY = 'finora.exchange_rate.updated_at';

    public function bolivianosPorDolar(): float
    {
        if ($this->fuente() === 'MANUAL') {
            return $this->manual() ?? (float) config('finora.usd_to_bob_fallback', 12.00);
        }

        return (float) Cache::remember(
            'finora.tipo-cambio.usd-bob.oficial',
            now()->endOfDay()->addMinutes(5),
            fn (): float => $this->guardarTasaOficial($this->consultarBancoCentral()),
        );
    }

    /**
     * @return array{tasa: float, fuente: string, actualizado: string, es_respaldo: bool}
     */
    public function informacion(): array
    {
        $tasaOficial = Cache::get(self::OFFICIAL_KEY);
        $actualizado = Cache::get(self::UPDATED_AT_KEY);

        return [
            'tasa' => $this->bolivianosPorDolar(),
            'fuente' => $this->fuente(),
            'actualizado' => $actualizado ?: now()->toIso8601String(),
            'es_respaldo' => $tasaOficial === null && $this->fuente() === 'OFICIAL',
        ];
    }

    public function actualizarOficialAhora(): ?float
    {
        $tasa = $this->consultarBancoCentral();

        if ($tasa === null) {
            return null;
        }

        $tasa = $this->guardarTasaOficial($tasa);
        Cache::put('finora.tipo-cambio.usd-bob.oficial', $tasa, now()->endOfDay()->addMinutes(5));

        return $tasa;
    }

    public function actualizarFuente(string $fuente, ?float $tasaManual = null): void
    {
        Cache::forever(self::MODE_KEY, $fuente);

        if ($tasaManual !== null) {
            Cache::forever(self::MANUAL_KEY, number_format($tasaManual, 6, '.', ''));
        }

        Cache::forget('finora.tipo-cambio.usd-bob.oficial');
    }

    private function guardarTasaOficial(?float $tasa): float
    {
        if ($tasa === null) {
            return (float) (Cache::get(self::OFFICIAL_KEY) ?: config('finora.usd_to_bob_fallback', 12.00));
        }

        Cache::forever(self::OFFICIAL_KEY, number_format($tasa, 6, '.', ''));
        Cache::forever(self::UPDATED_AT_KEY, now()->toIso8601String());

        return $tasa;
    }

    public function fuente(): string
    {
        return Cache::get(self::MODE_KEY) ?: 'OFICIAL';
    }

    public function manual(): ?float
    {
        $valor = Cache::get(self::MANUAL_KEY);

        return $valor !== null ? (float) $valor : null;
    }

    private function consultarBancoCentral(): ?float
    {
        try {
            $request = Http::accept('text/html')
                ->connectTimeout(3)
                ->retry(2, 250)
                ->timeout(5);

            if (! config('finora.exchange_rate_verify_ssl', false)) {
                $request = $request->withoutVerifying();
            }

            $response = $request->get(config('finora.exchange_rate_url'));

            if (! $response->successful()) {
                return null;
            }

            $textoPlano = preg_replace('/<[^>]+>/', ' ', $response->body()) ?? '';
            $contenido = preg_replace('/\s+/', ' ', html_entity_decode($textoPlano)) ?? '';

            if (preg_match('/ESTADOS\s+UNIDOS.*?\bUSD\b.*?(\d{1,3}(?:[\.,]\d{1,6})?)/i', $contenido, $matches) !== 1) {
                return null;
            }

            $tasa = (float) str_replace(',', '.', $matches[1]);

            return $tasa > 0 ? $tasa : null;
        } catch (\Throwable) {
            return null;
        }
    }
}
