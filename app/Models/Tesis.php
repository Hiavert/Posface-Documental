<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tesis extends Model
{
    protected $table = 'tesis';
    protected $primaryKey = 'id_tesis';
    protected $fillable = [
        'titulo', 
        'fk_id_tipo_tesis', 
        'fk_id_region', 
        'autor',
        'numero_cuenta',
        'ruta_archivo',
        'fk_id_usuario',
        'carrera',
        'tema',
        'anio',
        'fecha_defensa',
        'fecha_subida'
    ];

    public function tipo()
    {
        return $this->belongsTo(TipoTesis::class, 'fk_id_tipo_tesis', 'id_tipo_tesis');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'fk_id_region', 'id_region');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'fk_id_usuario', 'id_usuario');
    }
}