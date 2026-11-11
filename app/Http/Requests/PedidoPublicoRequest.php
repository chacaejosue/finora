<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PedidoPublicoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100', 'regex:/^[\p{L}\s\'’-]+$/u'],
            'apellido' => ['nullable', 'string', 'max:100', 'regex:/^[\p{L}\s\'’-]+$/u'],
            'telefono' => ['required', 'string', 'max:30', 'regex:/^[0-9+()\s-]+$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
            'preferencia_pago' => ['required', Rule::in(['CONTADO', 'CREDITO', 'POR_CONFIRMAR'])],
            'carrito' => ['required', 'array', 'min:1', 'max:50'],
            'carrito.*.producto_id' => ['required', 'integer', 'exists:productos,id'],
            'carrito.*.cantidad' => ['required', 'integer', 'min:1', 'max:99'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre',
            'apellido' => 'apellido',
            'telefono' => 'teléfono',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.regex' => 'El nombre solo puede contener letras, espacios, apóstrofes y guiones.',
            'apellido.regex' => 'El apellido solo puede contener letras, espacios, apóstrofes y guiones.',
            'telefono.regex' => 'El teléfono contiene caracteres no válidos.',
        ];
    }
}
