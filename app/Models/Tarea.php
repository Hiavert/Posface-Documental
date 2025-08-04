<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tarea extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tareas';
    protected $primaryKey = 'id_tarea';

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fk_id_usuario_asignado',
        'fk_id_usuario_creador',
        'nombre',
        'descripcion',
        'estado',
        'fecha_creacion',
        'fecha_vencimiento',
        'fecha_completado',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Relación con el usuario responsable de la tarea.
     */
    public function usuarioAsignado()
    {
        return $this->belongsTo(User::class, 'fk_id_usuario_asignado', 'id_usuario');
    }

    /**
     * Relación con el usuario creador de la tarea.
     */
    public function usuarioCreador()
    {
        return $this->belongsTo(User::class, 'fk_id_usuario_creador', 'id_usuario');
    }
    // Agrega un atributo calculado para días completados
public function getDiasCompletadoAttribute()
{
    if ($this->estado === 'Completada' && $this->fecha_completado) {
        return $this->fecha_creacion->diffInDays($this->fecha_completado);
    }
    return null;
}
    /**
     * Relación con los documentos asociados a la tarea.
     */
    public function documentos()
    {
        return $this->belongsToMany(DocumentoAdministrativo::class, 'tarea_documento', 'fk_id_tarea', 'fk_id_documento');
    }
}