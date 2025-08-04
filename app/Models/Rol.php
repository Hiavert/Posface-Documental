<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id_rol';
    public $timestamps = true; // o false si tu tabla no tiene timestamps

    protected $fillable = [
        'nombre_rol',
        'descripcion_rol',
        'estado_rol',
        // otros campos si los tienes
    ];

    protected $casts = [
        'estado_rol' => 'string',
    ];

    /**
     * Relación con usuarios
     */
    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'usuario_roles', 'fk_id_rol', 'fk_id_usuario', 'id_rol', 'id_usuario');
    }

    /**
     * Relación con accesos (permisos)
     */
    public function accesos()
    {
        return $this->hasMany(Acceso::class, 'fk_id_rol', 'id_rol');
    }

    /**
     * Relación con objetos a través de accesos
     */
    public function objetos()
    {
        return $this->belongsToMany(Objeto::class, 'accesos', 'fk_id_rol', 'fk_id_objeto');
    }

    /**
     * Verificar si el rol tiene permiso en un objeto
     */
    public function tienePermiso($objeto)
    {
        $acceso = $this->accesos()
            ->whereHas('objeto', function($query) use ($objeto) {
                $query->where('nombre_objeto', $objeto);
            })
            ->first();

        return $acceso ? $acceso->tienePermiso() : false;
    }

    /**
     * Verificar si el rol tiene un permiso específico en un objeto
     */
    public function tienePermisoEspecifico($objeto, $tipo)
    {
        $acceso = $this->accesos()
            ->whereHas('objeto', function($query) use ($objeto) {
                $query->where('nombre_objeto', $objeto);
            })
            ->first();

        return $acceso ? $acceso->tienePermisoEspecifico($tipo) : false;
    }

    /**
     * Verificar si el rol puede ver un objeto
     */
    public function puedeVer($objeto)
    {
        return $this->tienePermisoEspecifico($objeto, 'ver');
    }

    /**
     * Verificar si el rol puede editar un objeto
     */
    public function puedeEditar($objeto)
    {
        return $this->tienePermisoEspecifico($objeto, 'editar');
    }

    /**
     * Verificar si el rol puede agregar en un objeto
     */
    public function puedeAgregar($objeto)
    {
        return $this->tienePermisoEspecifico($objeto, 'agregar');
    }

    /**
     * Verificar si el rol puede eliminar en un objeto
     */
    public function puedeEliminar($objeto)
    {
        return $this->tienePermisoEspecifico($objeto, 'eliminar');
    }

    /**
     * Obtener todos los permisos del rol
     */
    public function getPermisos()
    {
        return $this->accesos()->with('objeto')->get();
    }

    /**
     * Scope para roles activos
     */
    public function scopeActivos($query)
    {
        return $query->where('estado_rol', '1');
    }
}
