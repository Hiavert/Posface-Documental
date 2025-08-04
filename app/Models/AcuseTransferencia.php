<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcuseTransferencia extends Model
{
    protected $table = 'acuses_transferencias';
    protected $primaryKey = 'id_transferencia';
    
    protected $fillable = [
        'fk_id_acuse',
        'fk_id_usuario_origen',
        'fk_id_usuario_destino',
        'fecha_transferencia'
    ];
    
    protected $casts = [
        'fecha_transferencia' => 'datetime'
    ];

    public function origen()
    {
        return $this->belongsTo(User::class, 'fk_id_usuario_origen', 'id_usuario');
    }
    
    public function destino()
    {
        return $this->belongsTo(User::class, 'fk_id_usuario_destino', 'id_usuario');
    }
}