@extends('layouts.app')
@section('title', 'Eventos')
@section('title-section', 'Administracion de Eventos')
@section('description', 'Administra los eventos turisticos de Neza y manten un mejor control visual entre vigentes y pasados.')

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
        $hoy = \Carbon\Carbon::today();
        $eventosVigentes = $eventos->filter(fn($evento) => \Carbon\Carbon::parse($evento->fecha)->gte($hoy));
        $eventosPasados = $eventos->filter(fn($evento) => \Carbon\Carbon::parse($evento->fecha)->lt($hoy));
        $eventosDestacados = $eventos->filter(fn($evento) => (bool) $evento->is_destacado)->count();
    @endphp

    <div class="admin-shell mb-5 overflow-hidden">
        <div class="bg-[linear-gradient(135deg,#2f1821,#61102a)] px-6 py-6 text-white">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#ffd175]">Agenda turistica</p>
                    <h2 class="mt-3 text-2xl font-semibold">Lista de Eventos</h2>
                    <p class="mt-2 text-sm leading-7 text-white/78">Consulta rapidamente lo vigente, separa el historico y recuerda la regla de negocio: solo un evento puede estar marcado como destacado.</p>
                </div>
                <a href="{{ route('admin.eventos.create') }}"
                    class="inline-flex items-center rounded-full bg-white px-4 py-2 text-sm font-semibold text-[#63102a] shadow-[0_10px_24px_rgba(0,0,0,0.14)] transition hover:bg-[#fff2f5] focus:outline-none focus:ring-2 focus:ring-white/40">
                    Crear Evento
                </a>
            </div>
        </div>

        <div class="grid gap-4 px-6 py-5 md:grid-cols-4">
            <div class="admin-stat">
                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[#8d6b10]">Total</p>
                <p class="mt-3 text-4xl font-bold leading-none text-[#201815]">{{ $eventos->count() }}</p>
                <p class="mt-2 text-sm text-[#6d5a62]">Eventos registrados.</p>
            </div>
            <div class="admin-stat">
                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-emerald-700">Vigentes</p>
                <p class="mt-3 text-4xl font-bold leading-none text-[#201815]">{{ $eventosVigentes->count() }}</p>
                <p class="mt-2 text-sm text-[#6d5a62]">Eventos de hoy en adelante.</p>
            </div>
            <div class="admin-stat">
                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-700">Pasados</p>
                <p class="mt-3 text-4xl font-bold leading-none text-[#201815]">{{ $eventosPasados->count() }}</p>
                <p class="mt-2 text-sm text-[#6d5a62]">Historico de actividades.</p>
            </div>
            <div class="admin-stat">
                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-amber-700">Destacados</p>
                <p class="mt-3 text-4xl font-bold leading-none text-[#201815]">{{ $eventosDestacados }}</p>
                <p class="mt-2 text-sm text-[#6d5a62]">Eventos con mayor visibilidad.</p>
            </div>
        </div>
    </div>

    <div class="admin-shell admin-table p-5">
        <div class="mb-5">
            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[#8d6b10]">Agenda activa</p>
            <h3 class="mt-2 text-lg font-semibold text-[#201815]">Eventos actuales y proximos</h3>
            <p class="mt-1 text-sm text-[#7d6870]">Aqui se muestran los eventos con fecha de hoy en adelante.</p>
        </div>
        <div class="overflow-x-auto">
            <table id="tabla-eventos" class="display stripe hover w-full text-sm">
                <thead>
                    <tr>
                        <th>Titulo</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Destacado</th>
                        <th>Estatus</th>
                        <th>Categoria</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($eventosVigentes as $evento)
                        <tr>
                            <td>
                                <div class="min-w-[220px]">
                                    <p class="font-semibold text-[#201815]">{{ $evento->titulo }}</p>
                                    <p class="mt-1 text-xs text-[#7d6870]">Evento vigente</p>
                                </div>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($evento->fecha)->format('Y-m-d') }}</td>
                            <td>{{ \Carbon\Carbon::parse($evento->hora)->format('H:i') }}</td>
                            <td>
                                @if ($evento->is_destacado)
                                    <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">
                                        Si
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                        -
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if ((int) $evento->estatus === 1)
                                    <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                                        Activo
                                    </span>
                                @elseif ((int) $evento->estatus === 0)
                                    <span class="inline-flex rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700">
                                        Inactivo
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-700">
                                        Vencido
                                    </span>
                                @endif
                            </td>
                            <td>{{ $evento->categoria?->nombre ?? 'Sin categoria' }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.eventos.edit', $evento) }}"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-sky-100 text-sky-700 transition hover:bg-sky-200"
                                        title="Editar" aria-label="Editar evento">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.862 4.487a2.25 2.25 0 1 1 3.182 3.182L8.25 19.462 4.5 20.5l1.038-3.75L16.862 4.487Z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.eventos.destroy', $evento) }}" method="POST"
                                        class="form-eliminar-evento" data-titulo="{{ $evento->titulo }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-rose-100 text-rose-700 transition hover:bg-rose-200"
                                            title="Eliminar" aria-label="Eliminar evento">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6 7.5h12m-9.75 0V6a1.5 1.5 0 0 1 1.5-1.5h4.5A1.5 1.5 0 0 1 15.75 6v1.5m-7.5 0v10.125A1.875 1.875 0 0 0 10.125 19.5h3.75a1.875 1.875 0 0 0 1.875-1.875V7.5M10.5 10.5v6m3-6v6" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="admin-shell admin-table mt-6 p-5">
        <div class="mb-5">
            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[#8d6b10]">Historico</p>
            <h3 class="mt-2 text-lg font-semibold text-[#201815]">Eventos pasados</h3>
            <p class="mt-1 text-sm text-[#7d6870]">Consulta el registro de eventos cuya fecha ya paso.</p>
        </div>
        <div class="overflow-x-auto">
            <table id="tabla-eventos-pasados" class="display stripe hover w-full text-sm">
                <thead>
                    <tr>
                        <th>Titulo</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Destacado</th>
                        <th>Estatus</th>
                        <th>Categoria</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($eventosPasados as $evento)
                        <tr>
                            <td>
                                <div class="min-w-[220px]">
                                    <p class="font-semibold text-[#201815]">{{ $evento->titulo }}</p>
                                    <p class="mt-1 text-xs text-[#7d6870]">Evento historico</p>
                                </div>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($evento->fecha)->format('Y-m-d') }}</td>
                            <td>{{ \Carbon\Carbon::parse($evento->hora)->format('H:i') }}</td>
                            <td>
                                @if ($evento->is_destacado)
                                    <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">
                                        Si
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                        -
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if ((int) $evento->estatus === 1)
                                    <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                                        Activo
                                    </span>
                                @elseif ((int) $evento->estatus === 0)
                                    <span class="inline-flex rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700">
                                        Inactivo
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-700">
                                        Vencido
                                    </span>
                                @endif
                            </td>
                            <td>{{ $evento->categoria?->nombre ?? 'Sin categoria' }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.eventos.edit', $evento) }}"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-sky-100 text-sky-700 transition hover:bg-sky-200"
                                        title="Editar" aria-label="Editar evento">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.862 4.487a2.25 2.25 0 1 1 3.182 3.182L8.25 19.462 4.5 20.5l1.038-3.75L16.862 4.487Z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.eventos.destroy', $evento) }}" method="POST"
                                        class="form-eliminar-evento" data-titulo="{{ $evento->titulo }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-rose-100 text-rose-700 transition hover:bg-rose-200"
                                            title="Eliminar" aria-label="Eliminar evento">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6 7.5h12m-9.75 0V6a1.5 1.5 0 0 1 1.5-1.5h4.5A1.5 1.5 0 0 1 15.75 6v1.5m-7.5 0v10.125A1.875 1.875 0 0 0 10.125 19.5h3.75a1.875 1.875 0 0 0 1.875-1.875V7.5M10.5 10.5v6m3-6v6" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
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
            const successMessage = @json(session('success') ?? request()->query('success'));
            const formsEliminar = document.querySelectorAll('.form-eliminar-evento');

            $('#tabla-eventos').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
                },
                pageLength: 10,
                order: [
                    [1, 'desc']
                ]
            });

            $('#tabla-eventos-pasados').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
                },
                pageLength: 10,
                order: [
                    [1, 'desc']
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

            formsEliminar.forEach((formulario) => {
                formulario.addEventListener('submit', function(event) {
                    event.preventDefault();

                    const tituloEvento = formulario.dataset.titulo || 'este evento';

                    Swal.fire({
                        icon: 'warning',
                        title: 'Eliminar evento',
                        text: `Seguro que deseas eliminar "${tituloEvento}"? Esta accion no se puede deshacer.`,
                        showCancelButton: true,
                        confirmButtonText: 'Si, eliminar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#be123c',
                        cancelButtonColor: '#94a3b8'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            formulario.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush
