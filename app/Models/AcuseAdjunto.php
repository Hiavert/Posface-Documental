<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class AcuseAdjunto extends Model
{
    protected $table = 'acuses_adjuntos';
    protected $primaryKey = 'id_adjunto';
    
    protected $fillable = [
        'fk_id_acuse',
        'tipo',
        'nombre_archivo',
        'ruta'
    ];
    public function getUrlAttribute()
{
    return asset('storage/' . $this->ruta);
}
}