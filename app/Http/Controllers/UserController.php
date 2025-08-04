<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::paginate(10); // Cambia all() por paginate()
        
        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        if (!auth()->user()->puedeAgregar('Usuarios')) {
            abort(403, 'No tienes permisos para crear usuarios.');
        }
        $roles = Rol::all();
        return view('admin.usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->puedeAgregar('Usuarios')) {
            abort(403, 'No tienes permisos para crear usuarios.');
        }
        $request->validate([
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'email' => 'required|email|unique:usuario,email',
            'identidad' => 'required|string|max:20|unique:usuario',
            'rol' => 'required|exists:roles,id_rol',
            'estado' => 'required|in:0,1',
        ]);

        $password = Str::random(10);
        $primerNombre = explode(' ', trim($request->nombres))[0];
        $apellidos = explode(' ', trim($request->apellidos));
        $iniciales = '';
        if (count($apellidos) > 0) {
            $iniciales .= strtoupper(substr($apellidos[0], 0, 1));
        }
        if (count($apellidos) > 1) {
            $iniciales .= strtoupper(substr($apellidos[1], 0, 1));
        }
        $usuario = $primerNombre . '-' . $iniciales;

        $user = User::create([
            'usuario' => $usuario,
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'email' => $request->email,
            'password' => Hash::make($password),
            'identidad' => $request->identidad,
            'estado' => $request->estado,
        ]);

        // Asignar rol
        $user->roles()->attach($request->rol);

        // Crear token de restablecimiento
        $token = Str::random(60);
        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => bcrypt($token),
            'created_at' => now(),
        ]);

        // Enviar email de bienvenida
        Mail::to($user->email)->send(new \App\Mail\BienvenidaUsuario($user, $token));

        $this->registrarBitacora('crear', 'Usuario', $user->id_usuario, null, $user->toArray());

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente. Se ha enviado un email para establecer la contraseña.');
    }

    public function edit($id)
    {
        if (!auth()->user()->puedeEditar('Usuarios')) {
            abort(403, 'No tienes permisos para editar usuarios.');
        }
        $usuario = User::findOrFail($id);
        $roles = Rol::all();
        return view('admin.usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->puedeEditar('Usuarios')) {
            abort(403, 'No tienes permisos para editar usuarios.');
        }
        $usuario = User::findOrFail($id);
        $datos_antes = $usuario->toArray();
        $request->validate([
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'usuario' => 'required|string|max:100|unique:usuario,usuario,' . $id . ',id_usuario',
            'email' => 'required|email|unique:usuario,email,' . $id . ',id_usuario',
            'estado' => 'required|in:0,1',
            'rol' => 'required|exists:roles,id_rol',
        ]);
        $usuario->update([
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'usuario' => $request->usuario,
            'email' => $request->email,
            'estado' => $request->estado,
        ]);
        if ($request->filled('password')) {
            $usuario->update(['password' => Hash::make($request->password)]);
        }
        // Sincronizar el rol seleccionado
        if ($request->filled('rol')) {
            $usuario->roles()->sync([$request->rol]);
        }
        $this->registrarBitacora('editar', 'Usuario', $usuario->id_usuario, $datos_antes, $usuario->toArray());
        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function cambiarEstado($id)
    {
        if (!auth()->user()->puedeEliminar('Usuarios')) {
            abort(403, 'No tienes permisos para cambiar el estado de usuarios.');
        }
        $usuario = User::findOrFail($id);
        $datos_antes = $usuario->toArray();
        $usuario->estado = $usuario->estado == 1 ? 0 : 1;
        $usuario->save();
        $this->registrarBitacora('cambiar_estado', 'Usuario', $usuario->id_usuario, $datos_antes, $usuario->toArray());
        return redirect()->route('usuarios.index')->with('success', 'Estado del usuario actualizado.');
    }
    // Agrega esto en UserController.php
public function destroy($id)
{
    if (!auth()->user()->puedeEliminar('Usuarios')) {
        abort(403, 'No tienes permisos para eliminar usuarios.');
    }
    
    $usuario = User::findOrFail($id);
    $datos_antes = $usuario->toArray();
    
    // Eliminar relaciones antes de borrar el usuario
    $usuario->roles()->detach();
    
    $usuario->delete();
    
    $this->registrarBitacora('eliminar', 'Usuario', $id, $datos_antes, null);
    
    return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente.');
}
} 