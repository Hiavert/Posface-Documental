<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PagoTerna extends Model
{
    protected $table = 'pagos_terna';
    
    protected $fillable = [
        'codigo',
        'descripcion',
        'estado',
        'fecha_defensa',
        'fecha_limite',
        'fecha_envio_admin',
        'fecha_envio_asistente',
        'fecha_pago',
        'id_administrador',
        'id_asistente',
        'responsable'
    ];

    protected $casts = [
        'fecha_defensa' => 'datetime',
        'fecha_limite' => 'datetime',
        'fecha_envio_admin' => 'datetime',
        'fecha_envio_asistente' => 'datetime',
        'fecha_pago' => 'datetime',
    ];

    public function documentos(): HasMany
    {
        return $this->hasMany(DocumentoTerna::class, 'pago_terna_id');
    }

    public function administrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_administrador', 'id_usuario');
    }

    public function asistente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_asistente', 'id_usuario');
    }
}