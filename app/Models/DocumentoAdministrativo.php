<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentoAdministrativo extends Model
{
    use SoftDeletes;

    protected $table = 'documento_administrativo';
    protected $primaryKey = 'id_documento';

    protected $fillable = [
        'fk_id_estado',
        'fk_id_tipo',
        'fk_id_usuario',
        'nombre_documento',
        'descripcion',
        'ruta_archivo',
        'fecha_subida',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'fk_id_usuario', 'id_usuario');
    }

    public function estado()
    {
        return $this->belongsTo(EstadoDocumento::class, 'fk_id_estado', 'id_estado');
    }

    public function tipo()
    {
        return $this->belongsTo(TipoDocumento::class, 'fk_id_tipo', 'id_tipo');
    }

    public function tareas()
    {
        return $this->belongsToMany(Tarea::class, 'tarea_documento', 'fk_id_documento', 'fk_id_tarea');
    }
}
