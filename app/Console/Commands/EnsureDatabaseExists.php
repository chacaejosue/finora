<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use PDO;
use Throwable;

#[Signature('db:create')]
#[Description('Crea la base de datos configurada si no existe.')]
class EnsureDatabaseExists extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $connection = config('database.default');

        if ($connection === 'sqlite') {
            return $this->asegurarSqlite();
        }

        if (in_array($connection, ['mysql', 'mariadb'], true)) {
            return $this->asegurarMysql((string) $connection);
        }

        $this->components->info("El controlador de base de datos [{$connection}] no requiere creación previa automatizada.");

        return self::SUCCESS;
    }

    /**
     * Asegura la existencia del archivo de base de datos SQLite.
     */
    private function asegurarSqlite(): int
    {
        $databasePath = config('database.connections.sqlite.database');

        if ($databasePath === ':memory:' || empty($databasePath)) {
            return self::SUCCESS;
        }

        if (! file_exists($databasePath)) {
            $directory = dirname((string) $databasePath);

            if (! is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            touch($databasePath);
            $this->components->info("Archivo de base de datos SQLite creado en [{$databasePath}].");
        } else {
            $this->components->info("La base de datos SQLite ya existe en [{$databasePath}].");
        }

        return self::SUCCESS;
    }

    /**
     * Asegura la existencia de la base de datos en MySQL / MariaDB.
     */
    private function asegurarMysql(string $connection): int
    {
        $config = config("database.connections.{$connection}");

        $database = $config['database'] ?? null;
        $host = $config['host'] ?? '127.0.0.1';
        $port = $config['port'] ?? 3306;
        $username = $config['username'] ?? 'root';
        $password = $config['password'] ?? '';
        $charset = $config['charset'] ?? 'utf8mb4';
        $collation = $config['collation'] ?? 'utf8mb4_unicode_ci';

        if (empty($database)) {
            $this->components->error('No se ha definido el nombre de la base de datos en la configuración.');

            return self::FAILURE;
        }

        try {
            $dsn = "mysql:host={$host};port={$port}";
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);

            $statement = sprintf(
                'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET %s COLLATE %s;',
                str_replace('`', '``', (string) $database),
                $charset,
                $collation
            );

            $pdo->exec($statement);

            $this->components->info("Base de datos MySQL [{$database}] verificada/creada exitosamente.");

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->components->error("No se pudo conectar al servidor MySQL para verificar la base de datos: {$e->getMessage()}");
            $this->components->warn('Asegúrate de que el servicio de MySQL esté iniciado y que las credenciales en .env sean correctas.');

            return self::FAILURE;
        }
    }
}
