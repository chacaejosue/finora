<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'apellido',
        'telefono',
        'email',
        'genero',
        'direccion',
        'observaciones',
        'password',
        'rol',
        'activo',
        'email_verified_at',
        'ultimo_acceso',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'activo' => 'boolean',
            'email_verified_at' => 'datetime',
            'ultimo_acceso' => 'datetime',
        ];
    }

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class, 'cliente_id');
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class, 'cliente_id');
    }

    public function scopeClientes(Builder $query): Builder
    {
        return $query->where('rol', 'CLIENTE');
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim($this->nombre.' '.($this->apellido ?? ''));
    }

    public function getSaludoAttribute(): string
    {
        if ($this->genero === 'FEMENINO') {
            return 'Bienvenida';
        }
        if ($this->genero === 'MASCULINO') {
            return 'Bienvenido';
        }
        if (str_starts_with(mb_strtolower((string) $this->nombre), 'consultora')) {
            return 'Bienvenida';
        }
        return 'Bienvenido';
    }

    public function getRolNombreAttribute(): string
    {
        if ($this->rol === 'CONSULTOR') {
            return $this->genero === 'FEMENINO' ? 'Consultora' : 'Consultor';
        }
        if ($this->rol === 'COLABORADOR') {
            return $this->genero === 'FEMENINO' ? 'Colaboradora' : 'Colaborador';
        }
        return $this->rol;
    }

    public function getTieneAccesoAttribute(): bool
    {
        return ! empty($this->password);
    }

    /**
     * Compatibilidad con vistas que acceden a $usuario->persona->campo o $cliente->persona->campo.
     */
    public function getPersonaAttribute(): self
    {
        return $this;
    }

    /**
     * Compatibilidad con vistas que acceden a $persona->cliente.
     */
    public function getClienteAttribute(): self
    {
        return $this;
    }

    /**
     * Compatibilidad con vistas que verifican $cliente->persona->usuario para saber si tiene acceso al portal.
     */
    public function getUsuarioAttribute(): ?self
    {
        return ! empty($this->password) ? $this : null;
    }

    public function getSaldoDeudorAttribute(): float
    {
        if ($this->relationLoaded('ventas')) {
            return (float) $this->ventas
                ->where('forma_pago', 'CREDITO')
                ->whereIn('estado', ['CONFIRMADA', 'BORRADOR'])
                ->sum(fn ($v) => $v->saldo_pendiente);
        }

        return (float) $this->ventas()
            ->where('forma_pago', 'CREDITO')
            ->whereIn('estado', ['CONFIRMADA', 'BORRADOR'])
            ->with(['detalles', 'pagos'])
            ->get()
            ->sum(fn ($v) => $v->saldo_pendiente);
    }

    public function getCuotasVencidasCountAttribute(): int
    {
        return (int) Cuota::whereHas('venta', function ($q) {
            $q->where('cliente_id', $this->id)
                ->whereIn('estado', ['CONFIRMADA', 'BORRADOR']);
        })
            ->where('estado', '!=', 'PAGADA')
            ->where('fecha_vencimiento', '<', now()->toDateString())
            ->whereRaw('monto > monto_pagado')
            ->count();
    }

    public function getEstadoCrediticioAttribute(): string
    {
        if ($this->cuotas_vencidas_count > 0) {
            return 'MOROSO';
        }
        if ($this->saldo_deudor > 0.01) {
            return 'CON_DEUDA';
        }

        return 'AL_DIA';
    }
}