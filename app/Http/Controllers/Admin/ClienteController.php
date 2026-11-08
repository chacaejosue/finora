<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClienteRequest;
use App\Http\Requests\CrearAccesoClienteRequest;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Usuario::clientes();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('apellido', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('telefono', 'like', "%{$search}%");
            });
        }

        if ($request->has('activo') && $request->input('activo') !== '') {
            $query->where('activo', (bool) $request->input('activo'));
        }

        $clientes = $query->latest('id')->paginate(15)->withQueryString();

        return view('panel.clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('panel.clientes.create');
    }

    public function store(ClienteRequest $request)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            Usuario::create([
                'nombre' => $validated['nombre'],
                'apellido' => $validated['apellido'] ?? null,
                'telefono' => $validated['telefono'] ?? null,
                'email' => $validated['email'] ?? null,
                'direccion' => $validated['direccion'] ?? null,
                'observaciones' => $validated['observaciones'] ?? null,
                'rol' => 'CLIENTE',
                'password' => null,
                'activo' => $validated['activo'] ?? true,
            ]);
        });

        return redirect()->route('panel.clientes.index')
            ->with('exito', 'Cliente registrado exitosamente.');
    }

    public function show(Usuario $cliente)
    {
        $cliente->load(['pedidos.detalles.producto', 'ventas.detalles.producto', 'ventas.pagos']);

        $totalDeuda = $cliente->ventas
            ->where('estado', 'CONFIRMADA')
            ->sum(fn ($venta) => $venta->saldo_pendiente);

        return view('panel.clientes.show', compact('cliente', 'totalDeuda'));
    }

    public function edit(Usuario $cliente)
    {
        return view('panel.clientes.edit', compact('cliente'));
    }

    public function update(ClienteRequest $request, Usuario $cliente)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $cliente, $request) {
            $cliente->update([
                'nombre' => $validated['nombre'],
                'apellido' => $validated['apellido'] ?? null,
                'telefono' => $validated['telefono'] ?? null,
                'email' => $validated['email'] ?? null,
                'direccion' => $validated['direccion'] ?? null,
                'observaciones' => $validated['observaciones'] ?? null,
                'activo' => $request->has('activo') ? (bool) $request->input('activo') : $cliente->activo,
            ]);
        });

        return redirect()->route('panel.clientes.index')
            ->with('exito', 'Cliente actualizado exitosamente.');
    }

    public function toggleStatus(Usuario $cliente)
    {
        $cliente->update(['activo' => ! $cliente->activo]);

        $estadoText = $cliente->activo ? 'activado' : 'desactivado';

        return redirect()->back()
            ->with('exito', "Cliente {$estadoText} correctamente.");
    }

    public function crearAcceso(CrearAccesoClienteRequest $request, Usuario $cliente)
    {
        if (! empty($cliente->password)) {
            return redirect()->back()->withErrors(['acceso' => 'Este cliente ya tiene una cuenta de acceso.']);
        }

        DB::transaction(function () use ($request, $cliente): void {
            $cliente->update([
                'email' => $request->validated('email'),
                'password' => Hash::make($request->validated('password')),
                'rol' => 'CLIENTE',
                'activo' => true,
            ]);
        });

        return redirect()->back()->with('exito', 'Acceso del cliente creado correctamente.');
    }
}
