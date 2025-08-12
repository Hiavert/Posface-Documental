<?php

namespace App\Http\Controllers;

use App\Models\PagoTerna;
use App\Models\DocumentoTerna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Notifications\ProcesoCompletadoNotification;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TernaAsistenteController extends Controller
{
    public function index()
    {
        $sort = request('sort', 'id');
        $direction = request('direction', 'desc');
        
        $query = PagoTerna::where('id_asistente', auth()->id())
                    ->where('estado', 'en_revision');
        
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
        $pagoTerna = PagoTerna::with('documentos', 'administrador', 'metodologo', 'tecnico1', 'tecnico2')->findOrFail($id);
        return view('terna.asistente.show', compact('pagoTerna'));
    }

    public function completarProceso(Request $request, $id)
    {
        $request->validate([
            'constancia_participacion' => 'nullable|file|mimes:pdf|max:2048',
            'constancia_participacion_enlace' => 'nullable|url',
            'orden_pago' => 'nullable|file|mimes:pdf|max:2048',
            'orden_pago_enlace' => 'nullable|url',
            'propuesta_maestria' => 'nullable|file|mimes:pdf|max:2048',
            'propuesta_maestria_enlace' => 'nullable|url',
        ], [
            'constancia_participacion_enlace.url' => 'El enlace de constancia debe ser una URL válida',
            'orden_pago_enlace.url' => 'El enlace de orden de pago debe ser una URL válida',
            'propuesta_maestria_enlace.url' => 'El enlace de propuesta debe ser una URL válida',
        ]);

        $pagoTerna = PagoTerna::findOrFail($id);
        
        $this->subirDocumentos($pagoTerna, $request);

        $pagoTerna->update([
            'estado' => 'pendiente_pago',
            'fecha_envio_asistente' => now()
        ]);

        $admin = $pagoTerna->administrador;
        if ($admin) {
            $admin->notify(new ProcesoCompletadoNotification($pagoTerna));
        }
        
        return redirect()->route('terna.asistente.index')->with('success', 'Proceso completado y enviado a finanzas');
    }

    private function subirDocumentos($pagoTerna, $request)
    {
        $documentos = [
            [
                'tipo' => 'constancia_participacion', 
                'file' => $request->file('constancia_participacion'),
                'enlace' => $request->constancia_participacion_enlace
            ],
            [
                'tipo' => 'orden_pago', 
                'file' => $request->file('orden_pago'),
                'enlace' => $request->orden_pago_enlace
            ],
            [
                'tipo' => 'propuesta_maestria', 
                'file' => $request->file('propuesta_maestria'),
                'enlace' => $request->propuesta_maestria_enlace
            ],
        ];

        foreach ($documentos as $doc) {
            $path = null;
            $tipo_archivo = 'archivo';
            
            if ($doc['file']) {
                $nombreResponsable = Str::slug($pagoTerna->responsable);
                $nombreOriginal = $doc['file']->getClientOriginalName();
                $nombreArchivo = Str::slug(pathinfo($nombreOriginal, PATHINFO_FILENAME)) 
                         . '.' . $doc['file']->getClientOriginalExtension();
                
                $path = $doc['file']->storeAs(
                    '',
                    $nombreArchivo,
                    'documentos_terna'
                );
            } elseif ($doc['enlace']) {
                $path = $doc['enlace'];
                $tipo_archivo = 'enlace';
            } else {
                continue;
            }

            DocumentoTerna::create([
                'pago_terna_id' => $pagoTerna->id,
                'tipo' => $doc['tipo'],
                'ruta_archivo' => $path,
                'tipo_archivo' => $tipo_archivo
            ]);
        }
    }
}