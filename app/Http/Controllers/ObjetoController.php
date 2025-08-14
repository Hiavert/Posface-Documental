<?php

namespace App\Http\Controllers;

use App\Models\Objeto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ObjetoController extends Controller
{
    public function index()
    {
        $objetos = Objeto::all();
        return view('objetos.index', compact('objetos'));
    }

    public function create()
    {
        return view('objetos.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre_objeto' => 'required|string|max:100|unique:objetos,nombre_objeto',
            'tipo_objeto' => 'required|in:Módulo,Función,Reporte',
            'descripcion_objeto' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Objeto::create($request->all());

        return redirect()->route('objetos.index')
            ->with('success', 'Objeto creado exitosamente.');
    }

    public function edit($id)
    {
        $objeto = Objeto::findOrFail($id);
        return view('objetos.edit', compact('objeto'));
    }

    public function update(Request $request, $id)
    {
        $objeto = Objeto::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nombre_objeto' => 'required|string|max:100|unique:objetos,nombre_objeto,' . $objeto->id_objeto . ',id_objeto',
            'tipo_objeto' => 'required|in:Módulo,Función,Reporte',
            'descripcion_objeto' => 'nullable|string|max:255',
            'estado_objeto' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $objeto->update($request->all());

        return redirect()->route('objetos.index')
            ->with('success', 'Objeto actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $objeto = Objeto::findOrFail($id);
        $objeto->delete();

        return redirect()->route('objetos.index')
            ->with('success', 'Objeto eliminado exitosamente.');
    }
}