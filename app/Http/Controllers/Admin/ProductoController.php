<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductoRequest;
use App\Models\Categoria;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Muestra el listado de productos con filtros y paginación.
     */
    public function index(Request $request)
    {
        $query = Producto::with(['proveedor', 'categoria'])
            ->withSum('movimientosInventario as stock_fisico_calc', 'cantidad')
            ->selectSub(
                \App\Models\DetallePedido::selectRaw('COALESCE(SUM(cantidad_reservada), 0)')
                    ->whereColumn('producto_id', 'productos.id')
                    ->whereHas('pedido', function ($q) {
                        $q->whereIn('estado', ['PENDIENTE', 'RESERVADO', 'PENDIENTE_ABASTECIMIENTO', 'LISTO_ENTREGA']);
                    }),
                'cantidad_reservada_calc'
            );

        // Filtro por término de búsqueda (código o nombre)
        if ($search = trim((string) $request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('codigo', 'like', "%{$search}%");
            });
        }

        // Filtro por categoría
        if ($categoriaId = $request->input('categoria_id')) {
            $query->where('categoria_id', $categoriaId);
        }

        // Filtro por marca o línea
        if ($marcaLinea = trim((string) $request->input('marca_linea', ''))) {
            $query->where('marca_linea', 'like', "%{$marcaLinea}%");
        }

        // Filtro por estado activo
        if ($request->filled('activo')) {
            $query->where('activo', $request->boolean('activo'));
        }

        // Filtro por estado publicado
        if ($request->filled('publicado')) {
            $query->where('publicado', $request->boolean('publicado'));
        }

        $productos = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        $categorias = Categoria::orderBy('nombre')->get();
        $proveedores = Proveedor::orderBy('nombre')->get();

        return view('panel.productos.index', [
            'productos' => $productos,
            'categorias' => $categorias,
            'proveedores' => $proveedores,
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo producto.
     */
    public function create()
    {
        $proveedores = Proveedor::where('activo', true)->orderBy('nombre')->get();
        $categorias = Categoria::where('activo', true)->orderBy('nombre')->get();

        return view('panel.productos.create', [
            'proveedores' => $proveedores,
            'categorias' => $categorias,
        ]);
    }

    /**
     * Almacena un nuevo producto en la base de datos.
     */
    public function store(ProductoRequest $request)
    {
        $datos = $request->validated();
        $stockInicial = (int) ($datos['stock_inicial'] ?? 0);
        $costoInicial = (float) ($datos['costo_inicial'] ?? 0);
        unset($datos['stock_inicial'], $datos['costo_inicial']);

        // Manejo de la carga de imagen en almacenamiento público local (guardando ruta relativa)
        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('productos', 'public');
            $datos['imagen_principal'] = $path;
        } elseif ($request->filled('imagen_url')) {
            $datos['imagen_principal'] = $request->input('imagen_url');
        }

        $datos['publicado'] = $request->boolean('publicado');
        $datos['activo'] = $request->boolean('activo', true);
        $datos['en_oferta'] = $request->boolean('en_oferta');
        if ($datos['en_oferta'] && ! empty($datos['precio_oferta']) && (float) $datos['precio_oferta'] > 0) {
            $datos['precio_oferta'] = (float) $datos['precio_oferta'];
        } else {
            $datos['precio_oferta'] = null;
            $datos['en_oferta'] = false;
        }
        if ($costoInicial > 0) {
            $datos['costo_promedio'] = $costoInicial;
        }

        $producto = Producto::create($datos);

        if ($stockInicial > 0) {
            MovimientoInventario::create([
                'producto_id' => $producto->id,
                'registrado_por_usuario_id' => $request->user()?->id,
                'tipo' => 'AJUSTE_ENTRADA',
                'cantidad' => $stockInicial,
                'costo_unitario' => $costoInicial,
                'costo_total' => round($stockInicial * $costoInicial, 2),
                'fecha' => now(),
                'observacion' => 'Stock inicial al registrar producto',
            ]);
        }

        return redirect()->route('panel.productos.index')
            ->with('exito', 'Producto registrado correctamente.');
    }

    /**
     * Muestra el formulario para editar un producto existente.
     */
    public function edit(Producto $producto)
    {
        $proveedores = Proveedor::where('activo', true)->orderBy('nombre')->get();
        $categorias = Categoria::where('activo', true)->orderBy('nombre')->get();

        return view('panel.productos.edit', [
            'producto' => $producto,
            'proveedores' => $proveedores,
            'categorias' => $categorias,
        ]);
    }

    /**
     * Actualiza la información del producto en la base de datos.
     */
    public function update(ProductoRequest $request, Producto $producto)
    {
        $datos = $request->validated();

        // Eliminar campos que no son columnas de la tabla
        unset($datos['imagen'], $datos['imagen_url'], $datos['stock_inicial'], $datos['costo_inicial']);

        if ($request->hasFile('imagen')) {
            // Nuevo archivo subido: reemplazar imagen
            $datos['imagen_principal'] = $request->file('imagen')->store('productos', 'public');
        } elseif ($request->filled('imagen_url')) {
            // URL externa provista: usar como imagen
            $datos['imagen_principal'] = $request->input('imagen_url');
        }
        // Si ninguno se envía: conservar imagen_principal existente (no se toca)

        $datos['publicado'] = $request->boolean('publicado');
        $datos['activo'] = $request->boolean('activo');
        $datos['en_oferta'] = $request->boolean('en_oferta');
        if ($datos['en_oferta'] && ! empty($datos['precio_oferta']) && (float) $datos['precio_oferta'] > 0) {
            $datos['precio_oferta'] = (float) $datos['precio_oferta'];
        } else {
            $datos['precio_oferta'] = null;
            $datos['en_oferta'] = false;
        }

        $producto->update($datos);

        return redirect()->route('panel.productos.index')
            ->with('exito', 'Producto actualizado correctamente.');
    }

    /**
     * Alterna la visibilidad pública o el estado activo de un producto.
     */
    public function toggleStatus(Request $request, Producto $producto)
    {
        if ($request->has('campo') && in_array($request->input('campo'), ['activo', 'publicado'])) {
            $campo = $request->input('campo');
            $producto->update([
                $campo => ! $producto->{$campo},
            ]);

            return redirect()->back()->with('exito', 'Estado actualizado correctamente.');
        }

        return redirect()->back();
    }

    /**
     * Desactiva el producto (desactivación lógica para preservar historial comercial).
     */
    public function destroy(Producto $producto)
    {
        $producto->update(['activo' => false]);

        return redirect()->route('panel.productos.index')
            ->with('exito', 'Producto desactivado correctamente.');
    }
}
