<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoTerna extends Model
{
    protected $table = 'documentos_terna';
    
    protected $fillable = [
        'pago_terna_id',
        'tipo',
        'ruta_archivo',
        'tipo_archivo'
    ];

    public function pagoTerna()
    {
        return $this->belongsTo(PagoTerna::class);
    }
}