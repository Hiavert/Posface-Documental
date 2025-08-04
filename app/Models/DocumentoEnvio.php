<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoEnvio extends Model
{
    use HasFactory;

    protected $fillable = [
        'documento_id', 'user_id', 'mensaje', 'leido', 'fecha_leido', 'enviado_por'
    ];

    // Convertir fechas automáticamente
    protected $dates = [
        'fecha_leido',
        'created_at',
        'updated_at'
    ];

    public function documento()
    {
        return $this->belongsTo(Documento::class);
    }

    public function destinatario()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_usuario');
    }

    public function enviadoPor()
    {
        return $this->belongsTo(User::class, 'enviado_por', 'id_usuario');
    }
}