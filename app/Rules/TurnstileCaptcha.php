<?php
namespace App\Rules;

use Illuminate\Support\Facades\Http;

class TurnstileCaptcha
{
    /**
     * Determina si la regla pasa la validación.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => config('services.turnstile.secret_key'),
            'response' => $value,
            'remoteip' => request()->ip(),
        ]);

        $result = $response->json();
        return $result['success'] ?? false;
    }

    /**
     * Obtiene el mensaje de error para la regla.
     *
     * @return string
     */
    public function message()
    {
        return 'La verificación de seguridad falló. Por favor, inténtalo de nuevo.';
    }
}
