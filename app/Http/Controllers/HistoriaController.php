<?php

namespace App\Http\Controllers;

use App\Models\Historia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class HistoriaController extends Controller
{
    public function index()
    {
        $historias = Historia::query()
            ->with(['galeria', 'fuentes'])
            ->orderByDesc('fecha_publicacion')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.historia.index', compact('historias'));
    }

    public function create()
    {
        return view('admin.historia.create');
    }

    public function store(Request $request)
    {
        $validatedData = $this->validateHistoria($request);

        DB::transaction(function () use ($request, $validatedData) {
            $historia = new Historia();
            $this->persistHistoria($request, $historia, $validatedData);
        });

        return redirect()
            ->route('admin.historia')
            ->with('success', 'Dato historico creado correctamente.');
    }

    public function edit(Historia $historia)
    {
        $historia->load(['galeria', 'fuentes']);

        return view('admin.historia.edit', compact('historia'));
    }

    public function update(Request $request, Historia $historia)
    {
        $validatedData = $this->validateHistoria($request, $historia);

        DB::transaction(function () use ($request, $historia, $validatedData) {
            $this->persistHistoria($request, $historia, $validatedData);
        });

        return redirect()
            ->route('admin.historia')
            ->with('success', 'Dato historico actualizado correctamente.');
    }

    public function destroy(Historia $historia)
    {
        $historia->estatus = 0;
        $historia->save();

        return redirect()
            ->route('admin.historia')
            ->with('success', 'Dato historico marcado como inactivo correctamente.');
    }

    public function activate(Historia $historia)
    {
        $historia->estatus = 1;
        $historia->save();

        return redirect()
            ->route('admin.historia')
            ->with('success', 'Dato historico activado correctamente.');
    }

    private function validateHistoria(Request $request, ?Historia $historia = null): array
    {
        $portadaRule = $historia ? 'nullable' : 'required';
        $galeriaRule = $historia ? 'nullable|array|max:10' : 'required|array|min:1|max:10';
        $galeriaItemRule = $historia ? 'nullable|image|mimes:jpeg,png,jpg,gif,webp' : 'required|image|mimes:jpeg,png,jpg,gif,webp';
        $slugRule = 'nullable|string|max:191|unique:historias,slug';

        if ($historia) {
            $slugRule .= ',' . $historia->id;
        }

        return $request->validate([
            'portada' => $portadaRule . '|image|mimes:jpeg,png,jpg,gif,webp',
            'titulo' => 'required|string|max:70',
            'slug' => $slugRule,
            'autor' => 'required|string|max:150',
            'resumen_corto' => 'required|string|max:255',
            'periodo' => 'required|string|max:255',
            'desarrollo' => 'required|string',
            'fecha_publicacion' => 'required|date',
            'estatus' => 'required|in:0,1',
            'galeria' => $galeriaRule,
            'galeria.*' => $galeriaItemRule,
            'fuentes_titulo' => 'nullable|array',
            'fuentes_titulo.*' => 'nullable|string|max:255',
            'fuentes_descripcion' => 'nullable|array',
            'fuentes_descripcion.*' => 'nullable|string',
            'fuentes_url' => 'nullable|array',
            'fuentes_url.*' => 'nullable|string|max:500',
        ]);
    }

    private function persistHistoria(Request $request, Historia $historia, array $validatedData): void
    {
        $slug = $this->generateUniqueSlug($validatedData['titulo'], $validatedData['slug'] ?? null, $historia);
        $directory = $this->resolveDirectory($historia, $slug);

        $historia->fill([
            'titulo' => $validatedData['titulo'],
            'slug' => $slug,
            'autor' => $validatedData['autor'],
            'resumen_corto' => $validatedData['resumen_corto'],
            'periodo' => $validatedData['periodo'],
            'desarrollo' => $validatedData['desarrollo'],
            'fecha_publicacion' => $validatedData['fecha_publicacion'],
            'estatus' => (int) $validatedData['estatus'],
        ]);

        if ($request->hasFile('portada')) {
            $this->deleteStoredFile($historia->portada);
            $historia->portada = $this->storeImage($request->file('portada'), $directory, 'portada');
        }

        $historia->save();

        if ($request->hasFile('galeria')) {
            $this->replaceGallery($historia, $request->file('galeria'), $directory);
        }

        $this->syncSources($historia, $request);
    }

    private function generateUniqueSlug(string $titulo, ?string $slug = null, ?Historia $historia = null): string
    {
        $baseSlug = Str::slug($slug ?: $titulo);
        $baseSlug = $baseSlug !== '' ? Str::limit($baseSlug, 180, '') : 'historia';
        $candidate = $baseSlug;
        $counter = 1;

        $query = Historia::query();

        if ($historia?->exists) {
            $query->where('id', '!=', $historia->id);
        }

        while ((clone $query)->where('slug', $candidate)->exists()) {
            $suffix = '-' . $counter;
            $candidate = Str::limit($baseSlug, 191 - strlen($suffix), '') . $suffix;
            $counter++;
        }

        return $candidate;
    }

    private function resolveDirectory(Historia $historia, string $slug): string
    {
        if ($historia->exists) {
            $existingDirectory = $this->resolveExistingDirectory($historia);

            if ($existingDirectory) {
                return $existingDirectory;
            }
        }

        $directory = public_path('img/historia/' . $slug . '-' . time());

        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        return $directory;
    }

    private function resolveExistingDirectory(Historia $historia): ?string
    {
        $historia->loadMissing('galeria');

        $existingPath = $historia->portada ?: optional($historia->galeria->first())->imagen;

        if (! $existingPath) {
            return null;
        }

        $resolved = public_path(dirname($existingPath));

        return File::isDirectory($resolved) ? $resolved : null;
    }

    private function storeImage($file, string $directory, string $prefix): string
    {
        $filename = $prefix . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($directory, $filename);

        $relativeDirectory = str_replace(public_path() . DIRECTORY_SEPARATOR, '', $directory);

        return str_replace('\\', '/', $relativeDirectory . '/' . $filename);
    }

    private function replaceGallery(Historia $historia, array $files, string $directory): void
    {
        $historia->loadMissing('galeria');

        foreach ($historia->galeria as $galleryItem) {
            $this->deleteStoredFile($galleryItem->imagen);
        }

        $historia->galeria()->delete();

        foreach ($files as $index => $file) {
            $historia->galeria()->create([
                'imagen' => $this->storeImage($file, $directory, 'galeria-' . ($index + 1)),
                'orden' => $index,
            ]);
        }
    }

    private function syncSources(Historia $historia, Request $request): void
    {
        $sourceTitles = $request->input('fuentes_titulo', []);
        $sourceDescriptions = $request->input('fuentes_descripcion', []);
        $sourceUrls = $request->input('fuentes_url', []);

        $historia->fuentes()->delete();

        foreach ($sourceTitles as $index => $titulo) {
            $descripcion = $sourceDescriptions[$index] ?? null;
            $url = $sourceUrls[$index] ?? null;

            if (! filled($titulo) && ! filled($descripcion) && ! filled($url)) {
                continue;
            }

            $historia->fuentes()->create([
                'titulo' => $titulo ?: 'Fuente sin titulo',
                'descripcion' => $descripcion,
                'url' => $url,
                'orden' => $index,
            ]);
        }
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
