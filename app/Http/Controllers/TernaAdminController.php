<?php

namespace App\Http\Controllers;

use App\Models\PagoTerna;
use App\Models\DocumentoTerna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Notifications\NuevoProcesoTernaNotification;
use App\Models\User;
use Illuminate\Support\Str;

class TernaAdminController extends Controller
{
    public function index()
    {
        $sort = request('sort', 'id');
        $direction = request('direction', 'desc');
        
        $query = PagoTerna::where('id_administrador', auth()->id());
        
        // Filtros
        if(request('codigo')) {
            $query->where('codigo', 'like', '%'.request('codigo').'%');
        }
        
        if(request('estado')) {
            $query->where('estado', request('estado'));
        }
        
        if(request('responsable')) {
            $query->where('responsable', 'like', '%'.request('responsable').'%');
        }
        
        $procesos = $query->orderBy($sort, $direction)
                        ->paginate(10);
        
        // Contadores para el resumen
        $counts = [
            'en_revision' => PagoTerna::where('id_administrador', auth()->id())
                                ->where('estado', 'en_revision')
                                ->count(),
            'pendiente_pago' => PagoTerna::where('id_administrador', auth()->id())
                                ->where('estado', 'pendiente_pago')
                                ->count(),
            'pagado' => PagoTerna::where('id_administrador', auth()->id())
                                ->where('estado', 'pagado')
                                ->count(),
            'retrasados' => PagoTerna::where('id_administrador', auth()->id())
                                ->where('estado', 'en_revision')
                                ->where('fecha_limite', '<', now())
                                ->count(),
        ];
        
        $retrasados = $counts['retrasados'];
                
        return view('terna.admin.index', compact('procesos', 'retrasados', 'counts'));
    }

    public function create()
    {
        $asistentes = User::whereHas('roles', function($query) {
            $query->where('nombre_rol', 'Asistente de Terna');
        })->get();
        
        return view('terna.admin.create', compact('asistentes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'descripcion' => 'required|string|max:255',
            'fecha_defensa' => 'required|date',
            'id_asistente' => 'required|exists:usuario,id_usuario',
            'responsable' => 'required|string|max:255',
            'fecha_limite' => 'required|date',
            'documento_fisico' => 'required|file|mimes:pdf|max:2048',
            'solvencia_cobranza' => 'required|file|mimes:pdf|max:2048',
            'acta_graduacion' => 'required|file|mimes:pdf|max:2048',
        ]);

        // Obtener último ID para código secuencial
        $ultimoId = PagoTerna::max('id') ?? 0;
        
        $pagoTerna = PagoTerna::create([
            'codigo' => 'TERNA-' . ($ultimoId + 1),
            'descripcion' => $request->descripcion,
            'fecha_defensa' => $request->fecha_defensa,
            'responsable' => $request->responsable,
            'fecha_limite' => $request->fecha_limite,
            'id_administrador' => auth()->id(),
            'id_asistente' => $request->id_asistente
        ]);

        $this->subirDocumentos($pagoTerna, $request);

        $pagoTerna->update([
            'estado' => 'en_revision',
            'fecha_envio_admin' => now()
        ]);

        // Notificar al asistente
        $asistente = User::find($request->id_asistente);
        if ($asistente) {
            $asistente->notify(new NuevoProcesoTernaNotification($pagoTerna));
        }
        return redirect()->route('terna.admin.index')->with('success', 'Proceso creado y enviado al asistente');
    }

    private function subirDocumentos($pagoTerna, $request)
{
    $documentos = [
        ['tipo' => 'documento_fisico', 'file' => $request->file('documento_fisico')],
        ['tipo' => 'solvencia_cobranza', 'file' => $request->file('solvencia_cobranza')],
        ['tipo' => 'acta_graduacion', 'file' => $request->file('acta_graduacion')],
    ];

    foreach ($documentos as $doc) {
        if (!$doc['file']) continue; // Si no hay archivo, saltear

        $nombreResponsable = Str::slug($pagoTerna->responsable);
        $nombreOriginal = $doc['file']->getClientOriginalName();
// En TernaAdminController y TernaAsistenteController:
        $nombreArchivo = Str::slug(pathinfo($nombreOriginal, PATHINFO_FILENAME)) 
                 . '.' . $doc['file']->getClientOriginalExtension();
        // Guardar en disco 'documentos_terna' (configurado en filesystems.php)
        $path = $doc['file']->storeAs(
            '',           // carpeta dentro del disco (vacío porque disco ya apunta a documentos_terna)
            $nombreArchivo,
            'documentos_terna'
        );

        DocumentoTerna::create([
            'pago_terna_id' => $pagoTerna->id,
            'tipo' => $doc['tipo'],
            'ruta_archivo' => $path,  // guardamos nombre o path relativo
        ]);
    }
}



    public function show($id)
    {
        $pagoTerna = PagoTerna::with('documentos', 'administrador', 'asistente')->findOrFail($id);
        return view('terna.admin.show', compact('pagoTerna'));
    }

    public function edit($id)
    {
        $pagoTerna = PagoTerna::findOrFail($id);
        $asistentes = User::whereHas('roles', function($query) {
            $query->where('nombre_rol', 'Asistente de Terna');
        })->get();
        
        return view('terna.admin.edit', compact('pagoTerna', 'asistentes'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'descripcion' => 'required|string|max:255',
            'fecha_defensa' => 'required|date',
            'id_asistente' => 'required|exists:usuario,id_usuario',
            'responsable' => 'required|string|max:255',
            'fecha_limite' => 'required|date',
        ]);

        $pagoTerna = PagoTerna::findOrFail($id);
        $pagoTerna->update($request->all());

        return redirect()->route('terna.admin.index')->with('success', 'Proceso actualizado correctamente');
    }

    public function destroy($id)
    {
        $pagoTerna = PagoTerna::findOrFail($id);
        
        // Eliminar documentos asociados
        foreach ($pagoTerna->documentos as $documento) {
            Storage::delete($documento->ruta_archivo);
            $documento->delete();
        }
        
        $pagoTerna->delete();
        
        return redirect()->route('terna.admin.index')->with('success', 'Proceso eliminado correctamente');
    }

    public function marcarPagado($id)
    {
        $pagoTerna = PagoTerna::findOrFail($id);
        $pagoTerna->update([
            'estado' => 'pagado',
            'fecha_pago' => now()
        ]);

        return back()->with('success', 'Pago marcado como realizado');
    }
}