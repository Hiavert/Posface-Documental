<?php

namespace App\Http\Controllers;

use App\Models\PagoTerna;
use App\Models\DocumentoTerna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Notifications\ProcesoCompletadoNotification;
use Illuminate\Support\Str;
class TernaAsistenteController extends Controller
{
    public function index()
    {
        $sort = request('sort', 'id');
        $direction = request('direction', 'desc');
        
        $query = PagoTerna::where('id_asistente', auth()->id())
                    ->where('estado', 'en_revision');
        
        // Filtros
        if(request('codigo')) {
            $query->where('codigo', 'like', '%'.request('codigo').'%');
        }
        
        if(request('responsable')) {
            $query->where('responsable', 'like', '%'.request('responsable').'%');
        }
        
        $procesos = $query->orderBy($sort, $direction)
                        ->paginate(10);
        
        return view('terna.asistente.index', compact('procesos'));
    }

    public function show($id)
    {
        $pagoTerna = PagoTerna::with('documentos', 'administrador')->findOrFail($id);
        return view('terna.asistente.show', compact('pagoTerna'));
    }

    public function completarProceso(Request $request, $id)
    {
        $request->validate([
            'constancia_participacion' => 'required|file|mimes:pdf|max:2048',
            'orden_pago' => 'required|file|mimes:pdf|max:2048',
            'propuesta_maestria' => 'required|file|mimes:pdf|max:2048',
        ]);

        $pagoTerna = PagoTerna::findOrFail($id);
        
        $this->subirDocumentos($pagoTerna, $request);

        $pagoTerna->update([
            'estado' => 'pendiente_pago',
            'fecha_envio_asistente' => now()
        ]);

        // Notificar al administrador
        $admin = $pagoTerna->administrador;
        if ($admin) {
            $admin->notify(new ProcesoCompletadoNotification($pagoTerna));
        }
        return redirect()->route('terna.asistente.index')->with('success', 'Proceso completado y enviado a finanzas');
    }

    private function subirDocumentos($pagoTerna, $request)
{
    $documentos = [
        ['tipo' => 'constancia_participacion', 'file' => $request->file('constancia_participacion')],
        ['tipo' => 'orden_pago', 'file' => $request->file('orden_pago')],
        ['tipo' => 'propuesta_maestria', 'file' => $request->file('propuesta_maestria')],
    ];

    foreach ($documentos as $doc) {
        if (!$doc['file']) continue; // Validar que exista archivo

        $nombreResponsable = Str::slug($pagoTerna->responsable);
        $nombreOriginal = $doc['file']->getClientOriginalName();
        // En TernaAdminController y TernaAsistenteController:
        $nombreArchivo = Str::slug(pathinfo($nombreOriginal, PATHINFO_FILENAME)) 
                 . '.' . $doc['file']->getClientOriginalExtension();
        // Guardar en disco personalizado 'documentos_terna'
        $path = $doc['file']->storeAs(
            '',             // carpeta dentro del disco (vacío porque disco apunta directo a documentos_terna)
            $nombreArchivo,
            'documentos_terna'
        );

        DocumentoTerna::create([
            'pago_terna_id' => $pagoTerna->id,
            'tipo' => $doc['tipo'],
            'ruta_archivo' => $path,
        ]);
    }
}


}