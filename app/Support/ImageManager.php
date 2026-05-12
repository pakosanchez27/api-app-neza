<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class ImageManager
{
    public static function storePublicImage(UploadedFile $file, string $directory, string $prefix): string
    {
        File::ensureDirectoryExists($directory, 0755, true);

        $filenameWithoutExtension = $prefix . '-' . uniqid();
        $relativeDirectory = str_replace(public_path() . DIRECTORY_SEPARATOR, '', $directory);
        $relativeDirectory = str_replace('\\', '/', $relativeDirectory);
        $webpRelativePath = trim($relativeDirectory . '/' . $filenameWithoutExtension . '.webp', '/');
        $webpAbsolutePath = public_path($webpRelativePath);

        if (self::attemptWebpConversion($file, $webpAbsolutePath)) {
            return $webpRelativePath;
        }

        $extension = strtolower($file->getClientOriginalExtension()) ?: 'bin';
        $filename = $filenameWithoutExtension . '.' . $extension;
        $file->move($directory, $filename);

        return trim($relativeDirectory . '/' . $filename, '/');
    }

    public static function storePublicDiskFile(UploadedFile $file, string $directory): string
    {
        $directory = trim($directory, '/');

        if (self::isImage($file)) {
            $disk = Storage::disk('public');
            $absoluteDirectory = $disk->path($directory);

            File::ensureDirectoryExists($absoluteDirectory, 0755, true);

            $filenameWithoutExtension = pathinfo($file->hashName(), PATHINFO_FILENAME);
            $relativePath = $directory . '/' . $filenameWithoutExtension . '.webp';
            $absolutePath = $disk->path($relativePath);

            if (self::attemptWebpConversion($file, $absolutePath)) {
                return str_replace('\\', '/', $relativePath);
            }
        }

        return str_replace('\\', '/', $file->store($directory, 'public'));
    }

    public static function publicUrl(?string $path): ?string
    {
        $preferredPath = self::preferPublicPath($path);

        return $preferredPath ? self::absoluteUrl($preferredPath) : null;
    }

    public static function storageUrl(?string $path): string
    {
        if (! $path) {
            return '';
        }

        if (preg_match('/^https?:\/\//i', $path)) {
            return $path;
        }

        $storagePath = Storage::disk('public')->url(self::preferStoragePath($path));

        return self::absoluteUrl($storagePath);
    }

    public static function preferPublicPath(?string $path): ?string
    {
        if (! $path || preg_match('/^https?:\/\//i', $path)) {
            return $path;
        }

        if (strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'webp') {
            return $path;
        }

        $candidate = self::replaceExtensionWithWebp($path);

        if ($candidate && File::exists(public_path($candidate))) {
            return $candidate;
        }

        return $path;
    }

    public static function preferStoragePath(?string $path): ?string
    {
        if (! $path || preg_match('/^https?:\/\//i', $path)) {
            return $path;
        }

        if (strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'webp') {
            return $path;
        }

        $candidate = self::replaceExtensionWithWebp($path);

        if ($candidate && Storage::disk('public')->exists($candidate)) {
            return $candidate;
        }

        return $path;
    }

    public static function convertExistingPublicPath(string $path): bool
    {
        $normalizedPath = str_replace('\\', '/', ltrim($path, '/'));

        if (strtolower(pathinfo($normalizedPath, PATHINFO_EXTENSION)) === 'webp') {
            return false;
        }

        $sourcePath = public_path($normalizedPath);

        if (! File::exists($sourcePath)) {
            return false;
        }

        $destinationPath = public_path((string) self::replaceExtensionWithWebp($normalizedPath));

        if (! $destinationPath || File::exists($destinationPath)) {
            return false;
        }

        return self::attemptWebpConversionFromPath($sourcePath, $destinationPath);
    }

    public static function convertExistingStoragePath(string $path): bool
    {
        $normalizedPath = str_replace('\\', '/', ltrim($path, '/'));

        if (strtolower(pathinfo($normalizedPath, PATHINFO_EXTENSION)) === 'webp') {
            return false;
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($normalizedPath)) {
            return false;
        }

        $destinationRelativePath = self::replaceExtensionWithWebp($normalizedPath);

        if (! $destinationRelativePath || $disk->exists($destinationRelativePath)) {
            return false;
        }

        return self::attemptWebpConversionFromPath(
            $disk->path($normalizedPath),
            $disk->path($destinationRelativePath)
        );
    }

    private static function attemptWebpConversion(UploadedFile $file, string $destinationPath): bool
    {
        if (! self::isImage($file)) {
            return false;
        }

        return self::attemptWebpConversionFromPath($file->getRealPath(), $destinationPath);
    }

    private static function attemptWebpConversionFromPath(string $sourcePath, string $destinationPath): bool
    {
        if (! $sourcePath) {
            return false;
        }

        $scriptPath = base_path('scripts/convert-image-to-webp.mjs');

        if (! File::exists($scriptPath)) {
            return false;
        }

        $process = new Process([
            'node',
            $scriptPath,
            $sourcePath,
            $destinationPath,
        ], base_path());

        $process->setTimeout(30);
        $process->run();

        return $process->isSuccessful() && File::exists($destinationPath);
    }

    private static function isImage(UploadedFile $file): bool
    {
        $mimeType = $file->getMimeType() ?? '';

        return str_starts_with($mimeType, 'image/');
    }

    private static function replaceExtensionWithWebp(string $path): ?string
    {
        if (! str_contains(basename($path), '.')) {
            return null;
        }

        return preg_replace('/\.[^.]+$/', '.webp', str_replace('\\', '/', $path));
    }

    private static function absoluteUrl(string $path): string
    {
        if (preg_match('/^https?:\/\//i', $path)) {
            return $path;
        }

        $normalizedPath = '/' . ltrim(str_replace('\\', '/', $path), '/');

        if (app()->bound('request')) {
            return request()->getSchemeAndHttpHost() . $normalizedPath;
        }

        return URL::to($normalizedPath);
    }
}
