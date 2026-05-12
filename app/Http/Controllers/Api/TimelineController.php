<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TimelineModel;
use App\Support\ImageManager;

class TimelineController extends Controller
{
    public function index()
    {
        $timelines = TimelineModel::query()
            ->orderBy('orden')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (TimelineModel $timeline) => $this->transformTimeline($timeline));

        return response()->json($timelines);
    }

    public function show(int $id)
    {
        $timeline = TimelineModel::query()->findOrFail($id);

        return response()->json($this->transformTimeline($timeline));
    }

    private function transformTimeline(TimelineModel $timeline): array
    {
        return [
            'id' => $timeline->id,
            'lugar_turistico' => $timeline->lugar_turistico,
            'descripcion' => $timeline->descripcion,
            'imagen_antes' => ImageManager::preferPublicPath($timeline->imagen_antes),
            'imagen_antes_url' => ImageManager::publicUrl($timeline->imagen_antes),
            'imagen_despues' => ImageManager::preferPublicPath($timeline->imagen_despues),
            'imagen_despues_url' => ImageManager::publicUrl($timeline->imagen_despues),
            'orden' => (int) $timeline->orden,
            'estatus' => (int) $timeline->estatus,
            'created_at' => $timeline->created_at,
            'updated_at' => $timeline->updated_at,
        ];
    }
}
