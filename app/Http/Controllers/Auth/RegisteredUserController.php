<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\BienvenidaUsuario;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $roles = \App\Models\Rol::all();
        return view('auth.register', compact('roles'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'email' => 'required|string|email|max:255|unique:usuario',
            'identidad' => 'required|string|max:20|unique:usuario',
            'rol' => 'required|exists:roles,id_rol',
        ]);

        $password = Str::random(10);

        // Obtener primer nombre
        $primerNombre = explode(' ', trim($request->nombres))[0];

        // Obtener apellidos y sus iniciales
        $apellidos = explode(' ', trim($request->apellidos));
        $iniciales = '';
        if (count($apellidos) > 0) {
            $iniciales .= strtoupper(substr($apellidos[0], 0, 1));
        }
        if (count($apellidos) > 1) {
            $iniciales .= strtoupper(substr($apellidos[1], 0, 1));
        }

        // Generar el usuario
        $usuario = $primerNombre . '-' . $iniciales;

        $user = User::create([
            'usuario' => $usuario,
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'email' => $request->email,
            'password' => bcrypt($password), // <-- CORREGIDO
            'identidad' => $request->identidad,
            'estado' => 0, 
        ]);

        // Asignar rol
        $user->roles()->attach($request->rol);

        // Crear token de restablecimiento
        $token = Str::random(60);
        \DB::table('password_resets')->insert([
            'email' => $user->email, 
            'token' => bcrypt($token),
            'created_at' => now(),
        ]);

        // Enviar email
        Mail::to($user->email)->send(new BienvenidaUsuario($user, $token));

        return redirect()->route('register')->with('success', 'Usuario registrado. Se ha enviado un email para establecer la contraseña.');
    }

    public function showResetForm(Request $request, $token)
    {
        $email = $request->query('email');
        return view('auth.set_password', [
            'token' => $token,
            'email' => $email,
        ]);
    }
}
