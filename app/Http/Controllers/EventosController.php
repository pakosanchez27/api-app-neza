<?php

namespace App\Http\Controllers;

use App\Models\EventoCategoriasModel;
use App\Models\EventoModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class EventosController extends Controller
{
    public function index()
    {
        EventoModel::whereDate('fecha', '<', Carbon::today())
            ->where('estatus', '!=', 2)
            ->update([
                'estatus' => 2,
            ]);

        $eventos = EventoModel::with('categoria')->orderBy('created_at', 'desc')->get();

        return view('admin.eventos.index', compact('eventos'));
    }

    public function create()
    {
        $categorias = EventoCategoriasModel::all();
        $eventoDestacadoActual = EventoModel::where('is_destacado', true)->first();

        return view('admin.eventos.create', compact('categorias', 'eventoDestacadoActual'));
    }

    public function store(Request $request)
    {
        $validatedData = $this->validateEvento($request);

        return $this->persistEvento($request, new EventoModel(), $validatedData, 'Evento creado exitosamente.');
    }

    public function edit(EventoModel $evento)
    {
        $categorias = EventoCategoriasModel::all();
        $eventoDestacadoActual = EventoModel::where('is_destacado', true)
            ->where('id', '!=', $evento->id)
            ->first();

        return view('admin.eventos.edit', compact('evento', 'categorias', 'eventoDestacadoActual'));
    }

    public function update(Request $request, EventoModel $evento)
    {
        $validatedData = $this->validateEvento($request);

        return $this->persistEvento($request, $evento, $validatedData, 'Evento actualizado exitosamente.');
    }

    public function destroy(EventoModel $evento)
    {
        if ($evento->portada) {
            $rutaPortada = public_path($evento->portada);

            if (File::exists($rutaPortada)) {
                File::delete($rutaPortada);
            }

            $directorioPortada = dirname($rutaPortada);

            if (File::isDirectory($directorioPortada) && count(File::files($directorioPortada)) === 0) {
                File::deleteDirectory($directorioPortada);
            }
        }

        $evento->delete();

        return redirect()->route('admin.eventos')->with('success', 'Evento eliminado exitosamente.');
    }

    private function validateEvento(Request $request): array
    {
        return $request->validate([
            'titulo' => 'required|string|max:255',
            'portada' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'fecha' => 'required|date|after_or_equal:today',
            'hora' => 'required',
            'calle' => 'required|string|max:255',
            'numero' => 'required|string|max:30',
            'col' => 'required|string|max:255',
            'latitud' => 'required',
            'longitud' => 'required',
            'acerca' => 'nullable|string',
            'is_destacado' => 'nullable|boolean',
            'force_change_destacado' => 'nullable|boolean',
            'estatus' => 'required|in:0,1,2',
            'id_categoria' => 'required|exists:event_categories,id',
        ]);
    }

    private function persistEvento(Request $request, EventoModel $evento, array $validatedData, string $successMessage)
    {
        $validatedData['colonia'] = $validatedData['col'];
        $validatedData['category_id'] = $validatedData['id_categoria'];
        $validatedData['user_id'] = 1;
        $validatedData['is_destacado'] = $request->boolean('is_destacado');

        $eventoDestacadoActual = EventoModel::where('is_destacado', true)
            ->where('id', '!=', $evento->id)
            ->first();

        if ($validatedData['is_destacado'] && $eventoDestacadoActual && ! $request->boolean('force_change_destacado')) {
            return back()
                ->withErrors([
                    'is_destacado' => 'Confirma que deseas reemplazar el evento destacado actual.',
                ])
                ->withInput();
        }

        if ($request->hasFile('portada')) {
            $validatedData['portada'] = $this->storePortada($request, $validatedData['titulo']);
        } elseif (! $evento->exists) {
            $validatedData['portada'] = null;
        }

        unset($validatedData['col'], $validatedData['id_categoria'], $validatedData['force_change_destacado']);

        if ($validatedData['is_destacado']) {
            EventoModel::where('is_destacado', true)
                ->where('id', '!=', $evento->id)
                ->update([
                    'is_destacado' => false,
                ]);
        }

        $evento->fill($validatedData);
        $evento->save();

        return redirect()->route('admin.eventos')->with('success', $successMessage);
    }

    private function storePortada(Request $request, string $tituloEvento): string
    {
        $eventoSlug = Str::slug($tituloEvento);
        $directorioEvento = public_path('img/eventos/' . $eventoSlug);

        if (! File::exists($directorioEvento)) {
            File::makeDirectory($directorioEvento, 0755, true);
        }

        $archivoPortada = $request->file('portada');
        $nombrePortada = 'portada-' . time() . '.' . $archivoPortada->getClientOriginalExtension();

        $archivoPortada->move($directorioEvento, $nombrePortada);

        return 'img/eventos/' . $eventoSlug . '/' . $nombrePortada;
    }
}
