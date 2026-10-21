<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Info Simple POS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center bg-slate-100 px-4">
    <div class="w-full max-w-sm bg-white rounded-md shadow p-6">
        <h1 class="text-lg font-semibold mb-2">Simple POS</h1>
        <p class="text-sm text-slate-600 mb-4">Aplikasi kasir sederhana untuk UMKM.</p>
        <a href="{{ route('login') }}"
           class="block text-center bg-slate-900 text-white rounded-md py-2 text-sm">Masuk</a>
    </div>
</body>
</html>
