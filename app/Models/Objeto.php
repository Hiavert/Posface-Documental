<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Objeto extends Model
{
    protected $table = 'objetos';
    protected $primaryKey = 'id_objeto';

    protected $fillable = [
        'nombre_objeto',
        'tipo_objeto',
        'descripcion_objeto',
        'estado_objeto',
    ];

    protected $casts = [
        'estado_objeto' => 'string',
    ];

    /**
     * Relación con los accesos (permisos)
     */
    public function accesos()
    {
        return $this->hasMany(Acceso::class, 'fk_id_objeto', 'id_objeto');
    }

    /**
     * Relación con roles a través de accesos
     */
    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'accesos', 'fk_id_objeto', 'fk_id_rol');
    }

    /**
     * Scope para objetos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('estado_objeto', '1');
    }

    /**
     * Scope por tipo de objeto
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_objeto', $tipo);
    }
} 