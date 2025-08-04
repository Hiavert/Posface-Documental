<?php

namespace App\Http\Controllers;

use App\Models\TipoElemento;
use Illuminate\Http\Request;

class TipoElementoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'categoria' => 'required|in:documento,objeto,kit'
        ]);

        TipoElemento::create($request->all());

        return back()->with('success', 'Tipo de elemento creado correctamente');
    }
}