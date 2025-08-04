<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\DocumentoEnvio;
use App\Models\User;
use App\Notifications\DocumentoRecibidoNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DocumentoEnvioController extends Controller
{
    // Reenviar documento a usuarios
    public function store(Request $request, Documento $documento)
    {
        $request->validate([
            'destinatarios' => 'required|array',
            'destinatarios.*' => 'exists:usuario,id_usuario',
            'mensaje' => 'nullable|string',
        ]);

        foreach ($request->destinatarios as $destinatarioId) {
            $destinatario = User::find($destinatarioId);
            
            if (!$destinatario) {
                continue; // Saltar si no se encuentra el usuario
            }

            $envio = DocumentoEnvio::create([
                'documento_id' => $documento->id,
                'user_id' => $destinatarioId,
                'mensaje' => $request->mensaje,
                'enviado_por' => Auth::id(),
            ]);

            // Notificar al destinatario
            try {
                $destinatario->notify(new DocumentoRecibidoNotification($documento, Auth::user()));
                Log::info("Notificación enviada a: {$destinatario->email}");
            } catch (\Exception $e) {
                Log::error("Error enviando notificación: " . $e->getMessage());
            }
        }

        return redirect()->route('documentos.gestor')->with('success', 'Documento reenviado correctamente.');
    }

    // Ver historial de envíos de un documento
    public function historial(Documento $documento)
    {
        $envios = $documento->envios()->with('destinatario', 'enviadoPor')->paginate(10);
        return view('documentos.historial_envios', compact('documento', 'envios'));
    }

    // Marcar un envío como leído (vía AJAX)
    public function marcarLeido(DocumentoEnvio $envio)
    {
        if (!$envio->leido) {
            $envio->update([
                'leido' => true,
                'fecha_leido' => now()
            ]);
        }
        
        return response()->json(['success' => true]);
    }
}