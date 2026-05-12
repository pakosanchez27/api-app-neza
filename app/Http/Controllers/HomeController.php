<?php

namespace App\Http\Controllers;

use App\Models\Establecimiento;
use App\Models\PasaporteSello;
use App\Models\PasaporteUsuario;
use App\Models\Ruta;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class HomeController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $totalUsuarios = User::count();
        $totalComercios = Establecimiento::count();
        $usuariosNuevosSemana = User::where('created_at', '>=', $now->copy()->startOfWeek())->count();
        $usuariosNuevosMes = User::where('created_at', '>=', $now->copy()->startOfMonth())->count();
        $comerciosVisibles = Establecimiento::where('is_visible', true)
            ->where('estatus', true)
            ->count();
        $comerciosIncompletos = Establecimiento::where('estatus', false)->count();
        $totalRutasActivas = Ruta::where('is_active', true)->count();
        $totalPasaportes = PasaporteUsuario::count();
        $pasaportesCompletados = PasaporteUsuario::whereNotNull('completed_at')->count();
        $totalSellos = PasaporteSello::count();

        $routeEstablishmentCounts = Ruta::withCount('establecimientos')
            ->get()
            ->pluck('establecimientos_count', 'id_ruta');

        $topUsuariosPasaporte = PasaporteUsuario::query()
            ->with(['user:id,name,nombre_p,app_p,apm_p,email'])
            ->withCount('sellos')
            ->get()
            ->groupBy('user_id')
            ->map(function (Collection $pasaportes) use ($routeEstablishmentCounts) {
                $user = $pasaportes->first()->user;
                $totalPosibles = $pasaportes->sum(
                    fn (PasaporteUsuario $pasaporte) => (int) ($routeEstablishmentCounts[$pasaporte->id_ruta] ?? 0)
                );
                $totalSellosUsuario = $pasaportes->sum('sellos_count');
                $pasaportesCompletadosUsuario = $pasaportes->filter(
                    fn (PasaporteUsuario $pasaporte) => $pasaporte->completed_at !== null
                )->count();
                $progresoPromedio = $totalPosibles > 0
                    ? round(($totalSellosUsuario / $totalPosibles) * 100, 1)
                    : 0;

                $nombreCompleto = collect([
                    $user?->nombre_p,
                    $user?->app_p,
                    $user?->apm_p,
                ])->filter()->implode(' ');

                if ($nombreCompleto === '') {
                    $nombreCompleto = $user?->name ?: 'Usuario sin nombre';
                }

                return [
                    'nombre' => $nombreCompleto,
                    'email' => $user?->email,
                    'pasaportes' => $pasaportes->count(),
                    'pasaportes_completados' => $pasaportesCompletadosUsuario,
                    'sellos' => $totalSellosUsuario,
                    'sellos_posibles' => $totalPosibles,
                    'progreso' => $progresoPromedio,
                ];
            })
            ->sort(function (array $a, array $b) {
                if ($a['progreso'] === $b['progreso']) {
                    return $b['sellos'] <=> $a['sellos'];
                }

                return $b['progreso'] <=> $a['progreso'];
            })
            ->take(10)
            ->values();

        $topComerciosPasaporte = Establecimiento::query()
            ->with(['tipo:id_tipo,nombre'])
            ->withCount('pasaporteSellos')
            ->having('pasaporte_sellos_count', '>', 0)
            ->orderByDesc('pasaporte_sellos_count')
            ->orderByDesc('updated_at')
            ->take(10)
            ->get()
            ->map(function (Establecimiento $establecimiento) {
                return [
                    'nombre' => $establecimiento->nombre_est,
                    'tipo' => $establecimiento->tipo?->nombre ?: 'Sin categoría',
                    'sellos' => (int) $establecimiento->pasaporte_sellos_count,
                    'visible' => (bool) $establecimiento->is_visible,
                    'activo' => (bool) $establecimiento->estatus,
                ];
            });

        return view('admin.dashboard', compact(
            'totalUsuarios',
            'totalComercios',
            'usuariosNuevosSemana',
            'usuariosNuevosMes',
            'comerciosVisibles',
            'comerciosIncompletos',
            'totalRutasActivas',
            'totalPasaportes',
            'pasaportesCompletados',
            'totalSellos',
            'topUsuariosPasaporte',
            'topComerciosPasaporte',
        ));
    }
}
