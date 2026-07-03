<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>GERDCare</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-teal-50 text-slate-900 antialiased">

    <div class="flex min-h-screen items-center justify-center px-4">

        <div class="w-full max-w-md">
            {{ $slot }}
        </div>

    </div>

</body>

</html>