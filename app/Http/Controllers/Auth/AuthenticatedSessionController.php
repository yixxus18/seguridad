<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TwoFactorCode;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\TwoFactorCodeMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Http;
use App\Rules\TurnstileCaptcha;

/**
 * Controlador para manejar la autenticación de sesiones de usuario
 */
class AuthenticatedSessionController extends Controller
{
    /**
     * Muestra el formulario de login
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Maneja el intento de autenticación
     */
    /**
     * Maneja una solicitud de autenticación
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
{

    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'g-recaptcha-response' => 'required',
    ], [
        'g-recaptcha-response.required' => 'Por favor completa la verificación de seguridad (reCAPTCHA).',
    ]);


    $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
        'secret' => config('services.recaptcha.secret_key'),
        'response' => $request->input('g-recaptcha-response'),
        'remoteip' => $request->ip(),
    ]);

    $recaptchaResponse = $response->json();

    if (!$recaptchaResponse['success']) {
        return back()->withErrors(['g-recaptcha-response' => 'Verificación de reCAPTCHA fallida.'])->withInput();
    }

    $user = User::where('email', $credentials['email'])->first();

    if (!$user || !Hash::check($credentials['password'], $user->password)) {
        throw ValidationException::withMessages([
            'email' => 'AUTH001:Usuario y/o contraseña incorrectos',
        ]);
    }

    if (!$user->is_active) {
        throw ValidationException::withMessages([
            'email' => 'AUTH002:Tu cuenta no está activa. Por favor revisa tu correo para activarla.',
        ]);
    }

    $request->session()->put('2fa_user_id', encrypt($user->id));
    $request->session()->put('2fa_remember', $request->has('remember'));
    $request->session()->put('captcha_verified', true);

    $this->generateAndSendTwoFactorCode($user);

    return redirect()->route('two-factor.form')
        ->with('status', '¡Has iniciado sesión correctamente!')
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
}

    /**
     * Muestra el formulario de 2FA
     */
    public function showTwoFactorForm()
    {
        return view('auth.two-factor');
    }

    // Método para verificar el código 2FA
    public function verifyTwoFactor(Request $request)
    {
        $request->validate(['code' => 'required|numeric|digits:6']);

        if (!$request->session()->has('2fa_user_id')) {
            return redirect()->route('login')
                ->withErrors(['email' => 'AUTH003:La sesión ha expirado. Por favor, inicie sesión nuevamente.']);
        }

        try {
            $userId = decrypt($request->session()->get('2fa_user_id'));
            $user = User::findOrFail($userId);

            // Validar código 2FA
            $codeEntry = $user->twoFactorCodes()
                ->where('expires_at', '>', now())
                ->latest()
                ->first();

            if ($codeEntry && Hash::check($request->code, $codeEntry->code)) {
                // Limpiar código usado
                $codeEntry->delete();

                // Limpiar datos temporales de la sesión
                $remember = $request->session()->pull('2fa_remember', false);
                $request->session()->forget('2fa_user_id');

                // Iniciar sesión
                Auth::login($user, $remember);
            $request->session()->regenerate();

            return redirect()->intended(RouteServiceProvider::HOME)
                ->with('status', '¡Has iniciado sesión correctamente!');
            }

            return back()->withErrors(['code' => 'AUTH004:Código inválido o expirado']);
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->withErrors(['email' => 'AUTH005:Ha ocurrido un error. Por favor, inicie sesión nuevamente.']);
        }
    }

    private function generateAndSendTwoFactorCode($user)
    {
        // 1. Eliminar códigos previos no expirados
        $user->twoFactorCodes()
            ->where('expires_at', '>', now())
            ->delete();

        // 2. Generar código seguro de 6 dígitos
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // 3. Almacenar código hasheado con expiración
        TwoFactorCode::create([
            'user_id' => $user->getKey(),
            'code' => Hash::make($code),
            'expires_at' => now()->addMinutes(10)
        ]);

        // 4. Enviar código por email con validación
        try {
            Mail::to($user->email)->send(new TwoFactorCodeMail($code));

            // Registrar el envío en logs
            \Log::info("Código 2FA enviado a {$user->email}", [
                'user_id' => $user->id,
                'ip' => request()->ip()
            ]);
        } catch (\Exception $e) {
            \Log::error("AUTH006:Error enviando código 2FA: {$e->getMessage()}", [
                'user_id' => $user->id
            ]);

            throw new \Exception('AUTH007:Error al enviar el código de verificación');
        }
    }

    /**
     * Destruye una sesión autenticada
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        // Eliminar códigos 2FA activos del usuario
        if ($request->user() && $request->user()->twoFactorCodes()->exists()) {
            $request->user()->twoFactorCodes()->delete();
        }

        // Cerrar sesión
        Auth::guard('web')->logout();

        // Invalidar sesión
        $request->session()->invalidate();

        // Regenerar token CSRF
        $request->session()->regenerateToken();

        // Redirigir con headers de caché más estrictos
        return redirect('/login')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sun, 02 Jan 1990 00:00:00 GMT')
            ->header('Clear-Site-Data', '"cache", "cookies", "storage"');
    }
}
