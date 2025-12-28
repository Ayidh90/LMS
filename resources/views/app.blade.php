<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale() ?: 'ar') }}" dir="{{ cookie('direction', 'rtl') }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'LMS') }}</title>

        @php
            $favicon = \App\Models\Settings::websiteFavicon();
            if ($favicon) {
                // Use getFileUrl() method to get the correct URL format (images/settings/favicon.png)
                $faviconUrl = \App\Models\Settings::getFileUrl($favicon);
                $extension = strtolower(pathinfo($favicon, PATHINFO_EXTENSION));
                $faviconType = match($extension) {
                    'ico' => 'image/x-icon',
                    'png' => 'image/png',
                    'svg' => 'image/svg+xml',
                    'jpg', 'jpeg' => 'image/jpeg',
                    'gif' => 'image/gif',
                    default => 'image/x-icon',
                };
            }
        @endphp
        @isset($faviconUrl)
            <link rel="icon" type="{{ $faviconType }}" href="{{ $faviconUrl }}">
            <link rel="shortcut icon" type="{{ $faviconType }}" href="{{ $faviconUrl }}">
        @endisset

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>