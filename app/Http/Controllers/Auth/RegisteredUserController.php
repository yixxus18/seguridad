<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountActivationMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Http;

/**
 * Controlador para manejar el registro de nuevos usuarios
 */
class RegisteredUserController extends Controller
{
    /**
     * Muestra el formulario de registro
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Maneja una solicitud de registro de nuevo usuario
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/',
        'email' => 'required|string|email:rfc,dns|max:255|unique:users',
        'password' => [
            'required',
            'string',
            'confirmed',
            'min:8', // Mínimo 8 caracteres
            'regex:/[A-Z]/', // Al menos una letra mayúscula
            'regex:/[a-z]/', // Al menos una letra minúscula
            'regex:/[0-9]/', // Al menos un número
            'regex:/[@$!%*?&\-_]/', // Al menos un carácter especial
        ],
        'g-recaptcha-response' => 'required',
    ], [
        'name.regex' => 'El nombre solo puede contener letras y espacios',
        'password.min' => 'La contraseña debe tener al menos 8 caracteres',
        'password.regex' => 'La contraseña debe incluir al menos una letra mayúscula, una minúscula, un número y un carácter especial (@$!%*?&-_).',
        'g-recaptcha-response.required' => 'Por favor completa la verificación de seguridad (reCAPTCHA).',
    ]);

    // Verificar el reCAPTCHA con Google
    $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
        'secret' => config('services.recaptcha.secret_key'),
        'response' => $request->input('g-recaptcha-response'),
        'remoteip' => $request->ip(),
    ]);

    $recaptchaResponse = $response->json();

    if (!$recaptchaResponse['success']) {
        return back()->withErrors(['g-recaptcha-response' => 'Verificación de reCAPTCHA fallida.'])->withInput();
    }

    // Crear el usuario
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'is_active' => false,
    ]);

    // Generar enlace de activación
    $signedroute = URL::temporarySignedRoute(
        'activate',
        now()->addMinutes(10),
        ['user' => $user->id]
    );

    // Enviar correo de activación
    Mail::to($user->email)->send(new AccountActivationMail($signedroute));

    return back()->with('status', '¡Registro exitoso! Por favor revisa tu correo para activar tu cuenta.');
}
}
