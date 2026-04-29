<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EventoCategoriasModel;
use App\Models\EventoInteresadoModel;
use App\Models\EventoModel;
use Illuminate\Http\Request;

class EventoController extends Controller
{
    public function index()
    {
        $eventos = EventoModel::with('categoria')
            ->withCount('interesados')
            ->orderBy('fecha')
            ->orderBy('hora')
            ->get()
            ->map(fn(EventoModel $evento) => $this->transformEvento($evento));

        return response()->json($eventos);
    }

    public function categorias()
    {
        $categorias = EventoCategoriasModel::query()
            ->select('id', 'nombre')
            ->orderBy('nombre')
            ->get();

        return response()->json($categorias);
    }

    public function show(EventoModel $evento)
    {
        $evento->load('categoria')->loadCount('interesados');

        return response()->json($this->transformEvento($evento));
    }

    public function asistire(Request $request, EventoModel $evento)
    {
        $validatedData = $request->validate([
            'visitor_id' => 'required|string|max:100',
        ]);

        $registro = EventoInteresadoModel::firstOrCreate([
            'evento_id' => $evento->id,
            'visitor_id' => $validatedData['visitor_id'],
        ]);

        $evento->loadCount('interesados');

        return response()->json([
            'success' => true,
            'already_marked' => ! $registro->wasRecentlyCreated,
            'message' => $registro->wasRecentlyCreated
                ? 'Asistencia registrada correctamente.'
                : 'Ya habías marcado tu asistencia para este evento.',
            'interested_count' => $evento->interesados_count,
        ]);
    }

    private function transformEvento(EventoModel $evento): array
    {
        return [
            'id' => $evento->id,
            'titulo' => $evento->titulo,
            'portada' => $evento->portada,
            'portada_url' => $evento->portada ? asset($evento->portada) : null,
            'fecha' => $evento->fecha,
            'hora' => $evento->hora,
            'calle' => $evento->calle,
            'numero' => $evento->numero,
            'colonia' => $evento->colonia,
            'latitud' => $evento->latitud,
            'longitud' => $evento->longitud,
            'acerca' => $evento->acerca,
            'is_destacado' => (bool) $evento->is_destacado,
            'estatus' => (int) $evento->estatus,
            'interested_count' => (int) ($evento->interesados_count ?? $evento->interesados()->count()),
            'category_id' => $evento->category_id,
            'categoria' => $evento->categoria ? [
                'id' => $evento->categoria->id,
                'nombre' => $evento->categoria->nombre,
                'slug' => $evento->categoria->slug,
            ] : null,
            'user_id' => $evento->user_id,
            'created_at' => $evento->created_at,
            'updated_at' => $evento->updated_at,
        ];
    }
}
