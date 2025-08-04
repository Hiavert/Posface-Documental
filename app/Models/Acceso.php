<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Acceso extends Model
{
    protected $table = 'accesos';
    protected $primaryKey = 'id_acceso';

    protected $fillable = [
        'fk_id_rol',
        'fk_id_objeto',
        'permiso',
        'permiso_ver',
        'permiso_editar',
        'permiso_agregar',
        'permiso_eliminar',
    ];

    protected $casts = [
        'permiso' => 'boolean',
        'permiso_ver' => 'boolean',
        'permiso_editar' => 'boolean',
        'permiso_agregar' => 'boolean',
        'permiso_eliminar' => 'boolean',
    ];

    /**
     * Relación con el rol
     */
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'fk_id_rol', 'id_rol');
    }

    /**
     * Relación con el objeto
     */
    public function objeto()
    {
        return $this->belongsTo(Objeto::class, 'fk_id_objeto', 'id_objeto');
    }

    /**
     * Verificar si tiene permiso general (compatibilidad)
     */
    public function tienePermiso()
    {
        return $this->permiso === true;
    }

    /**
     * Verificar si tiene permiso de ver
     */
    public function puedeVer()
    {
        return $this->permiso_ver === true;
    }

    /**
     * Verificar si tiene permiso de editar
     */
    public function puedeEditar()
    {
        return $this->permiso_editar === true;
    }

    /**
     * Verificar si tiene permiso de agregar
     */
    public function puedeAgregar()
    {
        return $this->permiso_agregar === true;
    }

    /**
     * Verificar si tiene permiso de eliminar
     */
    public function puedeEliminar()
    {
        return $this->permiso_eliminar === true;
    }

    /**
     * Verificar si tiene un permiso específico
     */
    public function tienePermisoEspecifico($tipo)
    {
        switch ($tipo) {
            case 'ver':
                return $this->puedeVer();
            case 'editar':
                return $this->puedeEditar();
            case 'agregar':
                return $this->puedeAgregar();
            case 'eliminar':
                return $this->puedeEliminar();
            default:
                return false;
        }
    }

    /**
     * Obtener el estado del permiso como array
     */
    public function getPermisosArray()
    {
        return [
            'ver' => $this->permiso_ver,
            'editar' => $this->permiso_editar,
            'agregar' => $this->permiso_agregar,
            'eliminar' => $this->permiso_eliminar,
        ];
    }
} 