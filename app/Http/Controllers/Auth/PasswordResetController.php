<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * Muestra el formulario para establecer una nueva contraseña.
     */
    public function showResetForm(Request $request, $token)
    {
        $email = $request->query('email');
        // Verifica que el token y el email existan en la tabla password_resets
        $reset = DB::table('password_resets')
            ->where('email', $email)
            ->first();

        if (!$reset || !Hash::check($token, $reset->token)) {
            return redirect('/')->withErrors('El enlace de restablecimiento no es válido o ha expirado.');
        }

        return view('auth.set_password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Procesa el cambio de contraseña y activa la cuenta.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:usuario,email',
            'token' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        // Buscar el registro de password_resets
        $reset = DB::table('password_resets')
            ->where('email', $request->email)
            ->first();

        // Validar token
        if (!$reset || !Hash::check($request->token, $reset->token)) {
            return redirect('/')->withErrors('El enlace de restablecimiento no es válido o ha expirado.');
        }

        // Actualizar contraseña y activar usuario
        $user = User::where('email', $request->email)->first();
        $user->password = bcrypt($request->password);
        $user->estado = 1; // Activo
        $user->save();

        // Borrar el token de la tabla password_resets
        DB::table('password_resets')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Contraseña establecida correctamente. Ya puedes iniciar sesión.');
    }
}
