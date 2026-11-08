<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UsuarioRequest;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Usuario::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('apellido', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('rol')) {
            $query->where('rol', $request->input('rol'));
        }

        $usuarios = $query->latest('id')->paginate(15)->withQueryString();

        return view('panel.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('panel.usuarios.create');
    }

    public function store(UsuarioRequest $request)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            Usuario::create([
                'nombre' => $validated['nombre'],
                'apellido' => $validated['apellido'] ?? null,
                'telefono' => $validated['telefono'] ?? null,
                'email' => $validated['email'],
                'genero' => $validated['genero'] ?? null,
                'direccion' => $validated['direccion'] ?? null,
                'password' => Hash::make($validated['password']),
                'rol' => $validated['rol'],
                'activo' => $validated['activo'] ?? true,
            ]);
        });

        return redirect()->route('panel.usuarios.index')
            ->with('exito', 'Usuario creado exitosamente.');
    }

    public function edit(Usuario $usuario)
    {
        return view('panel.usuarios.edit', compact('usuario'));
    }

    public function update(UsuarioRequest $request, Usuario $usuario)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $usuario) {
            $userData = [
                'nombre' => $validated['nombre'],
                'apellido' => $validated['apellido'] ?? null,
                'telefono' => $validated['telefono'] ?? null,
                'email' => $validated['email'],
                'genero' => $validated['genero'] ?? null,
                'direccion' => $validated['direccion'] ?? null,
                'rol' => $validated['rol'],
                'activo' => isset($validated['activo']) ? (bool) $validated['activo'] : $usuario->activo,
            ];

            if (! empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $usuario->update($userData);
        });

        return redirect()->route('panel.usuarios.index')
            ->with('exito', 'Usuario actualizado exitosamente.');
    }

    public function toggleStatus(Usuario $usuario)
    {
        if (auth()->id() === $usuario->id) {
            return redirect()->back()
                ->withErrors(['error' => 'No puedes desactivar tu propia cuenta mientras tienes la sesión activa.']);
        }

        $usuario->update(['activo' => ! $usuario->activo]);

        $estadoText = $usuario->activo ? 'activado' : 'desactivado';

        return redirect()->back()
            ->with('exito', "Usuario {$estadoText} correctamente.");
    }
}
