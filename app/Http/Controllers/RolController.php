<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Objeto;
use App\Models\Acceso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolController extends Controller
{
    /**
     * Mostrar lista de roles
     */
    public function index()
    {
        $roles = Rol::with('usuarios')->get();
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Mostrar formulario para crear rol
     */
    public function create()
    {
        return view('admin.roles.create');
    }

    /**
     * Almacenar nuevo rol
     */
    public function store(Request $request)
    {
        if (!auth()->user()->puedeAgregar('Roles')) {
            abort(403, 'No tienes permiso para agregar roles.');
        }
        $request->validate([
            'nombre_rol' => 'required|string|max:100|unique:roles,nombre_rol',
            'descripcion_rol' => 'nullable|string|max:255',
            'estado_rol' => 'required|in:0,1',
        ]);

        $rol = Rol::create($request->all());
        $this->registrarBitacora('crear', 'Rol', $rol->id_rol, null, $rol->toArray());

        return redirect()->route('roles.index')
            ->with('success', 'Rol creado correctamente.');
    }

    /**
     * Mostrar formulario para editar rol
     */
    public function edit($id)
    {
        $rol = Rol::findOrFail($id);
        return view('admin.roles.edit', compact('rol'));
    }

    /**
     * Actualizar rol
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->puedeEditar('Roles')) {
            abort(403, 'No tienes permiso para editar roles.');
        }
        $rol = Rol::findOrFail($id);
        $datos_antes = $rol->toArray();
        $request->validate([
            'nombre_rol' => 'required|string|max:100|unique:roles,nombre_rol,' . $id . ',id_rol',
            'descripcion_rol' => 'nullable|string|max:255',
            'estado_rol' => 'required|in:0,1',
        ]);

        $rol->update($request->all());
        $this->registrarBitacora('editar', 'Rol', $rol->id_rol, $datos_antes, $rol->toArray());

        return redirect()->route('roles.index')
            ->with('success', 'Rol actualizado correctamente.');
    }

    /**
     * Eliminar rol
     */
    public function destroy($id)
    {
        if (!auth()->user()->puedeEliminar('Roles')) {
            abort(403, 'No tienes permiso para eliminar roles.');
        }
        $rol = Rol::findOrFail($id);
        $datos_antes = $rol->toArray();
        // Verificar si hay usuarios asignados
        if ($rol->usuarios()->count() > 0) {
            return back()->with('error', 'No se puede eliminar el rol porque tiene usuarios asignados.');
        }
        // Eliminar accesos asociados
        $rol->accesos()->delete();
        $rol->delete();
        $this->registrarBitacora('eliminar', 'Rol', $id, $datos_antes, null);
        return redirect()->route('roles.index')
            ->with('success', 'Rol eliminado correctamente.');
    }

    /**
     * Mostrar permisos del rol
     */
    public function permisos($id)
    {
        if (!auth()->user()->puedeEditar('Roles')) {
            abort(403, 'No tienes permiso para gestionar permisos de roles.');
        }
        $rol = Rol::findOrFail($id);
        $objetos = Objeto::activos()->get();
        $accesos = $rol->accesos()->with('objeto')->get()->keyBy('fk_id_objeto');
        
        return view('admin.roles.permisos', compact('rol', 'objetos', 'accesos'));
    }

    /**
     * Actualizar permisos del rol
     */
    public function actualizarPermisos(Request $request, $id)
    {
        if (!auth()->user()->puedeEditar('Roles')) {
            abort(403, 'No tienes permiso para actualizar permisos de roles.');
        }
        $rol = Rol::findOrFail($id);
        $datos_antes = $rol->accesos()->with('objeto')->get()->toArray();
        DB::beginTransaction();
        try {
            // Eliminar permisos existentes
            $rol->accesos()->delete();
            // Crear nuevos permisos con granularidad
            if ($request->has('permisos')) {
                foreach ($request->permisos as $objetoId => $tipos) {
                    $acceso = [
                        'fk_id_rol' => $rol->id_rol,
                        'fk_id_objeto' => $objetoId,
                        'permiso' => !empty($tipos),
                        'permiso_ver' => in_array('ver', $tipos),
                        'permiso_editar' => in_array('editar', $tipos),
                        'permiso_agregar' => in_array('agregar', $tipos),
                        'permiso_eliminar' => in_array('eliminar', $tipos),
                    ];
                    Acceso::create($acceso);
                }
            }
            $datos_despues = $rol->accesos()->with('objeto')->get()->toArray();
            $this->registrarBitacora('actualizar_permisos', 'Rol', $rol->id_rol, $datos_antes, $datos_despues);
            DB::commit();
            return redirect()->route('roles.index')
                ->with('success', 'Permisos actualizados correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar permisos: ' . $e->getMessage());
        }
    }

    /**
     * Cambiar estado del rol (activar/desactivar)
     */
    public function cambiarEstado(Request $request, $id)
    {
        if (!auth()->user()->puedeEditar('Roles')) {
            abort(403, 'No tienes permiso para cambiar el estado de roles.');
        }
        $rol = Rol::findOrFail($id);
        $datos_antes = $rol->toArray();
        $request->validate([
            'estado_rol' => 'required|in:0,1',
        ]);
        $rol->update(['estado_rol' => $request->estado_rol]);
        $this->registrarBitacora('cambiar_estado', 'Rol', $rol->id_rol, $datos_antes, $rol->toArray());
        $accion = $request->estado_rol == '1' ? 'activado' : 'desactivado';
        return redirect()->route('roles.index')
            ->with('success', "Rol {$accion} correctamente.");
    }
} 