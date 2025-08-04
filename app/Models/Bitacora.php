<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    protected $table = 'bitacoras';

    protected $fillable = [
        'user_id',
        'usuario_nombre',
        'accion',
        'modulo',
        'registro_id',
        'datos_antes',
        'datos_despues',
        'ip',
    ];

    protected $casts = [
        'datos_antes' => 'array',
        'datos_despues' => 'array',
    ];
}
