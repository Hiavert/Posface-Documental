<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table = 'notificaciones';
    protected $primaryKey = 'id_notificacion';
    public $timestamps = true;

    protected $fillable = [
        'titulo',
        'mensaje',
        'fk_id_usuario_destinatario',
        'fk_id_acuse',
        'estado',
        'fecha'
    ];

    protected $casts = [
        'fecha' => 'datetime'
    ];
    
    public function destinatario()
    {
        return $this->belongsTo(User::class, 'fk_id_usuario_destinatario', 'id_usuario');
    }
    
    public function acuse()
    {
        return $this->belongsTo(Acuse::class, 'fk_id_acuse', 'id_acuse');
    }
}