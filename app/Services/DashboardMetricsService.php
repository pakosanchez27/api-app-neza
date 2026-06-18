<?php

namespace App\Services;

use App\Models\Establecimiento;
use App\Models\PasaporteSello;
use App\Models\PasaporteUsuario;
use App\Models\Ruta;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class DashboardMetricsService
{
    public function summary(): array
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

        return [
            'totalUsuarios' => $totalUsuarios,
            'totalComercios' => $totalComercios,
            'usuariosNuevosSemana' => $usuariosNuevosSemana,
            'usuariosNuevosMes' => $usuariosNuevosMes,
            'comerciosVisibles' => $comerciosVisibles,
            'comerciosIncompletos' => $comerciosIncompletos,
            'totalRutasActivas' => $totalRutasActivas,
            'totalPasaportes' => $totalPasaportes,
            'pasaportesCompletados' => $pasaportesCompletados,
            'totalSellos' => $totalSellos,
            'topUsuariosPasaporte' => $this->topUsuariosPasaporte(),
            'topComerciosPasaporte' => $this->topComerciosPasaporte(),
            'rates' => [
                'pasaportes_completados' => $totalPasaportes > 0 ? round(($pasaportesCompletados / $totalPasaportes) * 100, 1) : 0,
                'comercios_visibles' => $totalComercios > 0 ? round(($comerciosVisibles / $totalComercios) * 100, 1) : 0,
                'sellos_por_pasaporte' => $totalPasaportes > 0 ? round($totalSellos / $totalPasaportes, 2) : 0,
                'comercios_incompletos' => $totalComercios > 0 ? round(($comerciosIncompletos / $totalComercios) * 100, 1) : 0,
            ],
        ];
    }

    private function topUsuariosPasaporte(): Collection
    {
        $routeEstablishmentCounts = Ruta::withCount('establecimientos')
            ->get()
            ->pluck('establecimientos_count', 'id_ruta');

        return PasaporteUsuario::query()
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
            ->filter(fn (array $usuario) => $usuario['sellos'] > 0)
            ->sort(function (array $a, array $b) {
                if ($a['sellos'] === $b['sellos']) {
                    return $b['progreso'] <=> $a['progreso'];
                }

                return $b['sellos'] <=> $a['sellos'];
            })
            ->take(10)
            ->values();
    }

    private function topComerciosPasaporte(): Collection
    {
        return Establecimiento::query()
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
                    'tipo' => $establecimiento->tipo?->nombre ?: 'Sin categoria',
                    'sellos' => (int) $establecimiento->pasaporte_sellos_count,
                    'visible' => (bool) $establecimiento->is_visible,
                    'activo' => (bool) $establecimiento->estatus,
                ];
            });
    }
}
