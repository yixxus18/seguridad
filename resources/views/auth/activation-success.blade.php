@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center min-h-screen">
    <div class="p-8 bg-white rounded-lg shadow-md w-96">
        <h1 class="mb-6 text-2xl font-bold text-center text-gray-800">¡Cuenta Activada!</h1>

        <div class="p-4 mb-4 text-green-800 bg-green-100 border border-green-200 rounded-md">
            Tu cuenta ha sido activada exitosamente. Ya puedes iniciar sesión.
        </div>

        <div class="text-center">
            <a href="{{ route('login') }}" class="text-blue-500 hover:text-blue-700">
                Ir a Iniciar Sesión
            </a>
        </div>
    </div>

</div>
<script src="https://cdn.tailwindcss.com"></script>
@endsection
