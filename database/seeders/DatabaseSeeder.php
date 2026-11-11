<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Proveedor;
use App\Models\Usuario;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Semilla base del sistema Finora en estado limpio (sin productos ficticios ni ventas de prueba).
     */
    public function run(): void
    {
        // 1. Plan de cuentas contables oficial de Bolivia (indispensable para que el motor contable funcione)
        $this->call([
            ContabilidadBoliviaSeeder::class,
        ]);

        // 2. Usuario Consultor principal para acceso al panel
        $consultorEmail = (string) config('finora.demo_consultor_email', 'consultora.demo@finora.test');
        $consultorPassword = (string) config('finora.demo_consultor_password', 'FinoraDemo2026!');

        if (! Usuario::where('email', $consultorEmail)->exists()) {
            Usuario::create([
                'nombre' => 'Consultora',
                'apellido' => 'Finora',
                'email' => $consultorEmail,
                'telefono' => '70000000',
                'genero' => 'FEMENINO',
                'password' => Hash::make($consultorPassword),
                'rol' => 'CONSULTOR',
                'activo' => true,
            ]);
        }

        // 3. Proveedor oficial por defecto
        if (! Proveedor::where('nombre', 'Natura Cosméticos Bolivia')->exists()) {
            Proveedor::create([
                'nombre' => 'Natura Cosméticos Bolivia',
                'telefono' => '800-10-6288',
                'email' => 'atencion@natura.com.bo',
                'sitio_web' => 'https://www.natura.com.bo',
                'activo' => true,
            ]);
        }

        // 4. Categorías base de la consultoría Natura (para poder registrar productos de inmediato)
        $categoriasBase = [
            ['nombre' => 'Perfumería', 'descripcion' => 'Fragancias y perfumería fina masculina y femenina.'],
            ['nombre' => 'Cuidado facial', 'descripcion' => 'Tratamientos Chronos, antiseñales y limpieza de cutis.'],
            ['nombre' => 'Cuidado corporal', 'descripcion' => 'Hidratantes Tododia, aceites trifásicos Ekos y jabones.'],
            ['nombre' => 'Maquillaje', 'descripcion' => 'Bases, labiales y productos de belleza UNA y Faces.'],
            ['nombre' => 'Higiene personal', 'descripcion' => 'Desodorantes, shampoo, acondicionadores y cuidado diario.'],
        ];

        foreach ($categoriasBase as $cat) {
            if (! Categoria::where('nombre', $cat['nombre'])->exists()) {
                Categoria::create([
                    'nombre' => $cat['nombre'],
                    'descripcion' => $cat['descripcion'],
                    'activo' => true,
                ]);
            }
        }
    }
}
