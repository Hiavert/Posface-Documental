<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Elemento extends Model
{
    protected $table = 'elementos';
    protected $primaryKey = 'id_elemento';
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'descripcion',
        'cantidad',
        'fk_id_tipo',
        'fk_id_acuse'
    ];

    public function tipo()
    {
        return $this->belongsTo(TipoElemento::class, 'fk_id_tipo', 'id_tipo');
    }
}