<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\DocumentoEnvio;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DocumentoController extends Controller
{
    // Listar documentos (secretaria)
    public function index()
    {
        $documentos = Documento::where('user_id', Auth::id())->paginate(10);
        return view('documentos.gestor', compact('documentos'));
    }
    
    // Mostrar documentos recibidos
    public function recepcion()
    {
        $documentosRecibidos = DocumentoEnvio::with(['documento', 'enviadoPor'])
            ->where('user_id', auth()->id())
            ->paginate(10);

        return view('documentos.recepcion', compact('documentosRecibidos'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        return view('documentos.create');
    }

    // Guardar nuevo documento
    public function store(Request $request)
    {
        $request->validate([
            'tipo' => 'required|string',
            'numero' => 'nullable|string',
            'remitente' => 'required|string',
            'destinatario' => 'required|string',
            'asunto' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB
            'fecha_documento' => 'required|date_format:Y-m-d', // Asegurar formato
        ]);

        // Guardar archivo
        $archivoPath = $request->file('archivo')->store('documentos', 'public');

        Documento::create([
            'tipo' => $request->tipo,
            'numero' => $request->numero,
            'remitente' => $request->remitente,
            'destinatario' => $request->destinatario,
            'asunto' => $request->asunto,
            'descripcion' => $request->descripcion,
            'archivo_path' => $archivoPath,
            'fecha_documento' => $request->fecha_documento,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('documentos.gestor')->with('success', 'Documento guardado correctamente.');
    }

    // Mostrar un documento
    public function show(Documento $documento)
    {
        $usuario = Auth::user();
    
        $permitido = $documento->user_id == $usuario->id_usuario ||
                     $documento->envios()->where('user_id', $usuario->id_usuario)->exists();
        
        if (!$permitido) {
            abort(403, 'No tienes permiso para ver este documento');
        }

        $documento->load('user');
        return view('documentos.show', compact('documento'));
    }

    // Descargar el archivo adjunto y marcar como leído si es destinatario
    public function descargar(Documento $documento)
    {
        $usuario = Auth::user();
        
        // Si el usuario es un destinatario (no es el propietario)
        if ($documento->user_id !== $usuario->id_usuario) {
            // Buscar el envío correspondiente a este documento y usuario
            $envio = DocumentoEnvio::where('documento_id', $documento->id)
                        ->where('user_id', $usuario->id_usuario)
                        ->first();
            
            if ($envio && !$envio->leido) {
                $envio->update([
                    'leido' => true,
                    'fecha_leido' => now()
                ]);
            }
        }

        return Storage::disk('public')->download($documento->archivo_path);
    }

    // Mostrar formulario de edición
    public function edit(Documento $documento)
    {
        return view('documentos.edit', compact('documento'));
    }

    // Actualizar documento
    public function update(Request $request, Documento $documento)
    {
        $request->validate([
            'tipo' => 'required|string',
            'numero' => 'nullable|string',
            'remitente' => 'required|string',
            'destinatario' => 'required|string',
            'asunto' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_documento' => 'required|date_format:Y-m-d',
            'archivo' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $data = $request->only([
            'tipo', 'numero', 'remitente', 'destinatario', 
            'asunto', 'descripcion', 'fecha_documento'
        ]);

        if ($request->hasFile('archivo')) {
            // Eliminar archivo anterior
            Storage::disk('public')->delete($documento->archivo_path);
            
            // Guardar nuevo archivo
            $data['archivo_path'] = $request->file('archivo')->store('documentos', 'public');
        }

        $documento->update($data);

        return redirect()->route('documentos.gestor')->with('success', 'Documento actualizado correctamente.');
    }

    // Eliminar documento
    public function destroy(Documento $documento)
    {
        try {
            // Eliminar archivo físico
            Storage::disk('public')->delete($documento->archivo_path);
            
            // Eliminar envíos relacionados
            $documento->envios()->delete();
            
            // Eliminar documento
            $documento->delete();
            
            return redirect()->route('documentos.gestor')->with('success', 'Documento eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar documento: ' . $e->getMessage());
        }
    }
}