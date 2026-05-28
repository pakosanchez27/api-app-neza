<?php

namespace App\Http\Controllers;

use App\Models\CategoriaPunto;
use App\Models\EventoCategoriasModel;
use App\Models\Tipo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CatalogosController extends Controller
{
    public function tiposNegocio(): View
    {
        $tipos = Tipo::query()
            ->withCount('establecimientos')
            ->orderBy('nombre')
            ->get();

        return view('admin.catalogos.tipos-negocio', compact('tipos'));
    }

    public function createTipoNegocio(): View
    {
        return view('admin.catalogos.tipos-negocio-form', [
            'tipo' => new Tipo(),
            'isEditing' => false,
        ]);
    }

    public function storeTipoNegocio(Request $request): RedirectResponse
    {
        $validatedData = $this->validateTipoNegocio($request);

        Tipo::create($validatedData);

        return redirect()
            ->route('admin.catalogos.tipos-negocio')
            ->with('success', 'Tipo de negocio creado correctamente.');
    }

    public function editTipoNegocio(Tipo $tipo): View
    {
        return view('admin.catalogos.tipos-negocio-form', [
            'tipo' => $tipo,
            'isEditing' => true,
        ]);
    }

    public function updateTipoNegocio(Request $request, Tipo $tipo): RedirectResponse
    {
        $validatedData = $this->validateTipoNegocio($request, $tipo);

        $tipo->update($validatedData);

        return redirect()
            ->route('admin.catalogos.tipos-negocio')
            ->with('success', 'Tipo de negocio actualizado correctamente.');
    }

    public function destroyTipoNegocio(Tipo $tipo): RedirectResponse
    {
        if ($tipo->establecimientos()->exists()) {
            return redirect()
                ->route('admin.catalogos.tipos-negocio')
                ->with('error', 'No se puede eliminar el tipo porque tiene comercios relacionados.');
        }

        $tipo->delete();

        return redirect()
            ->route('admin.catalogos.tipos-negocio')
            ->with('success', 'Tipo de negocio eliminado correctamente.');
    }

    public function categoriasEventos(): View
    {
        $categorias = EventoCategoriasModel::query()
            ->withCount('eventos')
            ->orderBy('nombre')
            ->get();

        return view('admin.catalogos.categorias-eventos', compact('categorias'));
    }

    public function createCategoriaEvento(): View
    {
        return view('admin.catalogos.categorias-eventos-form', [
            'categoria' => new EventoCategoriasModel(),
            'isEditing' => false,
        ]);
    }

    public function storeCategoriaEvento(Request $request): RedirectResponse
    {
        $validatedData = $this->validateCategoriaEvento($request);
        $validatedData['slug'] = $this->uniqueCategoriaEventoSlug($validatedData['slug'] ?? null, $validatedData['nombre']);

        EventoCategoriasModel::create($validatedData);

        return redirect()
            ->route('admin.catalogos.categorias-eventos')
            ->with('success', 'Categoria de evento creada correctamente.');
    }

    public function editCategoriaEvento(EventoCategoriasModel $categoria): View
    {
        return view('admin.catalogos.categorias-eventos-form', [
            'categoria' => $categoria,
            'isEditing' => true,
        ]);
    }

    public function updateCategoriaEvento(Request $request, EventoCategoriasModel $categoria): RedirectResponse
    {
        $validatedData = $this->validateCategoriaEvento($request, $categoria);
        $validatedData['slug'] = $this->uniqueCategoriaEventoSlug(
            $validatedData['slug'] ?? null,
            $validatedData['nombre'],
            $categoria
        );

        $categoria->update($validatedData);

        return redirect()
            ->route('admin.catalogos.categorias-eventos')
            ->with('success', 'Categoria de evento actualizada correctamente.');
    }

    public function destroyCategoriaEvento(EventoCategoriasModel $categoria): RedirectResponse
    {
        if ($categoria->eventos()->exists()) {
            return redirect()
                ->route('admin.catalogos.categorias-eventos')
                ->with('error', 'No se puede eliminar la categoria porque tiene eventos relacionados.');
        }

        $categoria->delete();

        return redirect()
            ->route('admin.catalogos.categorias-eventos')
            ->with('success', 'Categoria de evento eliminada correctamente.');
    }

    public function categoriasMapa(): View
    {
        $categorias = CategoriaPunto::query()
            ->withCount('puntosMapa')
            ->orderBy('tipo')
            ->get();

        return view('admin.catalogos.categorias-mapa', compact('categorias'));
    }

    public function createCategoriaMapa(): View
    {
        return view('admin.catalogos.categorias-mapa-form', [
            'categoria' => new CategoriaPunto(),
            'isEditing' => false,
        ]);
    }

    public function storeCategoriaMapa(Request $request): RedirectResponse
    {
        $validatedData = $this->validateCategoriaMapa($request);

        CategoriaPunto::create($validatedData);

        return redirect()
            ->route('admin.catalogos.categorias-mapa')
            ->with('success', 'Categoria de mapa creada correctamente.');
    }

    public function editCategoriaMapa(CategoriaPunto $categoria): View
    {
        return view('admin.catalogos.categorias-mapa-form', [
            'categoria' => $categoria,
            'isEditing' => true,
        ]);
    }

    public function updateCategoriaMapa(Request $request, CategoriaPunto $categoria): RedirectResponse
    {
        $validatedData = $this->validateCategoriaMapa($request, $categoria);

        $categoria->update($validatedData);

        return redirect()
            ->route('admin.catalogos.categorias-mapa')
            ->with('success', 'Categoria de mapa actualizada correctamente.');
    }

    public function destroyCategoriaMapa(CategoriaPunto $categoria): RedirectResponse
    {
        if ($categoria->puntosMapa()->exists()) {
            return redirect()
                ->route('admin.catalogos.categorias-mapa')
                ->with('error', 'No se puede eliminar la categoria porque tiene puntos de mapa relacionados.');
        }

        $categoria->delete();

        return redirect()
            ->route('admin.catalogos.categorias-mapa')
            ->with('success', 'Categoria de mapa eliminada correctamente.');
    }

    private function validateTipoNegocio(Request $request, ?Tipo $tipo = null): array
    {
        return $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:50',
                Rule::unique('tipos', 'nombre')->ignore($tipo?->id_tipo, 'id_tipo'),
            ],
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique' => 'Ese tipo de negocio ya existe.',
        ]);
    }

    private function validateCategoriaEvento(Request $request, ?EventoCategoriasModel $categoria = null): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'slug' => [
                'nullable',
                'string',
                'max:120',
                Rule::unique('event_categories', 'slug')->ignore($categoria?->id),
            ],
            'descripcion' => ['nullable', 'string'],
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'slug.unique' => 'Ese slug ya esta registrado.',
        ]);
    }

    private function validateCategoriaMapa(Request $request, ?CategoriaPunto $categoria = null): array
    {
        return $request->validate([
            'tipo' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categorias_puntos', 'tipo')->ignore($categoria?->id),
            ],
        ], [
            'tipo.required' => 'El tipo es obligatorio.',
            'tipo.unique' => 'Esa categoria ya existe.',
        ]);
    }

    private function uniqueCategoriaEventoSlug(?string $slug, string $nombre, ?EventoCategoriasModel $categoria = null): string
    {
        $baseSlug = Str::slug($slug ?: $nombre) ?: 'categoria';
        $candidate = Str::limit($baseSlug, 120, '');
        $counter = 1;

        $query = EventoCategoriasModel::query();

        if ($categoria?->exists) {
            $query->whereKeyNot($categoria->getKey());
        }

        while ((clone $query)->where('slug', $candidate)->exists()) {
            $suffix = '-' . $counter;
            $candidate = Str::limit($baseSlug, 120 - strlen($suffix), '') . $suffix;
            $counter++;
        }

        return $candidate;
    }
}
