<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <meta name="theme-color" content="#0E4F49">
    <meta name="theme-color" media="(prefers-color-scheme: dark)" content="#071310">

    <script>
        (function () {
            try {
                var presets = {
                    bosque: { dark: false, color: '#0E4F49' },
                    noche: { dark: true, color: '#071310' },
                    mandioca: { dark: false, color: '#8A5A2B' },
                    terere: { dark: false, color: '#35603F' },
                    verano: { dark: false, color: '#16697F' },
                };

                var guardado = localStorage.getItem('theme');
                if (!presets[guardado]) {
                    guardado = localStorage.getItem('darkMode') === 'true' ? 'noche' : 'bosque';
                }

                var preset = presets[guardado] || presets.bosque;
                var raiz = document.documentElement;
                raiz.setAttribute('data-theme', guardado);
                if (preset.dark) {
                    raiz.classList.add('dark');
                }

                document.querySelectorAll('meta[name="theme-color"]').forEach(function (meta) {
                    meta.setAttribute('content', preset.color);
                });
            } catch (error) {
                /* sin acceso a localStorage */
            }
        })();
    </script>

    <title inertia>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <link rel="manifest" href="/manifest.json">
    <!-- Scripts -->
    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
</head>

<body class="font-sans antialiased">
    @inertia
</body>

</html>
