@extends('layouts.app')
@section('title', 'Noticias')
@section('title-section', 'Administracion de Noticias')
@section('description', 'Administra las noticias de Neza y da seguimiento a sus publicaciones desde un panel mas claro y ordenado.')

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
        $noticias = $noticias ?? collect();
        $noticiasActivas = $noticias->filter(fn($noticia) => (string) ($noticia->estatus ?? 1) === '1')->count();
        $noticiasInactivas = $noticias->filter(fn($noticia) => (string) ($noticia->estatus ?? 1) === '0')->count();
    @endphp

    <div class="admin-shell mb-5 overflow-hidden">
        <div class="bg-[linear-gradient(135deg,#2f1821,#61102a)] px-6 py-6 text-white">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#ffd175]">Panel editorial</p>
                    <h2 class="mt-3 text-2xl font-semibold">Lista de Noticias</h2>
                    <p class="mt-2 text-sm leading-7 text-white/78">Consulta publicaciones, revisa su estatus y entra rapido a editar o eliminar desde una sola vista.</p>
                </div>
                <a href="{{ route('admin.noticias.create') }}"
                    class="inline-flex items-center rounded-full bg-white px-4 py-2 text-sm font-semibold text-[#63102a] shadow-[0_10px_24px_rgba(0,0,0,0.14)] transition hover:bg-[#fff2f5] focus:outline-none focus:ring-2 focus:ring-white/40">
                    Crear Noticia
                </a>
            </div>
        </div>

        <div class="grid gap-4 px-6 py-5 md:grid-cols-3">
            <div class="admin-stat">
                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[#8d6b10]">Total</p>
                <p class="mt-3 text-4xl font-bold leading-none text-[#201815]">{{ $noticias->count() }}</p>
                <p class="mt-2 text-sm text-[#6d5a62]">Noticias registradas en el sistema.</p>
            </div>
            <div class="admin-stat">
                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-emerald-700">Activas</p>
                <p class="mt-3 text-4xl font-bold leading-none text-[#201815]">{{ $noticiasActivas }}</p>
                <p class="mt-2 text-sm text-[#6d5a62]">Publicaciones visibles actualmente.</p>
            </div>
            <div class="admin-stat">
                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-rose-700">Inactivas</p>
                <p class="mt-3 text-4xl font-bold leading-none text-[#201815]">{{ $noticiasInactivas }}</p>
                <p class="mt-2 text-sm text-[#6d5a62]">Registros deshabilitados o pausados.</p>
            </div>
        </div>
    </div>

    <div class="admin-shell admin-table p-5">
        <div class="mb-5">
            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[#8d6b10]">Listado</p>
            <h3 class="mt-2 text-lg font-semibold text-[#201815]">Publicaciones registradas</h3>
            <p class="mt-1 text-sm text-[#7d6870]">La tabla permite buscar, ordenar y paginar noticias de forma mas clara.</p>
        </div>

        <div class="overflow-x-auto">
            <table id="tabla-noticias" class="display stripe hover w-full text-sm">
                <thead>
                    <tr>
                        <th>Titulo</th>
                        <th>Fecha de publicacion</th>
                        <th>Estatus</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($noticias as $noticia)
                        @php
                            $estatus = $noticia->estatus ?? 1;
                            $fechaPublicacion = $noticia->fecha_publicacion ?? $noticia->published_at ?? $noticia->created_at ?? null;
                        @endphp
                        <tr>
                            <td>
                                <div class="min-w-[220px]">
                                    <p class="font-semibold text-[#201815]">{{ $noticia->titulo ?? 'Sin titulo' }}</p>
                                    <p class="mt-1 text-xs text-[#7d6870]">Registro editorial</p>
                                </div>
                            </td>
                            <td>{{ $fechaPublicacion ? \Carbon\Carbon::parse($fechaPublicacion)->format('Y-m-d') : 'Sin fecha' }}</td>
                            <td>
                                @if ((string) $estatus === '1' || $estatus === true || $estatus === 'activo')
                                    <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                                        Activo
                                    </span>
                                @elseif ((string) $estatus === '0' || $estatus === false || $estatus === 'inactivo')
                                    <span class="inline-flex rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700">
                                        Inactivo
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-700">
                                        {{ ucfirst((string) $estatus) }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.noticias.edit', $noticia) }}"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-sky-100 text-sky-700 transition hover:bg-sky-200"
                                        title="Editar" aria-label="Editar noticia">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.862 4.487a2.25 2.25 0 1 1 3.182 3.182L8.25 19.462 4.5 20.5l1.038-3.75L16.862 4.487Z" />
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.noticias.destroy', $noticia) }}"
                                        class="form-eliminar-noticia inline-flex"
                                        data-noticia-titulo="{{ $noticia->titulo ?? 'esta noticia' }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-rose-100 text-rose-700 transition hover:bg-rose-200"
                                            title="Eliminar" aria-label="Eliminar noticia">
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

            $('#tabla-noticias').DataTable({
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

            $('.form-eliminar-noticia').on('submit', function(event) {
                event.preventDefault();

                const form = this;
                const titulo = form.dataset.noticiaTitulo || 'esta noticia';

                Swal.fire({
                    icon: 'warning',
                    title: 'Eliminar noticia',
                    text: `Se eliminara ${titulo}. Esta accion no se puede deshacer.`,
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
