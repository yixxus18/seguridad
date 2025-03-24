<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body class="bg-gray-100">
    <div class="flex items-center justify-center min-h-screen">
        <div class="p-8 bg-white rounded-lg shadow-md w-96">
            <h1 class="mb-6 text-2xl font-bold text-center text-gray-800">Iniciar Sesión</h1>
            @if (session('status'))
                <div class="relative px-4 py-3 mb-4 text-green-700 bg-green-100 border border-green-400 rounded" role="alert">
                    {{ session('status') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="relative px-4 py-3 mb-4 text-red-700 bg-red-100 border border-red-400 rounded" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" action="{{ route('login') }}" id="authForm">
                @csrf
                <div class="mb-4">
                    <label class="block mb-2 text-sm font-bold text-gray-700">Email</label>
                    <input type="email" name="email" required
                        class="w-full px-3 py-2 border rounded-lg @error('email') border-red-500 @enderror"
                        placeholder="tu@email.com">
                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-6">
                    <label class="block mb-2 text-sm font-bold text-gray-700">Contraseña</label>
                    <input type="password" name="password" required
                        class="w-full px-3 py-2 border rounded-lg @error('password') border-red-500 @enderror"
                        placeholder="••••••••">
                    @error('password')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div class="g-recaptcha" data-sitekey="6LebH8oqAAAAAJ6xnY3CRAGsTnmUnPa_NALnQw1k"></div>
                <button type="submit"
                    class="w-full px-4 py-2 mt-4 text-white transition duration-200 bg-blue-500 rounded-lg hover:bg-blue-600">
                    Entrar
                </button>
                <div class="mt-4 text-center">
                    <p class="text-gray-600">¿No tienes una cuenta?
                        <a href="{{ route('register') }}" class="text-blue-500 hover:text-blue-700">
                            Regístrate aquí
                        </a>
                    </p>
                </div>
            </form>

        </div>
    </div>
</body>
</html>
