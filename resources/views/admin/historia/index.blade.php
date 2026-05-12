@extends('layouts.app')
@section('title', 'Historia')
@section('title-section', 'Administracion de Historia')
@section('description', 'Administra la historia de Neza, consulta sus registros y gestiona activaciones, desactivaciones y vistas previas.')

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

        .historia-modal-contenido {
            text-align: left;
        }

        .historia-modal-contenido img {
            display: block;
            max-width: 100%;
        }
    </style>
@endpush

@section('content')
    @php
        $historiasJson = $historias->map(function ($historia) {
            return [
                'id' => $historia->id,
                'titulo' => $historia->titulo,
                'autor' => $historia->autor,
                'resumen_corto' => $historia->resumen_corto,
                'periodo' => $historia->periodo,
                'desarrollo' => $historia->desarrollo,
                'portada' => $historia->portada ? \App\Support\ImageManager::publicUrl($historia->portada) : null,
                'fecha_publicacion' => $historia->fecha_publicacion ? \Carbon\Carbon::parse($historia->fecha_publicacion)->format('Y-m-d') : 'Sin fecha',
                'estatus' => (string) ($historia->estatus ?? '0'),
                'galeria' => $historia->galeria->map(function ($imagen) {
                    return \App\Support\ImageManager::publicUrl($imagen->imagen);
                })->values(),
                'fuentes' => $historia->fuentes->map(function ($fuente) {
                    return [
                        'titulo' => $fuente->titulo,
                        'descripcion' => $fuente->descripcion,
                        'url' => $fuente->url,
                    ];
                })->values(),
            ];
        });
        $historiasActivas = $historias->filter(fn($historia) => (string) ($historia->estatus ?? '0') === '1')->count();
        $historiasInactivas = $historias->filter(fn($historia) => (string) ($historia->estatus ?? '0') === '0')->count();
    @endphp

    <div class="admin-shell mb-5 overflow-hidden">
        <div class="bg-[linear-gradient(135deg,#2f1821,#61102a)] px-6 py-6 text-white">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#ffd175]">Memoria urbana</p>
                    <h2 class="mt-3 text-2xl font-semibold">Lista de Datos Historicos</h2>
                    <p class="mt-2 text-sm leading-7 text-white/78">Organiza registros historicos, previsualiza su contenido y administra su disponibilidad desde un panel mas claro.</p>
                </div>
                <a href="{{ route('admin.historia.create') }}"
                    class="inline-flex items-center rounded-full bg-white px-4 py-2 text-sm font-semibold text-[#63102a] shadow-[0_10px_24px_rgba(0,0,0,0.14)] transition hover:bg-[#fff2f5] focus:outline-none focus:ring-2 focus:ring-white/40">
                    Crear Dato Historico
                </a>
            </div>
        </div>

        <div class="grid gap-4 px-6 py-5 md:grid-cols-3">
            <div class="admin-stat">
                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[#8d6b10]">Total</p>
                <p class="mt-3 text-4xl font-bold leading-none text-[#201815]">{{ $historias->count() }}</p>
                <p class="mt-2 text-sm text-[#6d5a62]">Registros capturados en el panel.</p>
            </div>
            <div class="admin-stat">
                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-emerald-700">Publicadas</p>
                <p class="mt-3 text-4xl font-bold leading-none text-[#201815]">{{ $historiasActivas }}</p>
                <p class="mt-2 text-sm text-[#6d5a62]">Historias activas para el frontend.</p>
            </div>
            <div class="admin-stat">
                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-700">Inactivas</p>
                <p class="mt-3 text-4xl font-bold leading-none text-[#201815]">{{ $historiasInactivas }}</p>
                <p class="mt-2 text-sm text-[#6d5a62]">Registros pausados o archivados.</p>
            </div>
        </div>
    </div>

    <div class="admin-shell admin-table p-5">
        <div class="mb-5">
            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[#8d6b10]">Listado</p>
            <h3 class="mt-2 text-lg font-semibold text-[#201815]">Registros de historia</h3>
            <p class="mt-1 text-sm text-[#7d6870]">Busca, ordena, abre la vista previa y administra el estado de cada dato historico.</p>
        </div>

        <div class="overflow-x-auto">
            <table id="tabla-historia" class="display stripe hover w-full text-sm">
                <thead>
                    <tr>
                        <th>Titulo</th>
                        <th>Autor</th>
                        <th>Fecha de publicacion</th>
                        <th>Estatus</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($historias as $historia)
                        @php
                            $estatus = $historia->estatus ?? '0';
                            $fechaPublicacion = $historia->fecha_publicacion ?? null;
                        @endphp
                        <tr>
                            <td>
                                <div class="min-w-[230px]">
                                    <p class="font-semibold text-[#201815]">{{ $historia->titulo ?? 'Sin titulo' }}</p>
                                    <p class="mt-1 text-xs text-[#7d6870]">{{ $historia->periodo ?? 'Sin periodo' }}</p>
                                </div>
                            </td>
                            <td>{{ $historia->autor ?? 'Sin autor' }}</td>
                            <td>{{ $fechaPublicacion ? \Carbon\Carbon::parse($fechaPublicacion)->format('Y-m-d') : 'Sin fecha' }}</td>
                            <td>
                                @if (in_array((string) $estatus, ['published', 'activo', '1'], true))
                                    <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                                        Publicado
                                    </span>
                                @elseif (in_array((string) $estatus, ['draft', 'borrador', '0'], true))
                                    <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">
                                        Borrador
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-700">
                                        {{ ucfirst((string) $estatus) }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <button type="button"
                                        data-historia-id="{{ $historia->id }}"
                                        class="btn-ver-historia inline-flex h-9 w-9 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 transition hover:bg-emerald-200"
                                        title="Ver" aria-label="Ver dato historico">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.644C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.21.07.434 0 .644C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    </button>
                                    <a href="{{ route('admin.historia.edit', $historia) }}"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-sky-100 text-sky-700 transition hover:bg-sky-200"
                                        title="Editar" aria-label="Editar dato historico">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.862 4.487a2.25 2.25 0 1 1 3.182 3.182L8.25 19.462 4.5 20.5l1.038-3.75L16.862 4.487Z" />
                                        </svg>
                                    </a>
                                    @if (in_array((string) $estatus, ['published', 'activo', '1'], true))
                                        <form method="POST" action="{{ route('admin.historia.destroy', $historia) }}" class="form-toggle-historia inline-flex"
                                            data-historia-titulo="{{ $historia->titulo ?? 'este dato historico' }}"
                                            data-action-label="desactivar"
                                            data-action-title="Cambiar a inactivo"
                                            data-action-text="Se marcara como inactivo">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-rose-100 text-rose-700 transition hover:bg-rose-200"
                                                title="Desactivar" aria-label="Desactivar dato historico">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6 7.5h12m-9.75 0V6a1.5 1.5 0 0 1 1.5-1.5h4.5A1.5 1.5 0 0 1 15.75 6v1.5m-7.5 0v10.125A1.875 1.875 0 0 0 10.125 19.5h3.75a1.875 1.875 0 0 0 1.875-1.875V7.5M10.5 10.5v6m3-6v6" />
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.historia.activate', $historia) }}" class="form-toggle-historia inline-flex"
                                            data-historia-titulo="{{ $historia->titulo ?? 'este dato historico' }}"
                                            data-action-label="activar"
                                            data-action-title="Activar dato historico"
                                            data-action-text="Se marcara como activo">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 transition hover:bg-emerald-200"
                                                title="Activar" aria-label="Activar dato historico">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M4.5 12.75l6 6 9-13.5" />
                                                </svg>
                                            </button>
                                        </form>
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
            const historias = @json($historiasJson);
            const escapeHtml = (value) => $('<div>').text(value ?? '').html();

            const getStatusBadge = (estatus) => {
                if (['published', 'activo', '1'].includes(String(estatus))) {
                    return '<span style="display:inline-flex;border-radius:999px;background:#d1fae5;padding:6px 12px;font-size:12px;font-weight:600;color:#047857;">Publicado</span>';
                }

                if (['draft', 'borrador', '0'].includes(String(estatus))) {
                    return '<span style="display:inline-flex;border-radius:999px;background:#fef3c7;padding:6px 12px;font-size:12px;font-weight:600;color:#92400e;">Borrador</span>';
                }

                return `<span style="display:inline-flex;border-radius:999px;background:#e2e8f0;padding:6px 12px;font-size:12px;font-weight:600;color:#334155;">${escapeHtml(String(estatus))}</span>`;
            };

            const buildGalleryHtml = (galeria) => {
                if (!galeria?.length) {
                    return '<p style="margin-top:12px;color:#6b7280;">Sin imagenes de galeria.</p>';
                }

                return `
                    <div style="margin-top:16px;display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:12px;">
                        ${galeria.map((imagen) => `
                            <div style="overflow:hidden;border-radius:16px;border:1px solid #eadde2;background:#fffafc;">
                                <img src="${imagen}" alt="Imagen de galeria" style="height:120px;width:100%;object-fit:cover;">
                            </div>
                        `).join('')}
                    </div>
                `;
            };

            const buildSourcesHtml = (fuentes) => {
                if (!fuentes?.length) {
                    return '<p style="margin-top:12px;color:#6b7280;">Sin fuentes registradas.</p>';
                }

                return `
                    <div style="margin-top:16px;display:grid;gap:12px;">
                        ${fuentes.map((fuente) => `
                            <div style="border-radius:16px;border:1px solid #eadde2;background:#fffdfa;padding:14px;">
                                <p style="margin:0;font-weight:700;color:#201815;">${escapeHtml(fuente.titulo || 'Fuente sin titulo')}</p>
                                ${fuente.descripcion ? `<p style="margin:8px 0 0;color:#5f5257;">${escapeHtml(fuente.descripcion)}</p>` : ''}
                                ${fuente.url ? `<a href="${fuente.url}" target="_blank" rel="noreferrer" style="display:inline-block;margin-top:8px;color:#63102a;font-weight:600;">Abrir fuente</a>` : ''}
                            </div>
                        `).join('')}
                    </div>
                `;
            };

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Operacion completada',
                    text: @json(session('success')),
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#63102a'
                });
            @endif

            $('#tabla-historia').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
                },
                pageLength: 10,
                order: [
                    [2, 'desc']
                ]
            });

            $('.btn-ver-historia').on('click', function() {
                const historiaId = Number(this.dataset.historiaId);
                const historia = historias.find((item) => item.id === historiaId);

                if (!historia) {
                    return;
                }

                const portadaHtml = historia.portada
                    ? `
                        <div style="overflow:hidden;border-radius:24px;background:#2f1821;">
                            <img src="${historia.portada}" alt="${escapeHtml(historia.titulo)}" style="max-height:280px;width:100%;object-fit:cover;">
                        </div>
                    `
                    : '';

                const resumenHtml = historia.resumen_corto
                    ? `<p style="margin:12px 0 0;color:#5f5257;line-height:1.7;">${escapeHtml(historia.resumen_corto)}</p>`
                    : '';

                const desarrolloHtml = historia.desarrollo
                    ? historia.desarrollo
                    : '<p style="color:#6b7280;">Sin desarrollo registrado.</p>';

                Swal.fire({
                    width: 960,
                    showConfirmButton: false,
                    showCloseButton: true,
                    html: `
                        <div class="historia-modal-contenido">
                            ${portadaHtml}
                            <div style="margin-top:20px;">
                                <div style="display:flex;flex-wrap:wrap;align-items:center;gap:12px;justify-content:space-between;">
                                    <h2 style="margin:0;font-size:30px;line-height:1.1;font-weight:800;color:#201815;">${escapeHtml(historia.titulo || 'Sin titulo')}</h2>
                                    ${getStatusBadge(historia.estatus)}
                                </div>
                                <div style="margin-top:12px;display:flex;flex-wrap:wrap;gap:10px;">
                                    <span style="display:inline-flex;border-radius:999px;background:#f8f1e3;padding:6px 12px;font-size:12px;font-weight:600;color:#8d6b10;">${escapeHtml(historia.periodo || 'Sin periodo')}</span>
                                    <span style="display:inline-flex;border-radius:999px;background:#f5f1f3;padding:6px 12px;font-size:12px;font-weight:600;color:#6b5560;">${escapeHtml(historia.fecha_publicacion)}</span>
                                    <span style="display:inline-flex;border-radius:999px;background:#f5f1f3;padding:6px 12px;font-size:12px;font-weight:600;color:#6b5560;">${escapeHtml(historia.autor || 'Sin autor')}</span>
                                </div>
                                ${resumenHtml}
                            </div>

                            <div style="margin-top:24px;border-top:1px solid #efe6dd;padding-top:20px;">
                                <h3 style="margin:0 0 12px;font-size:18px;font-weight:700;color:#201815;">Desarrollo</h3>
                                <div style="color:#3e2d31;line-height:1.8;">${desarrolloHtml}</div>
                            </div>

                            <div style="margin-top:24px;border-top:1px solid #efe6dd;padding-top:20px;">
                                <h3 style="margin:0;font-size:18px;font-weight:700;color:#201815;">Galeria</h3>
                                ${buildGalleryHtml(historia.galeria)}
                            </div>

                            <div style="margin-top:24px;border-top:1px solid #efe6dd;padding-top:20px;">
                                <h3 style="margin:0;font-size:18px;font-weight:700;color:#201815;">Fuentes y referencias</h3>
                                ${buildSourcesHtml(historia.fuentes)}
                            </div>
                        </div>
                    `
                });
            });

            $('.form-toggle-historia').on('submit', function(event) {
                event.preventDefault();

                const form = this;
                const titulo = form.dataset.historiaTitulo || 'este dato historico';
                const actionLabel = form.dataset.actionLabel || 'actualizar';
                const actionTitle = form.dataset.actionTitle || 'Actualizar estatus';
                const actionText = form.dataset.actionText || 'Se actualizara el estatus de';

                Swal.fire({
                    icon: 'warning',
                    title: actionTitle,
                    text: `${actionText} ${titulo}.`,
                    showCancelButton: true,
                    confirmButtonText: `Si, ${actionLabel}`,
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
