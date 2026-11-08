<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProductoTest extends TestCase
{
    use RefreshDatabase;

    private function crearConsultor(): Usuario
    {
        return Usuario::create([
            'nombre' => 'Consultor',
            'email' => 'consultor.prod@finora.test',
            'password' => Hash::make('Password123!@#'),
            'rol' => 'CONSULTOR',
            'activo' => true,
        ]);
    }

    private function crearProveedorYCategoria(): array
    {
        $proveedor = Proveedor::create([
            'nombre' => 'Proveedor Test',
            'activo' => true,
        ]);

        $categoria = Categoria::create([
            'nombre' => 'Categoría Test',
            'activo' => true,
        ]);

        return [$proveedor, $categoria];
    }

    /**
     * Un invitado no puede acceder a las rutas administrativas de productos.
     */
    public function test_invitado_no_puede_gestionar_productos(): void
    {
        $response = $this->get('/panel/productos');
        $response->assertRedirect('/login');

        $responseCreate = $this->get('/panel/productos/create');
        $responseCreate->assertRedirect('/login');
    }

    /**
     * Usuarios con otros roles (CLIENTE) tienen acceso prohibido al CRUD, y COLABORADOR no puede crear productos.
     */
    public function test_roles_no_autorizados_son_bloqueados(): void
    {
        $cliente = Usuario::create([
            'nombre' => 'Cliente',
            'email' => 'cliente@finora.test',
            'password' => Hash::make('Clave123!@#'),
            'rol' => 'CLIENTE',
            'activo' => true,
        ]);

        $response = $this->actingAs($cliente)->get('/panel/productos');
        $response->assertStatus(403);

        $colaborador = Usuario::create([
            'nombre' => 'Colaborador',
            'email' => 'colab.prod@finora.test',
            'password' => Hash::make('Clave123!@#'),
            'rol' => 'COLABORADOR',
            'activo' => true,
        ]);

        $this->actingAs($colaborador)->get('/panel/productos/create')->assertStatus(403);
    }

    /**
     * El CONSULTOR puede acceder al listado de productos.
     */
    public function test_consultor_puede_ver_listado_de_productos(): void
    {
        $consultor = $this->crearConsultor();
        [$proveedor, $categoria] = $this->crearProveedorYCategoria();

        Producto::create([
            'codigo' => 'PROD-001',
            'nombre' => 'Producto de Prueba',
            'proveedor_id' => $proveedor->id,
            'categoria_id' => $categoria->id,
            'precio_venta_actual' => 15000.00,
            'publicado' => true,
            'activo' => true,
        ]);

        $response = $this->actingAs($consultor)->get('/panel/productos');
        $response->assertStatus(200);
        $response->assertSee('PROD-001');
        $response->assertSee('Producto de Prueba');
    }

    /**
     * El CONSULTOR puede registrar un nuevo producto mediante el formulario.
     */
    public function test_consultor_puede_crear_producto(): void
    {
        $consultor = $this->crearConsultor();
        [$proveedor, $categoria] = $this->crearProveedorYCategoria();

        $datos = [
            'codigo' => 'PROD-002',
            'nombre' => 'Nuevo Producto Natura',
            'descripcion' => 'Descripción corta',
            'proveedor_id' => $proveedor->id,
            'categoria_id' => $categoria->id,
            'precio_venta_actual' => 18990.00,
            'publicado' => '1',
            'activo' => '1',
        ];

        $response = $this->actingAs($consultor)->post('/panel/productos', $datos);

        $response->assertRedirect('/panel/productos');
        // 2.8: El nombre se convierte a mayúsculas antes de guardarse
        $this->assertDatabaseHas('productos', [
            'codigo' => 'PROD-002',
            'nombre' => 'NUEVO PRODUCTO NATURA',
            'precio_venta_actual' => 18990.00,
            'publicado' => true,
            'activo' => true,
        ]);
    }

    public function test_consultor_puede_crear_producto_con_stock_y_costo_inicial(): void
    {
        $consultor = $this->crearConsultor();
        [$proveedor, $categoria] = $this->crearProveedorYCategoria();

        $response = $this->actingAs($consultor)->post('/panel/productos', [
            'codigo' => 'PROD-STOCK-INI',
            'nombre' => 'Perfume Kaiak en Habitación',
            'proveedor_id' => $proveedor->id,
            'categoria_id' => $categoria->id,
            'marca_linea' => 'Kaiak',
            'precio_venta_actual' => 150.00,
            'stock_inicial' => 4,
            'costo_inicial' => 95.00,
            'publicado' => '1',
            'activo' => '1',
        ]);

        $response->assertRedirect('/panel/productos');
        $producto = Producto::where('codigo', 'PROD-STOCK-INI')->firstOrFail();
        $this->assertEquals(4, $producto->stock_fisico);
        $this->assertEquals(95.00, (float) $producto->costo_promedio);
    }

    /**
     * El código de producto debe ser único.
     */
    public function test_codigo_de_producto_debe_ser_unico(): void
    {
        $consultor = $this->crearConsultor();
        [$proveedor, $categoria] = $this->crearProveedorYCategoria();

        Producto::create([
            'codigo' => 'DUPLICADO-001',
            'nombre' => 'Producto Existente',
            'proveedor_id' => $proveedor->id,
            'categoria_id' => $categoria->id,
            'precio_venta_actual' => 10000.00,
            'publicado' => true,
            'activo' => true,
        ]);

        $response = $this->actingAs($consultor)->post('/panel/productos', [
            'codigo' => 'DUPLICADO-001',
            'nombre' => 'Otro Producto',
            'proveedor_id' => $proveedor->id,
            'categoria_id' => $categoria->id,
            'precio_venta_actual' => 12000.00,
        ]);

        $response->assertSessionHasErrors('codigo');
    }

    /**
     * El CONSULTOR puede editar un producto existente.
     */
    public function test_consultor_puede_editar_producto(): void
    {
        $consultor = $this->crearConsultor();
        [$proveedor, $categoria] = $this->crearProveedorYCategoria();

        $producto = Producto::create([
            'codigo' => 'EDIT-001',
            'nombre' => 'Nombre Original',
            'proveedor_id' => $proveedor->id,
            'categoria_id' => $categoria->id,
            'precio_venta_actual' => 10000.00,
            'publicado' => true,
            'activo' => true,
        ]);

        $response = $this->actingAs($consultor)->put("/panel/productos/{$producto->id}", [
            'codigo' => 'EDIT-001',
            'nombre' => 'Nombre Editado',
            'proveedor_id' => $proveedor->id,
            'categoria_id' => $categoria->id,
            'precio_venta_actual' => 12500.00,
            'publicado' => '1',
            'activo' => '1',
        ]);

        $response->assertRedirect('/panel/productos');
        // 2.8: El nombre se convierte a mayúsculas antes de guardarse
        $this->assertDatabaseHas('productos', [
            'id' => $producto->id,
            'nombre' => 'NOMBRE EDITADO',
            'precio_venta_actual' => 12500.00,
        ]);
    }

    /**
     * El catálogo público solo muestra productos activos y publicados.
     */
    public function test_catalogo_publico_solo_muestra_productos_activos_y_publicados(): void
    {
        [$proveedor, $categoria] = $this->crearProveedorYCategoria();

        // Producto 1: Activo y publicado (debe verse)
        Producto::create([
            'codigo' => 'PUB-001',
            'nombre' => 'Producto Visible en Catálogo',
            'proveedor_id' => $proveedor->id,
            'categoria_id' => $categoria->id,
            'precio_venta_actual' => 9990.00,
            'publicado' => true,
            'activo' => true,
        ]);

        // Producto 2: Borrador (no publicado) (NO debe verse)
        Producto::create([
            'codigo' => 'BORRADOR-001',
            'nombre' => 'Producto Oculto Borrador',
            'proveedor_id' => $proveedor->id,
            'categoria_id' => $categoria->id,
            'precio_venta_actual' => 9990.00,
            'publicado' => false,
            'activo' => true,
        ]);

        // Producto 3: Inactivo (NO debe verse)
        Producto::create([
            'codigo' => 'INACTIVO-001',
            'nombre' => 'Producto Desactivado',
            'proveedor_id' => $proveedor->id,
            'categoria_id' => $categoria->id,
            'precio_venta_actual' => 9990.00,
            'publicado' => true,
            'activo' => false,
        ]);

        $response = $this->get('/categorias');

        $response->assertStatus(200);
        $response->assertSee('Producto Visible en Catálogo');
        $response->assertDontSee('Producto Oculto Borrador');
        $response->assertDontSee('Producto Desactivado');
    }

    public function test_catalogo_publico_filtra_por_categoria_seleccionada(): void
    {
        $proveedor = Proveedor::create(['nombre' => 'Proveedor categorías', 'activo' => true]);
        $categoriaIncluida = Categoria::create(['nombre' => 'Cuidado Facial', 'activo' => true]);
        $categoriaExcluida = Categoria::create(['nombre' => 'Cuidado Corporal', 'activo' => true]);

        Producto::create([
            'proveedor_id' => $proveedor->id,
            'categoria_id' => $categoriaIncluida->id,
            'codigo' => 'FACIAL-001',
            'nombre' => 'Producto facial visible',
            'precio_venta_actual' => 20,
            'publicado' => true,
            'activo' => true,
        ]);
        Producto::create([
            'proveedor_id' => $proveedor->id,
            'categoria_id' => $categoriaExcluida->id,
            'codigo' => 'CORPORAL-001',
            'nombre' => 'Producto corporal oculto por filtro',
            'precio_venta_actual' => 25,
            'publicado' => true,
            'activo' => true,
        ]);

        $response = $this->get(route('categorias', ['categoria' => 'cuidado-facial']));

        $response->assertOk();
        $response->assertSee('Producto facial visible');
        $response->assertDontSee('data-product-category="cuidado-corporal"');
        $response->assertSee('Categoría seleccionada: Cuidado Facial');
    }

    /**
     * REGRESIÓN: La búsqueda por código exacto debe devolver el producto correcto.
     */
    public function test_busqueda_por_codigo_exacto_devuelve_producto(): void
    {
        $consultor = $this->crearConsultor();
        [$proveedor, $categoria] = $this->crearProveedorYCategoria();

        Producto::create([
            'codigo' => 'NAT-ILIA-001',
            'nombre' => 'Ilía Clásico Femenino 50ml',
            'proveedor_id' => $proveedor->id,
            'categoria_id' => $categoria->id,
            'precio_venta_actual' => 35.00,
            'publicado' => true,
            'activo' => true,
        ]);

        Producto::create([
            'codigo' => 'NAT-KAIA-001',
            'nombre' => 'Kaiak Aventura Masculino 100ml',
            'proveedor_id' => $proveedor->id,
            'categoria_id' => $categoria->id,
            'precio_venta_actual' => 38.00,
            'publicado' => true,
            'activo' => true,
        ]);

        $response = $this->actingAs($consultor)->get('/panel/productos?search=NAT-ILIA-001');

        $response->assertStatus(200);
        $response->assertSee('NAT-ILIA-001');
        $response->assertDontSee('NAT-KAIA-001');
    }

    /**
     * REGRESIÓN: La búsqueda parcial por nombre debe devolver coincidencias.
     */
    public function test_busqueda_parcial_por_nombre_devuelve_coincidencias(): void
    {
        $consultor = $this->crearConsultor();
        [$proveedor, $categoria] = $this->crearProveedorYCategoria();

        Producto::create([
            'codigo' => 'KAIA-001',
            'nombre' => 'Kaiak Aventura',
            'proveedor_id' => $proveedor->id,
            'categoria_id' => $categoria->id,
            'precio_venta_actual' => 38.00,
            'publicado' => true,
            'activo' => true,
        ]);

        Producto::create([
            'codigo' => 'ILIA-001',
            'nombre' => 'Ilía Clásico',
            'proveedor_id' => $proveedor->id,
            'categoria_id' => $categoria->id,
            'precio_venta_actual' => 35.00,
            'publicado' => true,
            'activo' => true,
        ]);

        $response = $this->actingAs($consultor)->get('/panel/productos?search=Kaiak');

        $response->assertStatus(200);
        $response->assertSee('Kaiak Aventura');
        $response->assertDontSee('Ilía Clásico');
    }

    /**
     * REGRESIÓN: Búsqueda con filtros en «Todos» (categoria_id='', publicado='') no debe filtrar resultados.
     * Caso reportado: buscar «prueba» con «Todas las categorías» y «Todos los estados»
     * muestra listado vacío cuando debería mostrar todos los que coincidan con el término.
     */
    public function test_busqueda_con_filtros_todos_no_restringe_resultados(): void
    {
        $consultor = $this->crearConsultor();
        [$proveedor, $categoria] = $this->crearProveedorYCategoria();

        // Producto publicado
        Producto::create([
            'codigo' => 'PRUEBA-001',
            'nombre' => 'Producto Prueba Publicado',
            'proveedor_id' => $proveedor->id,
            'categoria_id' => $categoria->id,
            'precio_venta_actual' => 20.00,
            'publicado' => true,
            'activo' => true,
        ]);

        // Producto borrador (no publicado)
        Producto::create([
            'codigo' => 'PRUEBA-002',
            'nombre' => 'Producto Prueba Borrador',
            'proveedor_id' => $proveedor->id,
            'categoria_id' => $categoria->id,
            'precio_venta_actual' => 25.00,
            'publicado' => false,
            'activo' => true,
        ]);

        // Producto sin coincidencia
        Producto::create([
            'codigo' => 'OTRO-001',
            'nombre' => 'Otro Producto',
            'proveedor_id' => $proveedor->id,
            'categoria_id' => $categoria->id,
            'precio_venta_actual' => 30.00,
            'publicado' => true,
            'activo' => true,
        ]);

        // Simula exactamente el formulario con «Todas las categorías» y «Todos los estados»:
        // el formulario envía categoria_id='' y publicado='' como strings vacíos vía GET.
        $response = $this->actingAs($consultor)
            ->get('/panel/productos?search=Prueba&categoria_id=&publicado=');

        $response->assertStatus(200);

        // Ambos productos «Prueba» deben aparecer, independientemente del estado publicado
        $response->assertSee('Producto Prueba Publicado');
        $response->assertSee('Producto Prueba Borrador');
        // El que no coincide con el término no debe aparecer
        $response->assertDontSee('Otro Producto');
    }

    /**
     * REGRESIÓN: Búsqueda por código exacto con filtros en «Todos».
     */
    public function test_busqueda_por_codigo_con_filtros_todos_devuelve_coincidencia(): void
    {
        $consultor = $this->crearConsultor();
        [$proveedor, $categoria] = $this->crearProveedorYCategoria();

        Producto::create([
            'codigo' => 'NAT-FACIAL-001',
            'nombre' => 'Crema Facial Hidratante',
            'proveedor_id' => $proveedor->id,
            'categoria_id' => $categoria->id,
            'precio_venta_actual' => 15.00,
            'publicado' => true,
            'activo' => true,
        ]);

        $response = $this->actingAs($consultor)
            ->get('/panel/productos?search=NAT-FACIAL-001&categoria_id=&publicado=');

        $response->assertStatus(200);
        $response->assertSee('NAT-FACIAL-001');
        $response->assertSee('Crema Facial Hidratante');
    }

    /**
     * REGRESIÓN: Filtros específicos por categoría y estado publicado restringen correctamente los resultados.
     */
    public function test_busqueda_con_filtros_especificos_filtra_correctamente(): void
    {
        $consultor = $this->crearConsultor();
        [$proveedor, $catFacial] = $this->crearProveedorYCategoria();

        $catCorporal = Categoria::create([
            'nombre' => 'Cuidado Corporal',
            'activo' => true,
        ]);

        // Producto en Cuidado Facial y Publicado
        Producto::create([
            'codigo' => 'FAC-001',
            'nombre' => 'Sérum Facial',
            'proveedor_id' => $proveedor->id,
            'categoria_id' => $catFacial->id,
            'precio_venta_actual' => 40.00,
            'publicado' => true,
            'activo' => true,
        ]);

        // Producto en Cuidado Facial pero Borrador
        Producto::create([
            'codigo' => 'FAC-002',
            'nombre' => 'Mascarilla Facial',
            'proveedor_id' => $proveedor->id,
            'categoria_id' => $catFacial->id,
            'precio_venta_actual' => 20.00,
            'publicado' => false,
            'activo' => true,
        ]);

        // Producto en Cuidado Corporal y Publicado
        Producto::create([
            'codigo' => 'CORP-001',
            'nombre' => 'Crema Corporal',
            'proveedor_id' => $proveedor->id,
            'categoria_id' => $catCorporal->id,
            'precio_venta_actual' => 30.00,
            'publicado' => true,
            'activo' => true,
        ]);

        // 1. Filtrar por Cuidado Facial + Publicados + término "Facial"
        $resp1 = $this->actingAs($consultor)
            ->get("/panel/productos?search=Facial&categoria_id={$catFacial->id}&publicado=1");

        $resp1->assertStatus(200);
        $resp1->assertSee('Sérum Facial');
        $resp1->assertDontSee('Mascarilla Facial');
        $resp1->assertDontSee('Crema Corporal');

        // 2. Filtrar por Cuidado Facial + Borradores (publicado=0)
        $resp2 = $this->actingAs($consultor)
            ->get("/panel/productos?search=&categoria_id={$catFacial->id}&publicado=0");

        $resp2->assertStatus(200);
        $resp2->assertSee('Mascarilla Facial');
        $resp2->assertDontSee('Sérum Facial');
    }

    /**
     * REGRESIÓN: Consulta sin término de búsqueda y sin filtros devuelve todos los productos.
     */
    public function test_consulta_sin_filtros_devuelve_todos_los_productos(): void
    {
        $consultor = $this->crearConsultor();
        [$proveedor, $categoria] = $this->crearProveedorYCategoria();

        Producto::create([
            'codigo' => 'PROD-A',
            'nombre' => 'Producto A',
            'proveedor_id' => $proveedor->id,
            'categoria_id' => $categoria->id,
            'precio_venta_actual' => 10.00,
            'publicado' => true,
            'activo' => true,
        ]);

        Producto::create([
            'codigo' => 'PROD-B',
            'nombre' => 'Producto B',
            'proveedor_id' => $proveedor->id,
            'categoria_id' => $categoria->id,
            'precio_venta_actual' => 12.00,
            'publicado' => false,
            'activo' => true,
        ]);

        $response = $this->actingAs($consultor)->get('/panel/productos');

        $response->assertStatus(200);
        $response->assertSee('Producto A');
        $response->assertSee('Producto B');
    }

    /**
     * REGRESIÓN: Botón de reinicio de filtros aparece cuando hay parámetros de búsqueda activos.
     */
    public function test_boton_reinicio_filtros_esta_presente_cuando_hay_busqueda_activa(): void
    {
        $consultor = $this->crearConsultor();

        // Sin búsqueda no se muestra botón de limpiar (o sólo con parámetros)
        $respNormal = $this->actingAs($consultor)->get('/panel/productos');
        $respNormal->assertStatus(200);

        // Con búsqueda activa se muestra el botón con restart_alt
        $respBusqueda = $this->actingAs($consultor)->get('/panel/productos?search=prueba');
        $respBusqueda->assertStatus(200);
        $respBusqueda->assertSee('Limpiar filtros');
        $respBusqueda->assertSee('restart_alt');
    }

    /**
     * REGRESIÓN: Al editar otros atributos sin enviar imagen, la imagen se conserva.
     */
    public function test_editar_producto_sin_imagen_conserva_imagen_existente(): void
    {
        $consultor = $this->crearConsultor();
        [$proveedor, $categoria] = $this->crearProveedorYCategoria();

        $producto = Producto::create([
            'codigo' => 'IMG-001',
            'nombre' => 'Producto con Imagen',
            'proveedor_id' => $proveedor->id,
            'categoria_id' => $categoria->id,
            'precio_venta_actual' => 20.00,
            'imagen_principal' => 'demo/productos/ilia.jpg',
            'publicado' => true,
            'activo' => true,
        ]);

        $response = $this->actingAs($consultor)->put("/panel/productos/{$producto->id}", [
            'codigo' => 'IMG-001',
            'nombre' => 'Nombre Actualizado',
            'proveedor_id' => $proveedor->id,
            'categoria_id' => $categoria->id,
            'precio_venta_actual' => 25.00,
            'publicado' => '1',
            'activo' => '1',
            // No se envía 'imagen' ni 'imagen_url'
        ]);

        $response->assertRedirect('/panel/productos');
        // 2.8: El nombre se convierte a mayúsculas antes de guardarse
        $this->assertDatabaseHas('productos', [
            'id' => $producto->id,
            'nombre' => 'NOMBRE ACTUALIZADO',
            'imagen_principal' => 'demo/productos/ilia.jpg', // debe conservarse
        ]);
    }

    /**
     * REGRESIÓN: El accesor imagen_url resuelve correctamente los tres formatos de ruta.
     */
    public function test_accesor_imagen_url_resuelve_rutas_correctamente(): void
    {
        [$proveedor, $categoria] = $this->crearProveedorYCategoria();

        // 1. Imagen demo
        $demo = Producto::create([
            'codigo' => 'DEMO-001', 'nombre' => 'Demo', 'proveedor_id' => $proveedor->id,
            'categoria_id' => $categoria->id, 'precio_venta_actual' => 10,
            'imagen_principal' => 'demo/productos/ilia.jpg',
        ]);
        $this->assertStringContainsString('images/demo/productos/ilia.jpg', $demo->imagen_url);

        // 2. Imagen subida al storage
        $storage = Producto::create([
            'codigo' => 'STO-001', 'nombre' => 'Storage', 'proveedor_id' => $proveedor->id,
            'categoria_id' => $categoria->id, 'precio_venta_actual' => 10,
            'imagen_principal' => 'productos/archivo.jpg',
        ]);
        $this->assertStringContainsString('storage/productos/archivo.jpg', $storage->imagen_url);

        // 3. URL externa
        $externa = Producto::create([
            'codigo' => 'EXT-001', 'nombre' => 'Externa', 'proveedor_id' => $proveedor->id,
            'categoria_id' => $categoria->id, 'precio_venta_actual' => 10,
            'imagen_principal' => 'https://ejemplo.com/img.jpg',
        ]);
        $this->assertSame('https://ejemplo.com/img.jpg', $externa->imagen_url);

        // 4. Sin imagen
        $sinImagen = Producto::create([
            'codigo' => 'NIL-001', 'nombre' => 'Sin imagen', 'proveedor_id' => $proveedor->id,
            'categoria_id' => $categoria->id, 'precio_venta_actual' => 10,
        ]);
        $this->assertNull($sinImagen->imagen_url);
    }
}
