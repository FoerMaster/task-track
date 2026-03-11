<?php

$envPath = __DIR__.'/../.env';
if (! file_exists($envPath) && file_exists(__DIR__.'/../.env.example')) {
    copy(__DIR__.'/../.env.example', $envPath);
}

$manifestPath = __DIR__.'/../public/build/manifest.json';
if (! file_exists($manifestPath)) {
    @mkdir(dirname($manifestPath), 0777, true);
    file_put_contents(
        $manifestPath,
        json_encode([
            'resources/js/app.ts' => [
                'file' => 'assets/app.js',
                'isEntry' => true,
            ],
        ], JSON_UNESCAPED_SLASHES),
    );
}

require __DIR__.'/../vendor/autoload.php';
