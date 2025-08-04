<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo', 
        'numero', 
        'remitente', 
        'destinatario', 
        'asunto', 
        'descripcion', 
        'archivo_path', 
        'fecha_documento', 
        'user_id'
    ];

    // Usar casts para que fecha_documento siempre sea un objeto Carbon
    protected $casts = [
        'fecha_documento' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_usuario');
    }

    public function envios()
    {
        return $this->hasMany(DocumentoEnvio::class);
    }
}