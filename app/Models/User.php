<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'usuario',
        'nombres',
        'apellidos',
        'email',
        'password',
        'identidad',
        'estado',
    ];

    protected $hidden = [
        'password',
    ];
    // Acuses donde el usuario es remitente
    public function acusesEnviados()
    {
        return $this->hasMany(Acuse::class, 'fk_id_usuario_remitente', 'id_usuario');
    }

    // Acuses donde el usuario es destinatario
    public function acusesRecibidos()
    {
        return $this->hasMany(Acuse::class, 'fk_id_usuario_destinatario', 'id_usuario');
    }
    // Relaciones y métodos adicionales
public function tareasAsignadas()
{
     return $this->hasMany(Tarea::class, 'fk_id_usuario_asignado', 'id_usuario');
}
public function pagosAsignados()
    {
        return $this->hasMany(PagoTerna::class, 'id_asistente', 'id_usuario');
    }
    // Notificaciones recibidas por el usuario
    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'fk_id_usuario_destinatario', 'id_usuario');
    }
    
    // Notificaciones no leídas
    public function notificacionesNoLeidas()
    {
        return $this->notificaciones()->where('estado', 'no_leida');
    }

    // Todos los acuses relacionados con el usuario (tanto enviados como recibidos)
    public function todosAcuses()
    {
        return Acuse::where('fk_id_usuario_remitente', $this->id_usuario)
            ->orWhere('fk_id_usuario_destinatario', $this->id_usuario);
    }
    
    // Elementos asociados a acuses donde el usuario está involucrado
    public function elementosAsociados()
    {
        return Elemento::whereHas('acuse', function($query) {
            $query->where('fk_id_usuario_remitente', $this->id_usuario)
                ->orWhere('fk_id_usuario_destinatario', $this->id_usuario);
        });
    }
    public function getAuthPassword()
    {
        return $this->password;
    }

    public function getAuthIdentifierName()
    {
        return $this->primaryKey;
    }

    public function getEmailForPasswordReset()
    {
        return $this->email;
    }

    /**
     * Get the email address that should be used for password reset.
     * This is needed for compatibility with password_reset_tokens table.
     */
    public function getEmailForPasswordResetAttribute()
    {
        return $this->email;
    }

    public function roles()
    {
        return $this->belongsToMany(
            \App\Models\Rol::class,        
            'usuario_roles',               
            'fk_id_usuario',               
            'fk_id_rol',                   
            'id_usuario',                 
            'id_rol'                      
        );
    }

    /**
     * Enviar la notificación personalizada de recuperación de contraseña.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }

    /**
     * Verificar si el usuario tiene permiso en un objeto
     */
    public function tienePermiso($objeto)
    {
        // Si el usuario tiene el rol SuperAdmin, siempre retorna true
        if ($this->roles->contains('nombre_rol', 'SuperAdmin')) {
            return true;
        }
        foreach ($this->roles as $rol) {
            if ($rol->tienePermiso($objeto)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verificar si el usuario tiene un permiso específico en un objeto
     */
    public function tienePermisoEspecifico($objeto, $tipo)
    {
        // Si el usuario tiene el rol SuperAdmin, siempre retorna true
        if ($this->roles->contains('nombre_rol', 'SuperAdmin')) {
            return true;
        }
        foreach ($this->roles as $rol) {
            if ($rol->tienePermisoEspecifico($objeto, $tipo)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verificar si el usuario puede ver un objeto
     */
    public function puedeVer($objeto)
    {
        return $this->tienePermisoEspecifico($objeto, 'ver');
    }

    /**
     * Verificar si el usuario puede editar un objeto
     */
    public function puedeEditar($objeto)
    {
        return $this->tienePermisoEspecifico($objeto, 'editar');
    }

    /**
     * Verificar si el usuario puede agregar en un objeto
     */
    public function puedeAgregar($objeto)
    {
        return $this->tienePermisoEspecifico($objeto, 'agregar');
    }

    /**
     * Verificar si el usuario puede eliminar en un objeto
     */
    public function puedeEliminar($objeto)
    {
        return $this->tienePermisoEspecifico($objeto, 'eliminar');
    }

    /**
     * Verificar si el usuario tiene un rol específico
     */
    public function tieneRol($rolNombre)
    {
        return $this->roles()->where('nombre_rol', $rolNombre)->exists();
    }

    /**
     * Obtener todos los permisos del usuario
     */
    public function getPermisos()
    {
        $permisos = collect();
        foreach ($this->roles as $rol) {
            $permisos = $permisos->merge($rol->getPermisos());
        }
        return $permisos->unique('id_acceso');
    }

    /**
     * Verificar si el usuario puede acceder a un objeto
     */
    public function puedeAcceder($objeto)
    {
        return $this->tienePermiso($objeto);
    }

    public function getNameAttribute()
    {
        return $this->usuario;
    }

    public function adminlte_image()
    {
        $nombre = urlencode($this->nombres . ' ' . $this->apellidos);
        return 'https://ui-avatars.com/api/?name='.$nombre.'&background=random';
    }

     public function adminlte_desc()
    {
        $rol = $this->roles()->first();
        return $rol ? $rol->nombre_rol : 'Usuario';
    }
    
    public function adminlte_profile_url()
    {
        return route('profile.show');
    }
    // Relación con los procesos donde el usuario es administrador
    
}
