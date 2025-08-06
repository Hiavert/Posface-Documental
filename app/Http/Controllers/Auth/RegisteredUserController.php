<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Mail\BienvenidaUsuario;

class RegisteredUserController extends Controller
{
    /**
     * Mostrar vista de registro.
     */
    public function create(): View
    {
        $roles = \App\Models\Rol::all();
        return view('auth.register', compact('roles'));
    }

    /**
     * Manejar el registro de un nuevo usuario.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombres'   => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'email'     => 'required|string|email|max:255|unique:usuario',
            'identidad' => 'required|string|max:20|unique:usuario',
            'rol'       => 'required|exists:roles,id_rol',
        ]);

        // Generar contraseña temporal
        $password = Str::random(10);

        // Generar nombre de usuario basado en nombre + iniciales
        $primerNombre = explode(' ', trim($request->nombres))[0];
        $apellidos = explode(' ', trim($request->apellidos));
        $iniciales = strtoupper(substr($apellidos[0] ?? '', 0, 1)) . strtoupper(substr($apellidos[1] ?? '', 0, 1));
        $usuario = $primerNombre . '-' . $iniciales;

        // Crear usuario
        $user = User::create([
            'usuario'   => $usuario,
            'nombres'   => $request->nombres,
            'apellidos' => $request->apellidos,
            'email'     => $request->email,
            'password'  => bcrypt($password),
            'identidad' => $request->identidad,
            'estado'    => 0,
        ]);

        // Asignar rol
        $user->roles()->attach($request->rol);

        // Generar token para restablecimiento (sin bcrypt)
        $token = Str::random(60);
        \DB::table('password_resets')->updateOrInsert(
            ['email' => $user->email],
            ['token' => $token, 'created_at' => now()]
        );

        // Enviar correo de bienvenida con el enlace correcto
        Mail::to($user->email)->send(new BienvenidaUsuario($user, $token));

        return redirect()->route('register')
            ->with('success', 'Usuario registrado. Se ha enviado un email para establecer la contraseña.');
    }

    /**
     * Mostrar el formulario para establecer la nueva contraseña.
     */
    public function showResetForm(Request $request, $token)
    {
        return view('auth.set_password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }
}
