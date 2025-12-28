<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale() ?: 'ar')); ?>" dir="<?php echo e(cookie('direction', 'rtl')); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <title inertia><?php echo e(config('app.name', 'LMS')); ?></title>

        <?php
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
        ?>
        <?php if(isset($faviconUrl)): ?>
            <link rel="icon" type="<?php echo e($faviconType); ?>" href="<?php echo e($faviconUrl); ?>">
            <link rel="shortcut icon" type="<?php echo e($faviconType); ?>" href="<?php echo e($faviconUrl); ?>">
        <?php endif; ?>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <?php echo app('Tighten\Ziggy\BladeRouteGenerator')->generate(); ?>
        <?php echo app('Illuminate\Foundation\Vite')(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"]); ?>
        <?php if (!isset($__inertiaSsrDispatched)) { $__inertiaSsrDispatched = true; $__inertiaSsrResponse = app(\Inertia\Ssr\Gateway::class)->dispatch($page); }  if ($__inertiaSsrResponse) { echo $__inertiaSsrResponse->head; } ?>
    </head>
    <body class="font-sans antialiased">
        <?php if (!isset($__inertiaSsrDispatched)) { $__inertiaSsrDispatched = true; $__inertiaSsrResponse = app(\Inertia\Ssr\Gateway::class)->dispatch($page); }  if ($__inertiaSsrResponse) { echo $__inertiaSsrResponse->body; } else { ?><div id="app" data-page="<?php echo e(json_encode($page)); ?>"></div><?php } ?>
    </body>
</html><?php /**PATH /var/www/html/resources/views/app.blade.php ENDPATH**/ ?>