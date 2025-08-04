<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bitacora;

class BitacoraController extends Controller
{
    public function index(Request $request)
    {
        $query = Bitacora::query();

        // Filtros básicos
        if ($request->filled('usuario')) {
            $query->where('usuario_nombre', 'like', '%' . $request->usuario . '%');
        }
        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }
        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }
        if ($request->filled('fecha')) {
            $query->whereDate('created_at', $request->fecha);
        }

        $bitacoras = $query->orderBy('created_at', 'desc')->paginate(20);
        $modulos = Bitacora::select('modulo')->distinct()->pluck('modulo');
        $acciones = Bitacora::select('accion')->distinct()->pluck('accion');

        return view('admin.bitacora.index', compact('bitacoras', 'modulos', 'acciones'));
    }
}
