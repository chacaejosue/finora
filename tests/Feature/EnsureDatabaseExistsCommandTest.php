<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class EnsureDatabaseExistsCommandTest extends TestCase
{
    /**
     * Verifica que el comando retorne éxito en entorno con SQLite :memory:.
     */
    public function test_retorna_exito_en_sqlite_en_memoria(): void
    {
        $this->artisan('db:create')
            ->assertSuccessful();
    }

    /**
     * Verifica que el comando cree el archivo SQLite si no existe en disco.
     */
    public function test_crea_archivo_sqlite_si_no_existe(): void
    {
        $tempPath = database_path('test_verify_creation.sqlite');

        if (file_exists($tempPath)) {
            @unlink($tempPath);
        }

        Config::set('database.default', 'sqlite');
        Config::set('database.connections.sqlite.database', $tempPath);

        try {
            $this->artisan('db:create')
                ->assertSuccessful();

            $this->assertFileExists($tempPath);
        } finally {
            if (file_exists($tempPath)) {
                @unlink($tempPath);
            }
        }
    }

    /**
     * Verifica que controladores no automatizados informen y retornen éxito.
     */
    public function test_controlador_no_gestionado_retorna_exito(): void
    {
        Config::set('database.default', 'redis');

        $this->artisan('db:create')
            ->assertSuccessful();
    }
}
