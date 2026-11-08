<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Procesa la autenticación del usuario.
     */
    public function login(Request $request)
    {
        // Validar los datos del formulario.
        $datos = $request->validate([
            'identity' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ]);

        // Buscar al usuario activo por correo electrónico.
        $usuario = Usuario::where('email', $datos['identity'])
            ->where('activo', true)
            ->first();

        // Comprobar que existe, que tiene contraseña de acceso y que es correcta.
        if (! $usuario || empty($usuario->password) || ! Hash::check($datos['password'], $usuario->password)) {
            throw ValidationException::withMessages([
                'identity' => 'Las credenciales proporcionadas son incorrectas.',
            ]);
        }

        // Actualizar el último acceso del usuario.
        $usuario->forceFill([
            'ultimo_acceso' => now(),
        ])->save();

        // Iniciar la sesión.
        Auth::login($usuario, $request->boolean('remember'));

        // Regenerar la sesión por seguridad.
        $request->session()->regenerate();

        // Redirigir según el rol del usuario o responder JSON si es petición AJAX.
        $targetUrl = match ($usuario->rol) {
            'CLIENTE' => route('mi-cuenta'),
            'COLABORADOR' => route('panel.colaborador'),
            default => route('panel'),
        };

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => session()->pull('url.intended', $targetUrl),
            ]);
        }

        return redirect()->intended($targetUrl);
    }

    /**
     * Cierra la sesión activa del usuario.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('landing')
            ->withHeaders([
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
            ]);
    }
}
