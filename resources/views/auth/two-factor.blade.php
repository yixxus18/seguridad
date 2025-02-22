<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="DENY">
    <title>Verificación en Dos Pasos</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-r from-purple-100 to-blue-100">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="w-full max-w-md p-8 bg-white shadow-2xl rounded-xl">
            <div class="text-center">
                <svg class="w-12 h-12 mx-auto text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <h1 class="mt-6 text-3xl font-bold text-gray-900">
                    Verificación en Dos Pasos
                </h1>
                <p class="mt-2 text-sm text-gray-600">
                    Hemos enviado un código a tu correo electrónico
                </p>
            </div>

            <form class="mt-8 space-y-6" method="POST" action="{{ route('two-factor.verify') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Código de 6 dígitos
                        </label>
                        <input type="number" name="code" required autofocus
                            class="w-full px-4 py-3 text-lg text-center tracking-widest rounded-lg border border-gray-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 @error('code') border-red-500 @enderror"
                            placeholder="••••••">
                        @error('code')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full px-4 py-3 font-medium text-white transition-colors duration-300 bg-purple-600 rounded-lg hover:bg-purple-700">
                        Verificar Código
                    </button>
                </div>
            </form>

<script>
    document.getElementById('twoFactorForm').addEventListener('submit', function(e) {
        const loader = document.getElementById('loaderOverlay');
        if (loader) loader.classList.remove('hidden');
    });

    // Ocultar loader cuando hay mensajes de error/status
    document.addEventListener('DOMContentLoaded', function() {
        const loader = document.getElementById('loaderOverlay');
        if (document.querySelector('[role="alert"]') && loader) {
            loader.classList.add('hidden');
        }
    });
</script>
        </div>
    </div>
</body>

</html>
