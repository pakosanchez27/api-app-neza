<?php

namespace App\Http\Controllers;

use App\Models\TimelineModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class TimelineModelController extends Controller
{
    public function index()
    {
        $timelines = TimelineModel::orderBy('orden', 'asc')->paginate(10);
        return view('admin.timeline.index', compact('timelines'));
    }


    public function create()
    {
        return view('admin.timeline.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'lugar_turistico' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'imagen_antes' => 'required|image|mimes:jpeg,png,jpg,gif,webp',
            'imagen_despues' => 'required|image|mimes:jpeg,png,jpg,gif,webp',
            'orden' => 'required|integer|min:0',
            'estatus' => 'required|in:0,1',
        ]);

        $timeline = new TimelineModel();
        $directorioTimeline = $this->resolveDirectory($validatedData['lugar_turistico']);

        $validatedData['imagen_antes'] = $this->storeImage(
            $request->file('imagen_antes'),
            $directorioTimeline,
            'antes'
        );

        $validatedData['imagen_despues'] = $this->storeImage(
            $request->file('imagen_despues'),
            $directorioTimeline,
            'despues'
        );

        $timeline->fill($validatedData);
        $timeline->save();

        return redirect()
            ->route('admin.timeline')
            ->with('success', 'Registro historico creado correctamente.');
    }

    public function edit(TimelineModel $timeline)
    {
        return view('admin.timeline.create', compact('timeline'));
    }

    public function update(Request $request, TimelineModel $timeline)
    {
        $validatedData = $request->validate([
            'lugar_turistico' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'imagen_antes' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'imagen_despues' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'orden' => 'required|integer|min:0',
            'estatus' => 'required|in:0,1',
        ]);

        $directorioTimeline = $this->resolveDirectory($validatedData['lugar_turistico']);

        if ($request->hasFile('imagen_antes')) {
            $validatedData['imagen_antes'] = $this->storeImage(
                $request->file('imagen_antes'),
                $directorioTimeline,
                'antes'
            );
        }

        if ($request->hasFile('imagen_despues')) {
            $validatedData['imagen_despues'] = $this->storeImage(
                $request->file('imagen_despues'),
                $directorioTimeline,
                'despues'
            );
        }

        $timeline->update($validatedData);

        return redirect()
            ->route('admin.timeline')
            ->with('success', 'Registro historico actualizado correctamente.');
    }

    public function destroy(TimelineModel $timeline)
    {
        $directory = $this->resolveExistingDirectory($timeline);

        $this->deleteStoredFile($timeline->imagen_antes);
        $this->deleteStoredFile($timeline->imagen_despues);

        $timeline->delete();

        if ($directory && File::isDirectory($directory) && empty(File::files($directory))) {
            File::deleteDirectory($directory);
        }

        return redirect()
            ->route('admin.timeline')
            ->with('success', 'Registro historico eliminado correctamente.');
    }

    private function resolveDirectory(string $lugarTuristico): string
    {
        $slug = Str::slug($lugarTuristico) ?: 'timeline';
        $directory = public_path('img/timeline/' . $slug . '-' . time());

        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        return $directory;
    }

    private function resolveExistingDirectory(TimelineModel $timeline): ?string
    {
        $existingPath = $timeline->imagen_antes ?: $timeline->imagen_despues;

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
