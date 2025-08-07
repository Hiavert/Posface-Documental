<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcuseAdjunto extends Model
{
    use SoftDeletes;
    
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