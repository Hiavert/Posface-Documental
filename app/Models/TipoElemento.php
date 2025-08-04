<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoElemento extends Model
{
    protected $table = 'tipos_elemento';
    protected $primaryKey = 'id_tipo';
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'categoria'
    ];
}