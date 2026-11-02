<?php

namespace App\Providers;

use App\Models\Compra;
use App\Models\MovimientoInventario;
use App\Models\Pago;
use App\Models\Venta;
use App\Observers\CompraObserver;
use App\Observers\MovimientoInventarioObserver;
use App\Observers\PagoObserver;
use App\Observers\VentaObserver;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::directive('money', function (string $expression): string {
            return "<?php echo e(\\App\\Support\\Money::format($expression)); ?>";
        });

        // Observers Contables (Normativa Bolivia - Integración Desacoplada)
        Venta::observe(VentaObserver::class);
        Compra::observe(CompraObserver::class);
        Pago::observe(PagoObserver::class);
        MovimientoInventario::observe(MovimientoInventarioObserver::class);
    }
}
