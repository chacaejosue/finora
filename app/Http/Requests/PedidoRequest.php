<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PedidoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && in_array($this->user()->rol, ['CONSULTOR', 'COLABORADOR']);
    }

    public function rules(): array
    {
        return [
            'cliente_id' => [
                'nullable',
                Rule::exists('usuarios', 'id')->where(fn ($query) => $query->where('rol', 'CLIENTE')),
                'required_without:nuevo_cliente_nombre',
            ],
            'nuevo_cliente_nombre' => ['nullable', 'string', 'max:100', 'required_without:cliente_id'],
            'nuevo_cliente_apellido' => ['nullable', 'string', 'max:100'],
            'nuevo_cliente_telefono' => ['nullable', 'string', 'max:30'],
            'observaciones' => ['nullable', 'string'],
            'detalles' => ['required', 'array', 'min:1'],
            'detalles.*.producto_id' => ['required', 'exists:productos,id'],
            'detalles.*.cantidad' => ['required', 'integer', 'min:1'],
            'detalles.*.precio_acordado' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_id.required_without' => 'Debe seleccionar un cliente existente o ingresar el nombre de un cliente nuevo.',
            'nuevo_cliente_nombre.required_without' => 'Debe ingresar el nombre del cliente o seleccionar uno existente.',
            'detalles.required' => 'Debe incluir al menos un producto en el pedido.',
            'detalles.min' => 'Debe incluir al menos un producto en el pedido.',
            'detalles.*.cantidad.min' => 'La cantidad debe ser mayor que cero.',
            'detalles.*.precio_acordado.min' => 'El precio acordado no puede ser negativo.',
        ];
    }
}
