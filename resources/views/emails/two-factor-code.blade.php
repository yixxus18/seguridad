<!-- resources/views/emails/two-factor-code.blade.php -->
@component('mail::layout')
@slot('header')
@component('mail::header', ['url' => config('app.url')])
{{ config('app.name') }}
@endcomponent
@endslot

# ¡Hola! 👋

Aquí está tu código de verificación de dos factores:

**Código:** {{ $code }}

Este código expirará en 10 minutos.

@slot('footer')
@component('mail::footer')
© {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.
@endcomponent
@endslot
@endcomponent