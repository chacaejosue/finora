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
        return $this->genero === 'FEMENINO' ? 'Bienvenida' : 'Bienvenido';
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
}