<?php

namespace App\Console\Commands;

use App\Models\Usuario;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

#[Signature('finora:crear-consultor')]
#[Description('Crea la cuenta inicial de consultor de Finora')]
class CrearConsultorCommand extends Command
{
    public function handle(): int
    {
        if (Usuario::where('rol', 'CONSULTOR')->exists()) {
            $this->error('Ya existe una cuenta CONSULTOR. Use el panel para crear colaboradores.');

            return self::FAILURE;
        }

        $nombre = trim((string) $this->ask('Nombre del consultor'));
        $email = strtolower(trim((string) $this->ask('Correo electrónico')));

        if ($nombre === '' || mb_strlen($nombre) > 100) {
            $this->error('El nombre es obligatorio y no puede superar los 100 caracteres.');

            return self::FAILURE;
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255) {
            $this->error('El correo electrónico no es válido.');

            return self::FAILURE;
        }

        if (Usuario::where('email', $email)->exists()) {
            $this->error('Ya existe una persona con ese correo electrónico.');

            return self::FAILURE;
        }

        $password = $this->secret('Contraseña (mínimo 12 caracteres)');
        $confirmacion = $this->secret('Confirmar contraseña');

        if (! is_string($password) || strlen($password) < 12 || $password !== $confirmacion) {
            $this->error('La contraseña debe tener al menos 12 caracteres y coincidir con su confirmación.');

            return self::FAILURE;
        }

        Usuario::create([
            'nombre' => $nombre,
            'email' => $email,
            'password' => Hash::make($password),
            'rol' => 'CONSULTOR',
            'activo' => true,
        ]);

        $this->info('Consultor registrado correctamente. Ya puede iniciar sesión en Finora.');

        return self::SUCCESS;
    }
}
