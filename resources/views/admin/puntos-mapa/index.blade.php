@extends('layouts.app')
@section('title', 'Puntos Mapa')
@section('title-section', 'Administracion de Puntos Mapa')
@section('description', 'Consulta los puntos del mapa y revisa su estatus, categoria e imagen principal desde un solo lugar.')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
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
        $puntosMapa = $puntosMapa ?? collect();
        $puntosActivos = $puntosMapa->filter(fn ($punto) => (string) ($punto->estatus ?? 1) === '1')->count();
        $puntosInactivos = $puntosMapa->filter(fn ($punto) => (string) ($punto->estatus ?? 1) === '0')->count();
    @endphp

    <div class="admin-shell mb-5 overflow-hidden">
        <div class="bg-[linear-gradient(135deg,#2f1821,#61102a)] px-6 py-6 text-white">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#ffd175]">Panel cartografico</p>
                    <h2 class="mt-3 text-2xl font-semibold">Lista de Puntos Mapa</h2>
                    <p class="mt-2 text-sm leading-7 text-white/78">Revisa rapidamente la foto principal, el nombre, el estatus y la categoria de cada punto registrado.</p>
                </div>
                <a href="{{ route('admin.puntos-mapa.create') }}"
                    class="inline-flex items-center rounded-full bg-white px-4 py-2 text-sm font-semibold text-[#63102a] shadow-[0_10px_24px_rgba(0,0,0,0.14)] transition hover:bg-[#fff2f5] focus:outline-none focus:ring-2 focus:ring-white/40">
                    Agregar Punto
                </a>
            </div>
        </div>

        <div class="grid gap-4 px-6 py-5 md:grid-cols-3">
            <div class="admin-stat">
                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[#8d6b10]">Total</p>
                <p class="mt-3 text-4xl font-bold leading-none text-[#201815]">{{ $puntosMapa->count() }}</p>
                <p class="mt-2 text-sm text-[#6d5a62]">Puntos registrados en el sistema.</p>
            </div>
            <div class="admin-stat">
                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-emerald-700">Activos</p>
                <p class="mt-3 text-4xl font-bold leading-none text-[#201815]">{{ $puntosActivos }}</p>
                <p class="mt-2 text-sm text-[#6d5a62]">Puntos visibles actualmente.</p>
            </div>
            <div class="admin-stat">
                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-rose-700">Inactivos</p>
                <p class="mt-3 text-4xl font-bold leading-none text-[#201815]">{{ $puntosInactivos }}</p>
                <p class="mt-2 text-sm text-[#6d5a62]">Puntos deshabilitados o pendientes.</p>
            </div>
        </div>
    </div>

    <div class="admin-shell admin-table p-5">
        <div class="mb-5">
            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[#8d6b10]">Listado</p>
            <h3 class="mt-2 text-lg font-semibold text-[#201815]">Puntos registrados</h3>
            <p class="mt-1 text-sm text-[#7d6870]">La tabla permite buscar, ordenar y paginar los puntos del mapa de forma mas clara.</p>
        </div>

        <div class="overflow-x-auto">
            <table id="tabla-puntos-mapa" class="display stripe hover w-full text-sm">
                <thead>
                    <tr>
                        <th>Foto principal</th>
                        <th>Nombre</th>
                        <th>Categoria</th>
                        <th>Latitud</th>
                        <th>Longitud</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($puntosMapa as $punto)
                        @php
                            $fotoPrincipal = $punto->foto_principal ? route('admin.puntos-mapa.foto', $punto) : null;
                            $puedeEditar = Route::has('admin.puntos-mapa.edit');
                            $puedeEliminar = Route::has('admin.puntos-mapa.destroy');
                        @endphp
                        <tr>
                            <td>
                                @if ($fotoPrincipal)
                                    <img src="{{ $fotoPrincipal }}" alt="Foto principal de {{ $punto->nombre_punto }}"
                                        class="h-16 w-16 min-w-16 rounded-2xl object-cover shadow-sm">
                                @else
                                    <div class="flex h-16 w-16 min-w-16 items-center justify-center rounded-2xl bg-[#f7ede6] text-[11px] font-semibold text-[#8a6b5b]">
                                        Sin foto
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="min-w-[220px]">
                                    <p class="font-semibold text-[#201815]">{{ $punto->nombre_punto ?? 'Sin nombre' }}</p>
                                    <p class="mt-1 text-xs text-[#7d6870]">Punto cartografico</p>
                                </div>
                            </td>
                            <td>{{ $punto->categoria->tipo ?? 'Sin categoria' }}</td>
                            <td>{{ $punto->latitud ?? 'Sin dato' }}</td>
                            <td>{{ $punto->longitud ?? 'Sin dato' }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    @if ($puedeEditar)
                                        <a href="{{ route('admin.puntos-mapa.edit', $punto) }}"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-sky-100 text-sky-700 transition hover:bg-sky-200"
                                            title="Editar" aria-label="Editar punto de mapa">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16.862 4.487a2.25 2.25 0 1 1 3.182 3.182L8.25 19.462 4.5 20.5l1.038-3.75L16.862 4.487Z" />
                                            </svg>
                                        </a>
                                    @else
                                        <span
                                            class="inline-flex h-9 w-9 cursor-not-allowed items-center justify-center rounded-full bg-slate-100 text-slate-400"
                                            title="Ruta de edicion pendiente" aria-label="Edicion no disponible">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16.862 4.487a2.25 2.25 0 1 1 3.182 3.182L8.25 19.462 4.5 20.5l1.038-3.75L16.862 4.487Z" />
                                            </svg>
                                        </span>
                                    @endif

                                    @if ($puedeEliminar)
                                        <form method="POST" action="{{ route('admin.puntos-mapa.destroy', $punto) }}"
                                            class="form-eliminar-punto inline-flex"
                                            data-punto-nombre="{{ $punto->nombre_punto ?? 'este punto' }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-rose-100 text-rose-700 transition hover:bg-rose-200"
                                                title="Eliminar" aria-label="Eliminar punto de mapa">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6 7.5h12m-9.75 0V6a1.5 1.5 0 0 1 1.5-1.5h4.5A1.5 1.5 0 0 1 15.75 6v1.5m-7.5 0v10.125A1.875 1.875 0 0 0 10.125 19.5h3.75a1.875 1.875 0 0 0 1.875-1.875V7.5M10.5 10.5v6m3-6v6" />
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <span
                                            class="inline-flex h-9 w-9 cursor-not-allowed items-center justify-center rounded-full bg-slate-100 text-slate-400"
                                            title="Ruta de eliminacion pendiente" aria-label="Eliminacion no disponible">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6 7.5h12m-9.75 0V6a1.5 1.5 0 0 1 1.5-1.5h4.5A1.5 1.5 0 0 1 15.75 6v1.5m-7.5 0v10.125A1.875 1.875 0 0 0 10.125 19.5h3.75a1.875 1.875 0 0 0 1.875-1.875V7.5M10.5 10.5v6m3-6v6" />
                                            </svg>
                                        </span>
                                    @endif
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

            $('#tabla-puntos-mapa').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
                },
                pageLength: 10,
                order: [
                    [1, 'asc']
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

            $('.form-eliminar-punto').on('submit', function(event) {
                event.preventDefault();

                const form = this;
                const nombre = form.dataset.puntoNombre || 'este punto';

                Swal.fire({
                    icon: 'warning',
                    title: 'Eliminar punto',
                    text: `Se eliminara ${nombre}. Esta accion no se puede deshacer.`,
                    showCancelButton: true,
                    confirmButtonText: 'Si, eliminar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#be123c',
                    cancelButtonColor: '#64748b'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
