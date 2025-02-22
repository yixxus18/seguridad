@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-lg">
        <h1 class="mb-4 text-2xl font-bold text-red-600">Error de conexión</h1>
        <p class="mb-4 text-gray-700">{{ $message }}</p>
        <p class="text-sm text-gray-600">Por favor intente nuevamente más tarde. Si el problema persiste, contacte al administrador del sistema.</p>
    </div>
</div>
<script src="https://cdn.tailwindcss.com"></script>
@endsection
