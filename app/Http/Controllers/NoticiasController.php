<?php

namespace App\Http\Controllers;

use App\Models\Noticia;
use App\Support\ImageManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class NoticiasController extends Controller
{
    public function index()
    {
        $noticias = Noticia::query()
            ->orderByDesc('fecha_publicacion')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.noticias.index', compact('noticias'));
    }

    public function create()
    {
        return view('admin.noticias.create');
    }

    public function store(Request $request)
    {
        $validatedData = $this->validateNoticia($request);
        $noticia = new Noticia();
        $this->persistNoticia($request, $noticia, $validatedData);

        return redirect()
            ->route('admin.noticias')
            ->with('success', 'Noticia creada correctamente.');
    }

    public function edit(Noticia $noticia)
    {
        return view('admin.noticias.edit', compact('noticia'));
    }

    public function update(Request $request, Noticia $noticia)
    {
        $validatedData = $this->validateNoticia($request);
        $this->persistNoticia($request, $noticia, $validatedData);

        return redirect()
            ->route('admin.noticias')
            ->with('success', 'Noticia actualizada correctamente.');
    }

    public function destroy(Noticia $noticia)
    {
        $directory = $this->resolveExistingDirectory($noticia);

        $this->deleteStoredFile($noticia->portada);

        foreach ($noticia->galeria ?? [] as $galleryImage) {
            $this->deleteStoredFile($galleryImage);
        }

        $noticia->delete();

        if ($directory && File::isDirectory($directory) && empty(File::files($directory))) {
            File::deleteDirectory($directory);
        }

        return redirect()
            ->route('admin.noticias')
            ->with('success', 'Noticia eliminada correctamente.');
    }

    private function validateNoticia(Request $request): array
    {
        return $request->validate([
            'portada' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'titulo' => 'required|string|max:50',
            'subtitulo' => 'nullable|string|max:255',
            'resumen' => 'nullable|string',
            'galeria' => 'nullable|array|max:10',
            'galeria.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'cta' => 'nullable|url|max:255',
            'fecha_publicacion' => 'required|date',
            'estatus' => 'required|in:0,1',
        ]);
    }

    private function persistNoticia(Request $request, Noticia $noticia, array $validatedData): void
    {
        $directorioNoticia = $this->resolveDirectory($noticia, $validatedData['titulo']);

        if ($request->hasFile('portada')) {
            $this->deleteStoredFile($noticia->portada);
            $validatedData['portada'] = $this->storeImage($request->file('portada'), $directorioNoticia, 'portada');
        } elseif (! $noticia->exists) {
            $validatedData['portada'] = null;
        } else {
            $validatedData['portada'] = $noticia->portada;
        }

        if ($request->hasFile('galeria')) {
            foreach ($noticia->galeria ?? [] as $galleryImage) {
                $this->deleteStoredFile($galleryImage);
            }

            $validatedData['galeria'] = $this->storeGallery($request, $directorioNoticia);
        } elseif ($noticia->exists) {
            $validatedData['galeria'] = $noticia->galeria ?? [];
        } else {
            $validatedData['galeria'] = [];
        }

        $noticia->fill($validatedData);
        $noticia->save();
    }

    private function resolveDirectory(Noticia $noticia, string $titulo): string
    {
        if ($noticia->exists) {
            $resolved = $this->resolveExistingDirectory($noticia);

            if ($resolved) {
                return $resolved;
            }
        }

        $slug = Str::slug($titulo) ?: 'noticia';
        $directory = public_path('img/noticias/' . $slug . '-' . time());

        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        return $directory;
    }

    private function resolveExistingDirectory(Noticia $noticia): ?string
    {
        $existingPath = $noticia->portada ?: (($noticia->galeria ?? [])[0] ?? null);

        if (! $existingPath) {
            return null;
        }

        $resolved = public_path(dirname($existingPath));

        return File::isDirectory($resolved) ? $resolved : null;
    }

    private function storeImage($file, string $directory, string $prefix): string
    {
        return ImageManager::storePublicImage($file, $directory, $prefix);
    }

    private function storeGallery(Request $request, string $directory): array
    {
        $galleryPaths = [];

        foreach ($request->file('galeria', []) as $index => $file) {
            $galleryPaths[] = $this->storeImage($file, $directory, 'galeria-' . ($index + 1));
        }

        return $galleryPaths;
    }

    private function deleteStoredFile(?string $relativePath): void
    {
        if (! $relativePath) {
            return;
        }

        $absolutePath = public_path($relativePath);

        if (File::exists($absolutePath)) {
            File::delete($absolutePath);
        }
    }
}
