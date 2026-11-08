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

        // 1. Productos con likes prioritarios (los más votados por la comunidad con corazones)
        $likedProducts = $productos->filter(fn ($p) => (int) $p->likes > 0)
            ->sortByDesc('likes')
            ->take(4);

        // 2. Si hay menos de 4 o ninguno tiene likes, se escogen al azar para completar la portada
        $needed = 4 - $likedProducts->count();
        $randomFill = collect();
        if ($needed > 0) {
            $likedIds = $likedProducts->pluck('id');
            $randomFill = $productos->reject(fn ($p) => $likedIds->contains($p->id))
                ->shuffle()
                ->take($needed);
        }

        $destacados = $likedProducts->concat($randomFill)->values();

        return view('landing', compact('categorias', 'productos', 'destacados'));
    }

    /**
     * Muestra el catálogo público de productos clasificados por categoría y ofertas.
     */
    public function index(Request $request)
    {
        $categorias = Categoria::where('activo', true)
            ->withCount(['productos' => function ($q) {
                $q->where('activo', true)->where('publicado', true);
            }])
            ->orderBy('nombre', 'asc')
            ->get();

        $productosBusqueda = Producto::with('categoria')
            ->where('activo', true)
            ->where('publicado', true)
            ->whereHas('categoria', fn ($query) => $query->where('activo', true))
            ->orderBy('nombre')
            ->get(['id', 'categoria_id', 'nombre', 'precio_venta_actual', 'precio_oferta', 'en_oferta']);

        $categoriaSlug = $request->string('categoria')->toString();
        $soloOfertas = $request->boolean('ofertas') || $categoriaSlug === 'ofertas' || $request->string('filtro')->toString() === 'ofertas';

        $categoriaSeleccionada = $soloOfertas ? null : $categorias->first(
            fn (Categoria $categoria): bool => Str::slug($categoria->nombre) === $categoriaSlug
        );

        $totalOfertas = Producto::where('activo', true)
            ->where('publicado', true)
            ->whereHas('categoria', fn ($q) => $q->where('activo', true))
            ->enOferta()
            ->count();

        $productos = Producto::with(['categoria', 'proveedor'])
            ->where('activo', true)
            ->where('publicado', true)
            ->whereHas('categoria', function ($query) {
                $query->where('activo', true);
            })
            ->when($soloOfertas, fn ($query) => $query->enOferta())
            ->when(! $soloOfertas && $categoriaSeleccionada, fn ($query) => $query->where('categoria_id', $categoriaSeleccionada->id))
            ->orderBy('nombre', 'asc')
            ->get();

        return view('categorias', [
            'categorias' => $categorias,
            'productos' => $productos,
            'productosBusqueda' => $productosBusqueda,
            'categoriaSeleccionada' => $categoriaSeleccionada,
            'soloOfertas' => $soloOfertas,
            'totalOfertas' => $totalOfertas,
        ]);
    }

    /**
     * Acceso directo al catálogo filtrado por productos en oferta.
     */
    public function ofertas(Request $request)
    {
        $request->merge(['ofertas' => 1]);

        return $this->index($request);
    }

    /**
     * Alterna o registra el me gusta (corazón) de un producto.
     */
    public function toggleLike(Request $request, Producto $producto)
    {
        $action = $request->input('action'); // 'like' | 'unlike' | null

        if ($action === 'unlike') {
            $producto->likes = max(0, ((int) $producto->likes) - 1);
            $liked = false;
        } elseif ($action === 'like') {
            $producto->likes = ((int) $producto->likes) + 1;
            $liked = true;
        } else {
            $likedIds = session()->get('finora_liked_products', []);
            if (in_array($producto->id, $likedIds)) {
                $producto->likes = max(0, ((int) $producto->likes) - 1);
                $likedIds = array_diff($likedIds, [$producto->id]);
                $liked = false;
            } else {
                $producto->likes = ((int) $producto->likes) + 1;
                $likedIds[] = $producto->id;
                $liked = true;
            }
            session()->put('finora_liked_products', array_values($likedIds));
        }

        $producto->save();

        return response()->json([
            'success' => true,
            'likes' => (int) $producto->likes,
            'liked' => $liked,
        ]);
    }
}
