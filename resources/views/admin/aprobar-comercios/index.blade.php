@extends('layouts.app')
@section('title', 'Aprobar Comercios')
@section('title-section', 'Aprobar Comercios')
@section('description', 'Revisa los comercios registrados y aprueba los que ya esten listos para publicarse.')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <style>
        .admin-shell {
            border: 1px solid rgba(97, 16, 42, 0.08);
            border-radius: 26px;
            background: #fff;
            box-shadow: 0 18px 40px rgba(97, 18, 50, 0.07);
        }

        .admin-stat {
            border: 1px solid rgba(97, 16, 42, 0.08);
            border-radius: 22px;
            background: linear-gradient(180deg, #fff, #fff8f3);
            padding: 1.1rem 1.25rem;
        }

        .admin-table table.dataTable thead th {
            color: #6b5560;
            font-size: 12px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .admin-table .dataTables_wrapper .dataTables_filter input,
        .admin-table .dataTables_wrapper .dataTables_length select {
            border: 1px solid #e7d8dc;
            border-radius: 999px;
            padding: 0.45rem 0.85rem;
            background: #fff;
        }

    </style>
@endpush

@section('content')
    @php
        $comercios = $comercios ?? collect();
        $comerciosPendientes = $comercios->filter(fn ($comercio) => (int) ($comercio->estatus_registro ?? 0) === \App\Models\Preregistro::ESTATUS_PENDIENTE)->count();
        $comerciosAprobados = $comercios->filter(fn ($comercio) => (int) ($comercio->estatus_registro ?? 0) === \App\Models\Preregistro::ESTATUS_ACEPTADO)->count();
        $comerciosCorreccion = $comercios->filter(fn ($comercio) => (int) ($comercio->estatus_registro ?? 0) === \App\Models\Preregistro::ESTATUS_REQUIERE_CORRECCION)->count();
        $comerciosRechazados = $comercios->filter(fn ($comercio) => (int) ($comercio->estatus_registro ?? 0) === \App\Models\Preregistro::ESTATUS_RECHAZADO_DEFINITIVO)->count();
    @endphp

    <div class="admin-shell mb-5 overflow-hidden">
        <div class="bg-[linear-gradient(135deg,#2f1821,#61102a)] px-6 py-6 text-white">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#ffd175]">Panel de revision</p>
                    <h2 class="mt-3 text-2xl font-semibold">Aprobar Comercios</h2>
                    <p class="mt-2 text-sm leading-7 text-white/78">Revisa cada comercio, valida sus datos principales y aprueba los registros pendientes.</p>
                </div>
            </div>
        </div>

        <div class="grid gap-4 px-6 py-5 md:grid-cols-5">
            <div class="admin-stat">
                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[#8d6b10]">Total</p>
                <p class="mt-3 text-4xl font-bold leading-none text-[#201815]">{{ $comercios->count() }}</p>
                <p class="mt-2 text-sm text-[#6d5a62]">Comercios registrados en el sistema.</p>
            </div>
            <div class="admin-stat">
                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-amber-700">Pendientes</p>
                <p class="mt-3 text-4xl font-bold leading-none text-[#201815]">{{ $comerciosPendientes }}</p>
                <p class="mt-2 text-sm text-[#6d5a62]">Comercios en espera de aprobacion.</p>
            </div>
            <div class="admin-stat">
                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-emerald-700">Aprobados</p>
                <p class="mt-3 text-4xl font-bold leading-none text-[#201815]">{{ $comerciosAprobados }}</p>
                <p class="mt-2 text-sm text-[#6d5a62]">Comercios ya habilitados en la plataforma.</p>
            </div>
            <div class="admin-stat">
                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-sky-700">Correccion</p>
                <p class="mt-3 text-4xl font-bold leading-none text-[#201815]">{{ $comerciosCorreccion }}</p>
                <p class="mt-2 text-sm text-[#6d5a62]">Registros que aun pueden corregirse.</p>
            </div>
            <div class="admin-stat">
                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-rose-700">Rechazados</p>
                <p class="mt-3 text-4xl font-bold leading-none text-[#201815]">{{ $comerciosRechazados }}</p>
                <p class="mt-2 text-sm text-[#6d5a62]">Expedientes cerrados de forma definitiva.</p>
            </div>
        </div>
    </div>

    <div class="admin-shell admin-table p-5">
        <div class="mb-5">
            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[#8d6b10]">Listado</p>
            <h3 class="mt-2 text-lg font-semibold text-[#201815]">Solicitudes de comercios</h3>
            <p class="mt-1 text-sm text-[#7d6870]">La tabla permite buscar, ordenar y revisar rapidamente cada solicitud.</p>
        </div>

        <div class="overflow-x-auto">
            <table id="tabla-aprobar-comercios" class="display stripe hover w-full text-sm">
                <thead>
                    <tr>
                        <th>Establecimiento</th>
                        <th>Titular</th>
                        <th>Razon social</th>
                        <th>Telefono</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($comercios as $comercio)
                        @php
                            $titular = collect([
                                $comercio->nombre_p,
                                $comercio->app_p,
                                $comercio->apm_p,
                            ])->filter()->implode(' ');

                            if ($titular === '') {
                                $titular = 'Sin titular';
                            }

                            $telefono = $comercio->telefono ?: 'Sin telefono';
                            $estadoRegistro = (int) ($comercio->estatus_registro ?? \App\Models\Preregistro::ESTATUS_PENDIENTE);
                        @endphp
                        <tr>
                            <td>
                                <div class="min-w-[220px]">
                                    <p class="font-semibold text-[#201815]">{{ $comercio->nombre_est ?: 'Sin nombre' }}</p>
                                    <p class="mt-1 text-xs text-[#7d6870]">Folio {{ $comercio->id_preresgistro }}</p>
                                </div>
                            </td>
                            <td>{{ $titular }}</td>
                            <td>{{ $comercio->razon_social ?: 'Sin razon social' }}</td>
                            <td>{{ $telefono }}</td>
                            <td>{{ $comercio->tipoRelacion?->nombre ?: 'Sin tipo' }}</td>
                            <td>
                                @if ($estadoRegistro === \App\Models\Preregistro::ESTATUS_PENDIENTE)
                                    <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                        Pendiente
                                    </span>
                                @elseif ($estadoRegistro === \App\Models\Preregistro::ESTATUS_ACEPTADO)
                                    <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                                        Aceptado
                                    </span>
                                @elseif ($estadoRegistro === \App\Models\Preregistro::ESTATUS_REQUIERE_CORRECCION)
                                    <span class="inline-flex rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700">
                                        Requiere correccion
                                    </span>
                                @elseif ($estadoRegistro === \App\Models\Preregistro::ESTATUS_RECHAZADO_DEFINITIVO)
                                    <span class="inline-flex rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700">
                                        Rechazado definitivo
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-700">
                                        {{ $estadoRegistro }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.aprobar-comercios.show', $comercio) }}"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-700 transition hover:bg-slate-200"
                                        title="Ver detalle" aria-label="Ver detalle del comercio">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.644C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.433 0 .644C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(function() {
            const successMessage = @json(session('success'));
            const errorMessage = @json(session('error'));

            $('#tabla-aprobar-comercios').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
                },
                pageLength: 10,
                order: [
                    [5, 'asc']
                ]
            });

            if (successMessage) {
                Swal.fire({
                    icon: 'success',
                    title: 'Correcto',
                    text: successMessage,
                    confirmButtonColor: '#63102a'
                });
            }

            if (errorMessage) {
                Swal.fire({
                    icon: 'error',
                    title: 'No fue posible completar la accion',
                    text: errorMessage,
                    confirmButtonColor: '#63102a'
                });
            }
        });
    </script>
@endpush
