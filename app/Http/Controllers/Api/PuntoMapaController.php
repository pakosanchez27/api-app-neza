<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Establecimiento;
use App\Models\PuntoMapa;
use App\Support\ImageManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PuntoMapaController extends Controller
{
    public function index(): JsonResponse
    {
        $puntosMapa = PuntoMapa::query()
            ->with('categoria:id,tipo')
            ->where('estatus', 1)
            ->orderBy('categoria_id')
            ->orderBy('nombre_punto')
            ->get()
            ->map(fn (PuntoMapa $puntoMapa): array => $this->transformMapPoint($puntoMapa));

        $establecimientos = Establecimiento::query()
            ->with([
                'tipo:id_tipo,nombre',
                'contacto:id_contacto,telefono,correo,id_establecimiento',
                'domicilio:id_domicilio,calle,colonia,num_int,num_ext,cp,latitud,longitud,id_establecimiento',
                'horarios:id_horario,id_establecimiento,dia_semana,hora_apertura,hora_cierra,cerrado',
            ])
            ->where('estatus', true)
            ->where('is_visible', true)
            ->whereHas('domicilio', function ($query) {
                $query->whereNotNull('latitud')
                    ->whereNotNull('longitud');
            })
            ->orderBy('nombre_est')
            ->get()
            ->map(fn (Establecimiento $establecimiento): array => $this->transformEstablishment($establecimiento));

        $mapItems = $puntosMapa
            ->concat($establecimientos)
            ->sortBy([
                ['category', 'asc'],
                ['name', 'asc'],
            ])
            ->values();

        return response()->json($mapItems);
    }

    public function photo(PuntoMapa $puntoMapa): BinaryFileResponse
    {
        abort_if(! $puntoMapa->foto_principal, 404);

        $absolutePath = storage_path('app/' . ltrim(str_replace('\\', '/', $puntoMapa->foto_principal), '/'));

        abort_unless(File::exists($absolutePath), 404);

        return response()->file($absolutePath);
    }

    private function transformMapPoint(PuntoMapa $puntoMapa): array
    {
        $addressParts = array_filter([
            $puntoMapa->calle,
            $puntoMapa->numero_exterior,
            $puntoMapa->numero_interior ? 'Int. ' . $puntoMapa->numero_interior : null,
            $puntoMapa->colonia,
            $puntoMapa->cp,
        ], fn ($value) => filled($value));

        return [
            'id' => 'punto-' . $puntoMapa->id,
            'source' => 'punto_mapa',
            'source_id' => $puntoMapa->id,
            'name' => $puntoMapa->nombre_punto,
            'category' => $puntoMapa->categoria?->tipo ?? 'Sin categoria',
            'description' => $puntoMapa->descripcion,
            'image' => $puntoMapa->foto_principal
                ? route('api.puntos-mapa.foto', $puntoMapa)
                : null,
            'address' => implode(', ', $addressParts),
            'phone' => $puntoMapa->telefono,
            'email' => $puntoMapa->email,
            'hours' => $puntoMapa->horarios,
            'position' => [
                (float) $puntoMapa->latitud,
                (float) $puntoMapa->longitud,
            ],
            'latitud' => (float) $puntoMapa->latitud,
            'longitud' => (float) $puntoMapa->longitud,
            'calle' => $puntoMapa->calle,
            'numero_exterior' => $puntoMapa->numero_exterior,
            'numero_interior' => $puntoMapa->numero_interior,
            'cp' => $puntoMapa->cp,
            'colonia' => $puntoMapa->colonia,
        ];
    }

    private function transformEstablishment(Establecimiento $establecimiento): array
    {
        $domicilio = $establecimiento->domicilio;
        $contacto = $establecimiento->contacto;
        $latitud = (float) $domicilio->latitud;
        $longitud = (float) $domicilio->longitud;
        $addressParts = array_filter([
            $domicilio->calle,
            $domicilio->num_ext,
            $domicilio->num_int ? 'Int. ' . $domicilio->num_int : null,
            $domicilio->colonia,
            $domicilio->cp,
        ], fn ($value) => filled($value));

        return [
            'id' => 'establecimiento-' . $establecimiento->id_establecimiento,
            'source' => 'establecimiento',
            'source_id' => $establecimiento->id_establecimiento,
            'name' => $establecimiento->nombre_est,
            'category' => 'Establecimiento',
            'subcategory' => $establecimiento->tipo?->nombre,
            'description' => $establecimiento->descripcion,
            'image' => $establecimiento->logo
                ? ImageManager::storageUrl($establecimiento->logo)
                : null,
            'address' => implode(', ', $addressParts),
            'phone' => $contacto?->telefono,
            'email' => $contacto?->correo,
            'hours' => $this->formatEstablishmentHours($establecimiento->horarios),
            'position' => [$latitud, $longitud],
            'latitud' => $latitud,
            'longitud' => $longitud,
            'calle' => $domicilio->calle,
            'numero_exterior' => $domicilio->num_ext,
            'numero_interior' => $domicilio->num_int,
            'cp' => $domicilio->cp,
            'colonia' => $domicilio->colonia,
        ];
    }

    private function formatEstablishmentHours(Collection $horarios): ?string
    {
        $formatted = $horarios
            ->map(function ($horario) {
                $dayName = $this->resolveWeekdayName((int) ($horario->dia_semana ?? 0));

                if (($horario->cerrado ?? false) === true) {
                    return $dayName !== '' ? $dayName . ': Cerrado' : null;
                }

                $horaApertura = $horario->hora_apertura ? substr((string) $horario->hora_apertura, 0, 5) : null;
                $horaCierre = $horario->hora_cierra ? substr((string) $horario->hora_cierra, 0, 5) : null;

                if ($dayName === '' && ! $horaApertura && ! $horaCierre) {
                    return null;
                }

                if ($horaApertura && $horaCierre) {
                    return trim($dayName . ': ' . $horaApertura . ' - ' . $horaCierre, ': ');
                }

                return $dayName !== '' ? $dayName : null;
            })
            ->filter()
            ->values();

        return $formatted->isNotEmpty() ? $formatted->implode(' | ') : null;
    }

    private function resolveWeekdayName(int $dayNumber): string
    {
        return match ($dayNumber) {
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miercoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sabado',
            7 => 'Domingo',
            default => '',
        };
    }
}
