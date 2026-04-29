<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Noticia;
use Illuminate\Support\Str;

class NoticiaController extends Controller
{
    public function index()
    {
        $noticias = Noticia::query()
            ->orderByDesc('fecha_publicacion')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Noticia $noticia) => $this->transformNoticia($noticia));

        return response()->json($noticias);
    }

    public function show(Noticia $noticia)
    {
        return response()->json($this->transformNoticia($noticia));
    }

    private function transformNoticia(Noticia $noticia): array
    {
        $galeria = collect($noticia->galeria ?? [])
            ->filter()
            ->values();

        return [
            'id' => $noticia->id,
            'titulo' => $noticia->titulo,
            'subtitulo' => $noticia->subtitulo,
            'slug' => Str::slug($noticia->titulo ?: 'noticia-' . $noticia->id),
            'resumen' => $noticia->resumen,
            'cta' => $noticia->cta,
            'portada' => $noticia->portada,
            'portada_url' => $noticia->portada ? asset($noticia->portada) : null,
            'galeria' => $galeria->all(),
            'galeria_urls' => $galeria
                ->map(fn (string $image) => asset($image))
                ->all(),
            'fecha_publicacion' => optional($noticia->fecha_publicacion)->format('Y-m-d'),
            'estatus' => (int) $noticia->estatus,
            'created_at' => $noticia->created_at,
            'updated_at' => $noticia->updated_at,
        ];
    }
}
