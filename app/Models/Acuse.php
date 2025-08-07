<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Acuse extends Model
{
    protected $table = 'acuses';
    protected $primaryKey = 'id_acuse';
    public $timestamps = true;

    protected $fillable = [
        'titulo',
        'descripcion',
        'fk_id_usuario_remitente',
        'fk_id_usuario_destinatario',
        'estado',
        'fecha_envio',
        'fecha_recepcion'
    ];

    protected $casts = [
        'fecha_envio' => 'datetime',
        'fecha_recepcion' => 'datetime',
    ];

    public function remitente()
    {
        return $this->belongsTo(User::class, 'fk_id_usuario_remitente', 'id_usuario');
    }

    public function destinatario()
    {
        return $this->belongsTo(User::class, 'fk_id_usuario_destinatario', 'id_usuario');
    }

    public function elementos()
    {
        return $this->hasMany(Elemento::class, 'fk_id_acuse', 'id_acuse');
    }
    
    public function adjuntos()
    {
        return $this->hasMany(AcuseAdjunto::class, 'fk_id_acuse', 'id_acuse');
    }
    
    public function transferencias()
    {
        return $this->hasMany(AcuseTransferencia::class, 'fk_id_acuse', 'id_acuse');
    }
    
    public function rutaCompleta()
    {
        $ruta = [];
        $actual = $this;
        
        while ($actual) {
            array_unshift($ruta, $actual);
            
            // Verificar si existe una relación original
            if ($actual->original) {
                $actual = $actual->original;
            } else {
                $actual = null;
            }
        }
        
        return $ruta;
    }
}