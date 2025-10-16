<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-10 rounded-xl shadow-lg text-center max-w-md">
        <h1 class="text-3xl font-bold mb-6">Bem-vindo ao Sistema</h1>
        <p class="mb-6 text-gray-600">Acesse sua conta ou crie uma nova para começar.</p>

        <a href="{{ route('login') }}" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded-md inline-block">
            Login
        </a>
    </div>
</body>
</html>
