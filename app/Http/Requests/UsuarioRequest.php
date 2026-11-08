<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->rol === 'CONSULTOR';
    }

    public function rules(): array
    {
        $usuario = $this->route('usuario');
        $usuarioId = $usuario ? $usuario->id : null;

        $isCreate = $this->isMethod('post');

        return [
            'nombre' => ['required', 'string', 'max:100'],
            'apellido' => ['nullable', 'string', 'max:100'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('usuarios', 'email')->ignore($usuarioId),
            ],
            'direccion' => ['nullable', 'string', 'max:255'],
            'genero' => ['nullable', 'string', Rule::in(['MASCULINO', 'FEMENINO', 'OTRO'])],
            'rol' => ['required', 'string', Rule::in(['CONSULTOR', 'COLABORADOR', 'CLIENTE'])],
            'password' => [$isCreate ? 'required' : 'nullable', 'string', 'min:6'],
            'activo' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del usuario es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio para crear el usuario.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'email.unique' => 'El correo electrónico ya está registrado por otra persona.',
            'rol.required' => 'Debe seleccionar un rol para el usuario.',
            'rol.in' => 'El rol seleccionado no es válido.',
            'password.required' => 'La contraseña es obligatoria para nuevos usuarios.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ];
    }
}
