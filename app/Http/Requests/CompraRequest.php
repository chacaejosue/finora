<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && in_array($this->user()->rol, ['CONSULTOR', 'COLABORADOR']);
    }

    public function rules(): array
    {
        return [
            'proveedor_id' => ['required', 'exists:proveedores,id'],
            'forma_pago' => ['nullable', 'in:CONTADO,CREDITO'],
            'metodo_pago' => ['nullable', 'string', 'in:EFECTIVO,TRANSFERENCIA,QR,BANCO'],
            'observaciones' => ['nullable', 'string'],
            'detalles' => ['required', 'array', 'min:1'],
            'detalles.*.producto_id' => ['nullable', 'exists:productos,id', 'required_without:detalles.*.nuevo_producto_nombre'],
            'detalles.*.nuevo_producto_nombre' => ['nullable', 'string', 'max:180'],
            'detalles.*.nuevo_producto_codigo' => ['nullable', 'string', 'max:80'],
            'detalles.*.cantidad' => ['required', 'integer', 'min:1'],
            'detalles.*.costo_unitario' => ['required_without:detalles.*.costo_unitario_usd', 'nullable', 'numeric', 'min:0'],
            'detalles.*.costo_unitario_usd' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'proveedor_id.required' => 'Debe seleccionar un proveedor.',
            'detalles.required' => 'Debe agregar al menos un producto a la compra.',
            'detalles.min' => 'Debe agregar al menos un producto a la compra.',
            'detalles.*.cantidad.min' => 'La cantidad debe ser mayor que cero.',
            'detalles.*.costo_unitario.min' => 'El costo unitario en bolivianos no puede ser negativo.',
            'detalles.*.costo_unitario.required_without' => 'El costo unitario del producto es obligatorio.',
            'detalles.*.costo_unitario_usd.min' => 'El precio no puede ser negativo.',
        ];
    }
}
