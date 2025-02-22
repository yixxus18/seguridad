<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body class="bg-gray-100">
    <div class="flex items-center justify-center min-h-screen">
        <div class="p-8 bg-white rounded-lg shadow-md w-96">
            <h1 class="mb-6 text-2xl font-bold text-center text-gray-800">Registro de Usuario</h1>

            @if ($errors->any())
                <div class="relative px-4 py-3 mb-4 text-red-700 bg-red-100 border border-red-400 rounded" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('status'))
                <div class="relative px-4 py-3 mb-4 text-green-700 bg-green-100 border border-green-400 rounded" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-4">
                    <label class="block mb-2 text-sm font-bold text-gray-700">Nombre</label>
                    <input id="name" type="text"
                        class="w-full px-3 py-2 border rounded-lg @error('name') border-red-500 @enderror"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        autocomplete="name"
                        autofocus
                        pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+"
                        title="Solo letras y espacios">
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block mb-2 text-sm font-bold text-gray-700">Email</label>
                    <input id="email" type="email"
                        class="w-full px-3 py-2 border rounded-lg @error('email') border-red-500 @enderror"
                        name="email"
                        value="{{ session('status') ? '' : old('email') }}"
                        required
                        autocomplete="email">
                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block mb-2 text-sm font-bold text-gray-700">Contraseña</label>
                    <input id="password" type="password"
                        class="w-full px-3 py-2 border rounded-lg @error('password') border-red-500 @enderror"
                        name="password"
                        required
                        autocomplete="new-password"
                        title="Debe tener al menos 8 caracteres, una letra mayúscula, una minúscula, un número y un carácter especial (@$!%*?&-_).">
                    @error('password')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block mb-2 text-sm font-bold text-gray-700">Confirmar Contraseña</label>
                    <input id="password-confirm" type="password"
                        class="w-full px-3 py-2 border rounded-lg"
                        name="password_confirmation"
                        required
                        autocomplete="new-password">
                </div>
                <div class="g-recaptcha" data-sitekey="6LebH8oqAAAAAJ6xnY3CRAGsTnmUnPa_NALnQw1k"></div>
                <button type="submit"
                    class="w-full px-4 py-2 mt-4 text-white transition duration-200 bg-blue-500 rounded-lg hover:bg-blue-600">
                    Registrarse
                </button>

                <div class="mt-4 text-center">
                    <p class="text-gray-600">¿Ya tienes una cuenta?
                        <a href="{{ route('login') }}" class="text-blue-500 hover:text-blue-700">
                            Inicia Sesión aquí
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
