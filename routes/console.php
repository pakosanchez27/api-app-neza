<?php

use App\Support\ImageManager;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('images:convert-webp', function () {
    $publicConverted = 0;
    $storageConverted = 0;

    $publicFiles = File::isDirectory(public_path('img'))
        ? File::allFiles(public_path('img'))
        : [];

    foreach ($publicFiles as $file) {
        $extension = strtolower($file->getExtension());

        if (! in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'avif'], true)) {
            continue;
        }

        $relativePath = str_replace('\\', '/', $file->getRelativePathname());
        $publicPath = 'img/' . $relativePath;

        if (ImageManager::convertExistingPublicPath($publicPath)) {
            $publicConverted++;
            $this->line("public: {$publicPath}");
        }
    }

    foreach (Storage::disk('public')->allFiles() as $path) {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if (! in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'avif'], true)) {
            continue;
        }

        if (ImageManager::convertExistingStoragePath($path)) {
            $storageConverted++;
            $this->line("storage: {$path}");
        }
    }

    $this->info("Conversion completada. Publico: {$publicConverted}. Storage: {$storageConverted}.");
})->purpose('Convierte imagenes existentes a WebP cuando aun no tienen variante .webp');
