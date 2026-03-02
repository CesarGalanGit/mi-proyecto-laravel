<!DOCTYPE html>
<html>
<head>
    <title>CRUD Laravel Perfecto</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-5 md:p-10">
    <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <div class="bg-white p-6 rounded-xl shadow-md h-fit">
            <h1 class="text-xl font-bold mb-4 text-gray-700">Nuevo Usuario</h1>
            <form action="/usuarios" method="POST" class="space-y-3">
                @csrf
                <input type="text" name="nombre" placeholder="Nombre completo" class="w-full p-2 border rounded focus:ring-2 focus:ring-blue-400 outline-none" required>
                <input type="email" name="correo" placeholder="Email" class="w-full p-2 border rounded focus:ring-2 focus:ring-blue-400 outline-none" required>
                <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 rounded hover:bg-blue-700 transition">Guardar en DB</button>
            </form>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-md">
            <h2 class="text-xl font-bold mb-4 text-gray-700">Usuarios en phpMyAdmin</h2>
            <div class="space-y-4">
                @foreach($usuarios as $user)
                <div class="p-4 border rounded-lg bg-gray-50">
                    <form action="/usuarios/{{ $user->id }}" method="POST" class="flex flex-col gap-2">
                        @csrf
                        @method('PUT')
                        <input type="text" name="nombre" value="{{ $user->name }}" class="font-semibold bg-transparent border-b focus:border-blue-500 outline-none">
                        <input type="email" name="correo" value="{{ $user->email }}" class="text-sm text-gray-600 bg-transparent border-b focus:border-blue-500 outline-none">
                        
                        <div class="flex gap-2 mt-2">
                            <button type="submit" class="text-xs bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">Actualizar</button>
                    </form>

                    <form action="/usuarios/{{ $user->id }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Borrar</button>
                    </form>
                        </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</body>
</html>