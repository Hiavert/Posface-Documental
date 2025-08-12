<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IntegranteTerna extends Model
{
    protected $table = 'integrantes_terna';
    
    protected $fillable = [
        'nombre',
        'cuenta',
        'ruta_identidad'
    ];

    public function procesosComoMetodologo(): HasMany
    {
        return $this->hasMany(PagoTerna::class, 'metodologo_id');
    }

    public function procesosComoTecnico1(): HasMany
    {
        return $this->hasMany(PagoTerna::class, 'tecnico1_id');
    }

    public function procesosComoTecnico2(): HasMany
    {
        return $this->hasMany(PagoTerna::class, 'tecnico2_id');
    }
}