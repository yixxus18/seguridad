<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="es" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Security Headers -->
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="DENY">

    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <title>{{ config('app.name', 'Secure App') }} - @yield('title')</title>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <script src="{{ mix('js/app.js') }}" defer></script>
    <!-- Optional CSP (Content Security Policy) -->
    @if(config('app.env') === 'production')
        <meta http-equiv="Content-Security-Policy" content="default-src 'self'">
    @endif

    <script>
        window.onload = function () {
            if (window.history && window.history.pushState) {
                window.history.pushState('forward', null, window.location.pathname);
                window.history.forward();

                window.onpopstate = function () {
                    window.history.pushState('forward', null, window.location.pathname);
                    window.history.forward();
                };
            }
        }

        // Deshabilitar el botón de retroceso
        window.location.hash = "no-back-button";
        window.location.hash = "Again-No-back-button";
        window.onhashchange = function () {
            window.location.hash = "no-back-button";
        }
    </script>
</head>

<body class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="text-xl font-bold text-gray-800">
                        {{ config('app.name') }}
                    </a>
                </div>

                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-3 py-2 text-gray-700 rounded-md hover:bg-gray-100">
                            Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="px-3 py-2 text-red-600 hover:text-red-800">
                                Cerrar Sesión
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="px-3 py-2 text-gray-700 hover:text-gray-900">
                            Iniciar Sesión
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Messages -->
            @if(session('status'))
                <div class="p-4 mb-4 text-green-800 bg-green-100 border border-green-200 rounded-md">
                    {{ session('status') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 mb-4 text-red-800 bg-red-100 border border-red-200 rounded-md">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Scripts -->
    @stack('scripts')
</body>

</html>
