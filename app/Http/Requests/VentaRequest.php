<?php

namespace App\Http\Requests;

use App\Models\Pedido;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class VentaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && in_array($this->user()->rol, ['CONSULTOR', 'COLABORADOR']);
    }

    public function rules(): array
    {
        return [
            'cliente_id' => [
                'required',
                Rule::exists('usuarios', 'id')->where(fn ($query) => $query->where('rol', 'CLIENTE')),
            ],
            'pedido_id' => ['nullable', 'exists:pedidos,id'],
            'forma_pago' => ['required', 'string', Rule::in(['CONTADO', 'CREDITO'])],
            'metodo_pago' => ['nullable', 'string', Rule::in(['EFECTIVO', 'TRANSFERENCIA', 'QR', 'TARJETA', 'OTRO']), 'required_if:forma_pago,CONTADO'],
            'numero_cuotas' => ['nullable', 'integer', 'min:1', 'max:24', 'required_if:forma_pago,CREDITO'],
            'descuento' => ['nullable', 'numeric', 'min:0'],
            'detalles' => ['required', 'array', 'min:1'],
            'detalles.*.producto_id' => [
                'required',
                'distinct',
                Rule::exists('productos', 'id')->where(fn ($query) => $query->where('activo', true)),
            ],
            'detalles.*.cantidad' => ['required', 'integer', 'min:1'],
            'detalles.*.precio_unitario' => ['required', 'numeric', 'min:0'],
        ];
    }

    /**
     * Valida que el pedido relacionado sea compatible con la venta.
     *
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->hasAny(['cliente_id', 'pedido_id', 'detalles'])) {
                return;
            }

            $pedidoId = $this->input('pedido_id');
            if (! $pedidoId) {
                return;
            }

            $pedido = Pedido::with('venta')->find($pedidoId);
            if (! $pedido) {
                return;
            }

            if ($pedido->cliente_id !== (int) $this->input('cliente_id')) {
                $validator->errors()->add('pedido_id', 'El pedido no pertenece al cliente seleccionado.');
            }

            if (in_array($pedido->estado, ['CANCELADO', 'COMPLETADO'], true)) {
                $validator->errors()->add('pedido_id', 'El pedido ya no puede convertirse en una venta.');
            }

            if ($pedido->venta) {
                $validator->errors()->add('pedido_id', 'El pedido ya tiene una venta asociada.');
            }
        }];
    }

    public function messages(): array
    {
        return [
            'cliente_id.required' => 'Debe seleccionar un cliente para la venta.',
            'forma_pago.required' => 'Debe seleccionar la forma de pago (CONTADO o CREDITO).',
            'metodo_pago.required_if' => 'Debe seleccionar el método de pago para una venta de contado.',
            'detalles.required' => 'Debe incluir al menos un producto en la venta.',
            'detalles.*.cantidad.min' => 'La cantidad vendida debe ser mayor que cero.',
            'detalles.*.precio_unitario.min' => 'El precio unitario no puede ser negativo.',
        ];
    }
}
