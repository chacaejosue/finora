<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductoRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta solicitud.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->rol === 'CONSULTOR';
    }

    protected function prepareForValidation(): void
    {
        $merge = [];
        if ($this->has('codigo')) {
            $merge['codigo'] = mb_strtoupper(trim((string) $this->input('codigo')), 'UTF-8');
        }
        if ($this->has('nombre')) {
            $merge['nombre'] = mb_strtoupper(trim((string) $this->input('nombre')), 'UTF-8');
        }
        if ($this->has('descripcion') && $this->filled('descripcion')) {
            $merge['descripcion'] = mb_strtoupper(trim((string) $this->input('descripcion')), 'UTF-8');
        }
        if (! empty($merge)) {
            $this->merge($merge);
        }
    }

    /**
     * Reglas de validación aplicadas al producto.
     */
    public function rules(): array
    {
        $productoId = $this->route('producto') ? $this->route('producto')->id : null;

        return [
            'codigo' => [
                'required',
                'string',
                'max:80',
                Rule::unique('productos', 'codigo')->ignore($productoId),
            ],
            'nombre' => ['required', 'string', 'max:180'],
            'descripcion' => ['nullable', 'string'],
            'proveedor_id' => ['required', 'exists:proveedores,id'],
            'categoria_id' => ['required', 'exists:categorias,id'],
            'marca_linea' => ['nullable', 'string', 'max:120'],
            'precio_venta_actual' => ['required', 'numeric', 'min:0'],
            'en_oferta' => ['sometimes', 'boolean'],
            'precio_oferta' => ['nullable', 'numeric', 'min:0'],
            'stock_inicial' => ['nullable', 'integer', 'min:0'],
            'costo_inicial' => ['nullable', 'numeric', 'min:0'],
            'costo_promedio' => ['nullable', 'numeric', 'min:0'],
            'imagen' => ['nullable', 'image', 'max:2048'],
            'imagen_url' => ['nullable', 'url', 'max:255'],
            'publicado' => ['sometimes', 'boolean'],
            'activo' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Nombres de atributos personalizados para mensajes de error.
     */
    public function attributes(): array
    {
        return [
            'codigo' => 'código de producto',
            'nombre' => 'nombre del producto',
            'proveedor_id' => 'proveedor',
            'categoria_id' => 'categoría',
            'linea_id' => 'línea',
            'precio_venta_actual' => 'precio de venta',
            'precio_oferta' => 'precio de oferta',
            'en_oferta' => 'estado de oferta',
            'imagen' => 'imagen del producto',
            'imagen_url' => 'URL de imagen externa',
        ];
    }

    /**
     * Mensajes de error personalizados en español para el módulo de productos.
     * Complementan el archivo lang/es/validation.php y sirven de respaldo.
     */
    public function messages(): array
    {
        return [
            'codigo.required' => 'El código de producto es obligatorio.',
            'codigo.unique' => 'El código ":input" ya está en uso. Utilice un código único.',
            'codigo.max' => 'El código no puede superar los :max caracteres.',
            'nombre.required' => 'El nombre comercial del producto es obligatorio.',
            'nombre.max' => 'El nombre no puede superar los :max caracteres.',
            'precio_venta_actual.required' => 'El precio de venta es obligatorio.',
            'precio_venta_actual.numeric' => 'El precio de venta debe ser un número válido (ej. 35.00).',
            'precio_venta_actual.min' => 'El precio de venta no puede ser negativo.',
            'proveedor_id.required' => 'Debe seleccionar un proveedor.',
            'proveedor_id.exists' => 'El proveedor seleccionado no existe en el sistema.',
            'categoria_id.required' => 'Debe seleccionar una categoría.',
            'categoria_id.exists' => 'La categoría seleccionada no existe en el sistema.',
            'linea_id.exists' => 'La línea comercial seleccionada no existe en el sistema.',
            'imagen.image' => 'El archivo seleccionado no es una imagen válida (JPG, PNG, WebP, GIF).',
            'imagen.max' => 'La imagen no puede superar los 2 MB.',
            'imagen_url.url' => 'La URL de imagen externa no es válida. Debe comenzar con http:// o https://.',
            'imagen_url.max' => 'La URL de imagen no puede superar los :max caracteres.',
        ];
    }
}
