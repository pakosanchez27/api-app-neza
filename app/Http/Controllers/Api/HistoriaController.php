<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Historia;
use App\Support\ImageManager;

class HistoriaController extends Controller
{
    public function index()
    {
        $historias = Historia::query()
            ->with(['galeria', 'fuentes'])
            ->where('estatus', 1)
            ->orderByDesc('fecha_publicacion')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Historia $historia) => $this->transformHistoria($historia));

        return response()->json($historias);
    }

    public function show(int $id)
    {
        $historia = Historia::query()
            ->with(['galeria', 'fuentes'])
            ->where('estatus', 1)
            ->findOrFail($id);

        return response()->json($this->transformHistoria($historia));
    }

    private function transformHistoria(Historia $historia): array
    {
        $galeria = $historia->galeria
            ->map(fn ($imagen) => [
                'id' => $imagen->id,
                'imagen' => $imagen->imagen,
                'imagen_url' => ImageManager::publicUrl($imagen->imagen),
                'orden' => $imagen->orden,
            ])
            ->values();

        $fuentes = $historia->fuentes
            ->map(fn ($fuente) => [
                'id' => $fuente->id,
                'titulo' => $fuente->titulo,
                'descripcion' => $fuente->descripcion,
                'url' => $fuente->url,
                'orden' => $fuente->orden,
            ])
            ->values();

        return [
            'id' => $historia->id,
            'titulo' => $historia->titulo,
            'slug' => $historia->slug,
            'autor' => $historia->autor,
            'resumen_corto' => $historia->resumen_corto,
            'periodo' => $historia->periodo,
            'desarrollo' => $historia->desarrollo,
            'portada' => ImageManager::preferPublicPath($historia->portada),
            'portada_url' => ImageManager::publicUrl($historia->portada),
            'fecha_publicacion' => optional($historia->fecha_publicacion)->format('Y-m-d'),
            'estatus' => (int) $historia->estatus,
            'galeria' => $galeria,
            'fuentes' => $fuentes,
            'created_at' => $historia->created_at,
            'updated_at' => $historia->updated_at,
        ];
    }
}
