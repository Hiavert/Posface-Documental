<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoTesis extends Model
{
    protected $table = 'tipos_tesis';
    protected $primaryKey = 'id_tipo_tesis';
    protected $fillable = ['nombre'];
}