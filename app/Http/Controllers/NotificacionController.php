<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Support\Facades\Auth;

class NotificacionController extends Controller
{
    public function show($id)
    {
        $notificacion = Notificacion::findOrFail($id);

        if ($notificacion->fk_id_usuario_destinatario != Auth::user()->id_usuario) {
            abort(403);
        }

        $notificacion->estado = 'leida';
        $notificacion->save();

        return redirect()->route('acuses.show', $notificacion->fk_id_acuse);
    }
}