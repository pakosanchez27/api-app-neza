<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TimelineModel;

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
            'imagen_antes' => $timeline->imagen_antes,
            'imagen_antes_url' => $timeline->imagen_antes ? asset($timeline->imagen_antes) : null,
            'imagen_despues' => $timeline->imagen_despues,
            'imagen_despues_url' => $timeline->imagen_despues ? asset($timeline->imagen_despues) : null,
            'orden' => (int) $timeline->orden,
            'estatus' => (int) $timeline->estatus,
            'created_at' => $timeline->created_at,
            'updated_at' => $timeline->updated_at,
        ];
    }
}
