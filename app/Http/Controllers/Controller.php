<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    /**
     * Registrar acción en la bitácora
     */
    protected function registrarBitacora($accion, $modulo, $registro_id = null, $datos_antes = null, $datos_despues = null)
    {
        $user = Auth::user();
        Bitacora::create([
            'user_id' => $user ? $user->id_usuario : null,
            'usuario_nombre' => $user ? ($user->nombres . ' ' . $user->apellidos) : null,
            'accion' => $accion,
            'modulo' => $modulo,
            'registro_id' => $registro_id,
            'datos_antes' => $datos_antes,
            'datos_despues' => $datos_despues,
            'ip' => request()->ip(),
        ]);
    }
}
