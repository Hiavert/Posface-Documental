<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoDocumento extends Model
{
    protected $table = 'estado_documento';
    protected $primaryKey = 'id_estado';
    protected $fillable = ['descripcion'];
    
    public function getNombreAttribute()
    {
        return $this->descripcion;
    }
}