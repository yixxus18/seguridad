<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Controlador para manejar la activación de cuentas de usuario
 *
 * @package App\Http\Controllers\Auth
 */
class ActivationController extends Controller
{
    /**
     * Activa una cuenta de usuario mediante enlace temporal
     *
     * @param \Illuminate\Http\Request $request
     * @param int $user_id ID del usuario a activar
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activate(Request $request, $user_id)
    {
        // Verificar firma del token temporal
        if (!$request->hasValidSignature()) {
            return redirect()->route('register')
                ->with('error', 'ACT001:Enlace de activación inválido o expirado. Por favor regístrese nuevamente.');
        }

        $user = User::find($user_id);

        if (!$user) {
            return redirect()->route('register')
                ->with('error', 'ACT002:Usuario no encontrado. Por favor regístrese nuevamente.');
        }

        // Verificar si el usuario ya está activo
        if ($user->is_active) {
            return redirect()->route('login')
                ->with('status', 'Tu cuenta ya está activa. Puedes iniciar sesión.');
        }

        // Activar cuenta de usuario
        $user->update([
            'is_active' => true,
            'activated_at' => now()
        ]);

        return redirect()->route('login')
            ->with('status', 'Tu cuenta ha sido activada exitosamente. Puedes iniciar sesión.');
    }
}
