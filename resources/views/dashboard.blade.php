<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-96 text-center">
            <h1 class="text-2xl font-bold text-gray-800 mb-4">¡Bienvenido, {{ Auth::user()->name }}!</h1>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-red-600 hover:text-red-800 font-medium">
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </div>
    <script>
// Previene el retroceso incluso si el caché está deshabilitado
history.pushState(null, null, location.href);
window.onpopstate = function() {
    history.go(0);
};
</script>
</body>

</html>