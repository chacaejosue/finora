<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CatalogoController extends Controller
{
    public function landing(): View
    {
        $categorias = Categoria::with(['productos' => function ($query) {
            $query->where('activo', true)->where('publicado', true)->latest('id');
        }])->where('activo', true)->orderBy('nombre')->get();

        $productos = Producto::with(['categoria', 'proveedor'])
            ->where('activo', true)
            ->where('publicado', true)
            ->whereHas('categoria', fn ($query) => $query->where('activo', true))
            ->latest('id')
            ->get();

        return view('landing', compact('categorias', 'productos'));
    }

    /**
     * Muestra el catálogo público de productos clasificados por categoría.
     */
    public function index(Request $request)
    {
        $categorias = Categoria::where('activo', true)
            ->orderBy('nombre', 'asc')
            ->get();

        $productosBusqueda = Producto::with('categoria')
            ->where('activo', true)
            ->where('publicado', true)
            ->whereHas('categoria', fn ($query) => $query->where('activo', true))
            ->orderBy('nombre')
            ->get(['id', 'categoria_id', 'nombre']);

        $categoriaSlug = $request->string('categoria')->toString();
        $categoriaSeleccionada = $categorias->first(
            fn (Categoria $categoria): bool => Str::slug($categoria->nombre) === $categoriaSlug
        );

        $productos = Producto::with(['categoria', 'proveedor'])
            ->where('activo', true)
            ->where('publicado', true)
            ->whereHas('categoria', function ($query) {
                $query->where('activo', true);
            })
            ->when($categoriaSeleccionada, fn ($query) => $query->where('categoria_id', $categoriaSeleccionada->id))
            ->orderBy('nombre', 'asc')
            ->get();

        return view('categorias', [
            'categorias' => $categorias,
            'productos' => $productos,
            'productosBusqueda' => $productosBusqueda,
            'categoriaSeleccionada' => $categoriaSeleccionada,
        ]);
    }
}
