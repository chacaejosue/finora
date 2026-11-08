<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && in_array($this->user()->rol, ['CONSULTOR', 'COLABORADOR']);
    }

    public function rules(): array
    {
        $cliente = $this->route('cliente');
        $clienteId = $cliente ? $cliente->id : null;

        return [
            'nombre' => ['required', 'string', 'max:100'],
            'apellido' => ['nullable', 'string', 'max:100'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('usuarios', 'email')->ignore($clienteId),
            ],
            'direccion' => ['nullable', 'string', 'max:255'],
            'observaciones' => ['nullable', 'string'],
            'activo' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del cliente es obligatorio.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'email.unique' => 'El correo electrónico ya está registrado por otra persona.',
        ];
    }
}
