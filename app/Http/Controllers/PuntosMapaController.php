<?php

namespace App\Http\Controllers;

use App\Models\CategoriaPunto;
use App\Models\PuntoMapa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PuntosMapaController extends Controller
{
    public function index()
    {
        $puntosMapa = PuntoMapa::query()
            ->with('categoria:id,tipo')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.puntos-mapa.index', compact('puntosMapa'));
    }

    public function create()
    {
        $categorias = $this->loadCategorias();

        return view('admin.puntos-mapa.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $validatedData = $this->validatePuntoMapa($request);
        $validatedData['estatus'] = 1;

        $this->ensureCategoriaDisponible($validatedData['categoria_id']);

        if ($request->hasFile('foto_principal')) {
            $validatedData['foto_principal'] = $this->storeFotoPrincipal($request, $validatedData['nombre_punto']);
        }

        PuntoMapa::create($validatedData);

        return redirect()
            ->route('admin.puntos-mapa')
            ->with('success', 'Punto de mapa creado correctamente.');
    }

    public function edit(PuntoMapa $puntoMapa)
    {
        $categorias = $this->loadCategorias();

        return view('admin.puntos-mapa.edit', compact('puntoMapa', 'categorias'));
    }

    public function update(Request $request, PuntoMapa $puntoMapa)
    {
        $validatedData = $this->validatePuntoMapa($request);

        $this->ensureCategoriaDisponible($validatedData['categoria_id']);

        if ($request->hasFile('foto_principal')) {
            $this->deleteStoredFotoPrincipal($puntoMapa->foto_principal);
            $validatedData['foto_principal'] = $this->storeFotoPrincipal($request, $validatedData['nombre_punto']);
        } else {
            $validatedData['foto_principal'] = $puntoMapa->foto_principal;
        }

        $puntoMapa->update($validatedData);

        return redirect()
            ->route('admin.puntos-mapa')
            ->with('success', 'Punto de mapa actualizado correctamente.');
    }

    public function destroy(PuntoMapa $puntoMapa)
    {
        $this->deleteStoredFotoPrincipal($puntoMapa->foto_principal);
        $puntoMapa->delete();

        return redirect()
            ->route('admin.puntos-mapa')
            ->with('success', 'Punto de mapa eliminado correctamente.');
    }

    public function fotoPrincipal(PuntoMapa $puntoMapa)
    {
        abort_if(! $puntoMapa->foto_principal, 404);

        $absolutePath = storage_path('app/' . ltrim(str_replace('\\', '/', $puntoMapa->foto_principal), '/'));

        abort_unless(File::exists($absolutePath), 404);

        return response()->file($absolutePath);
    }

    private function loadCategorias()
    {
        return CategoriaPunto::query()
            ->where('tipo', '!=', 'Establecimiento')
            ->orderBy('tipo')
            ->get();
    }

    private function validatePuntoMapa(Request $request): array
    {
        return $request->validate([
            'nombre_punto' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'foto_principal' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'categoria_id' => 'required|exists:categorias_puntos,id',
            'calle' => 'nullable|string|max:150',
            'numero_exterior' => 'nullable|string|max:20',
            'numero_interior' => 'nullable|string|max:20',
            'cp' => 'nullable|string|max:10',
            'colonia' => 'nullable|string|max:150',
            'latitud' => 'required|numeric|between:-90,90',
            'longitud' => 'required|numeric|between:-180,180',
            'telefono' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'horarios' => 'nullable|string',
        ]);
    }

    private function ensureCategoriaDisponible(int $categoriaId): void
    {
        $categoria = CategoriaPunto::query()->findOrFail($categoriaId);

        abort_if($categoria->tipo === 'Establecimiento', 422, 'La categoria Establecimiento no esta disponible para esta vista.');
    }

    private function storeFotoPrincipal(Request $request, string $nombrePunto): string
    {
        $slug = Str::slug($nombrePunto) ?: 'punto-mapa';
        $directory = storage_path('app/punto-mapa/' . $slug);

        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $file = $request->file('foto_principal');
        $extension = strtolower($file->getClientOriginalExtension()) ?: 'bin';
        $filename = 'foto-principal-' . uniqid() . '.' . $extension;

        $file->move($directory, $filename);

        return 'punto-mapa/' . $slug . '/' . $filename;
    }

    private function deleteStoredFotoPrincipal(?string $relativePath): void
    {
        if (! $relativePath) {
            return;
        }

        $absolutePath = storage_path('app/' . ltrim(str_replace('\\', '/', $relativePath), '/'));

        if (File::exists($absolutePath)) {
            File::delete($absolutePath);
        }

        $directory = dirname($absolutePath);

        if (File::isDirectory($directory) && count(File::files($directory)) === 0) {
            File::deleteDirectory($directory);
        }
    }
}
