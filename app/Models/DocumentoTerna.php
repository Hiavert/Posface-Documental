<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentoTerna extends Model
{
    protected $table = 'documentos_terna';
    
    protected $fillable = [
        'pago_terna_id',
        'tipo',
        'ruta_archivo'
    ];

    public function pagoTerna(): BelongsTo
    {
        return $this->belongsTo(PagoTerna::class);
    }
}