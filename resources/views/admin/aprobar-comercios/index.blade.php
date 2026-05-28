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

        .detail-modal-panel {
            max-height: 92vh;
        }

        .detail-card {
            border: 1px solid rgba(97, 16, 42, 0.08);
            border-radius: 22px;
            background: #fff;
            box-shadow: 0 12px 30px rgba(97, 18, 50, 0.08);
        }

        .detail-card iframe,
        .detail-card img {
            width: 100%;
            border: 0;
            border-radius: 18px;
            background: #f8fafc;
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
                            $payload = [
                                'establecimiento' => $comercio->nombre_est ?: 'Sin nombre',
                                'titular' => $titular,
                                'razon_social' => $comercio->razon_social ?: 'Sin razon social',
                                'telefono' => $telefono,
                                'correo' => $comercio->correo ?: 'Sin correo',
                                'tipo' => $comercio->tipoRelacion?->nombre ?: 'Sin tipo',
                                'estado' => $comercio->estatusEtiqueta(),
                                'descripcion' => $comercio->descripcion_est ?: 'Sin descripcion.',
                                'motivo_rechazo' => $comercio->observacion_registro ?: 'Sin observaciones.',
                                'latitud' => $comercio->latitud,
                                'longitud' => $comercio->longitud,
                                'ine_url' => $comercio->ine ? \App\Support\ImageManager::storageUrl($comercio->ine) : null,
                                'licencia_url' => $comercio->lic_fun ? \App\Support\ImageManager::storageUrl($comercio->lic_fun) : null,
                                'foto_url' => $comercio->foto_est ? \App\Support\ImageManager::storageUrl($comercio->foto_est) : null,
                                'approve_url' => route('admin.aprobar-comercios.approve', $comercio),
                                'correction_url' => route('admin.aprobar-comercios.correction', $comercio),
                                'reject_url' => route('admin.aprobar-comercios.reject', $comercio),
                                'allows_review' => $comercio->permiteRevision(),
                            ];
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
                                    <button type="button"
                                        class="btn-ver-comercio inline-flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-700 transition hover:bg-slate-200"
                                        data-comercio='@json($payload)'
                                        title="Ver detalle" aria-label="Ver detalle del comercio">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.644C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.433 0 .644C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="detail-modal"
        class="fixed inset-0 z-[80] hidden bg-[#190710]/70 p-3 backdrop-blur-[2px] sm:p-5">
        <div class="detail-modal-panel mx-auto flex h-full max-w-7xl flex-col overflow-hidden rounded-[26px] bg-[#f8f6f3] shadow-[0_24px_60px_rgba(0,0,0,0.28)]">
            <div class="flex items-center justify-between bg-[#a41e34] px-5 py-4 text-white">
                <h3 class="text-2xl font-semibold">Detalle del establecimiento</h3>
                <button type="button" id="detail-modal-close"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-full transition hover:bg-white/10"
                    aria-label="Cerrar detalle">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.8" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-4 sm:p-5">
                <div class="grid gap-5 xl:grid-cols-[1.05fr_1fr]">
                    <div class="detail-card p-4 sm:p-5">
                        <p class="text-[13px] uppercase tracking-[0.08em] text-[#6b7280]">Informacion general</p>
                        <dl class="mt-4 grid gap-3 text-[15px] text-[#1f2937] sm:grid-cols-[160px_1fr]">
                            <dt class="font-semibold">Establecimiento</dt>
                            <dd id="detail-establecimiento">-</dd>
                            <dt class="font-semibold">Titular</dt>
                            <dd id="detail-titular">-</dd>
                            <dt class="font-semibold">Razon social</dt>
                            <dd id="detail-razon-social">-</dd>
                            <dt class="font-semibold">Telefono</dt>
                            <dd id="detail-telefono">-</dd>
                            <dt class="font-semibold">Correo</dt>
                            <dd id="detail-correo">-</dd>
                            <dt class="font-semibold">Tipo</dt>
                            <dd id="detail-tipo">-</dd>
                            <dt class="font-semibold">Estatus</dt>
                            <dd><span id="detail-estado-badge" class="inline-flex rounded-full px-3 py-1 text-xs font-semibold">-</span></dd>
                            <dt class="font-semibold">Latitud</dt>
                            <dd id="detail-latitud">-</dd>
                            <dt class="font-semibold">Longitud</dt>
                            <dd id="detail-longitud">-</dd>
                        </dl>
                    </div>

                    <div class="detail-card p-4 sm:p-5">
                        <p class="text-[13px] uppercase tracking-[0.08em] text-[#6b7280]">Descripcion</p>
                        <p id="detail-descripcion" class="mt-4 text-[15px] leading-7 text-[#1f2937]">-</p>
                        <div class="my-5 border-t border-[#e5e7eb]"></div>
                        <p class="text-[13px] uppercase tracking-[0.08em] text-[#6b7280]">Observaciones de revision</p>
                        <p id="detail-motivo" class="mt-4 text-[15px] leading-7 text-[#1f2937]">-</p>
                    </div>
                </div>

                <div class="detail-card mt-5 p-4 sm:p-5">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-[13px] uppercase tracking-[0.08em] text-[#6b7280]">Ubicacion en mapa</p>
                    </div>
                    <div class="mt-4 overflow-hidden rounded-[18px] border border-[#e5e7eb] bg-white">
                        <div id="detail-map" class="hidden h-[340px] w-full"></div>
                        <div id="detail-map-empty" class="px-5 py-10 text-center text-[14px] text-[#6b7280]">
                            No hay coordenadas disponibles para este preregistro.
                        </div>
                    </div>
                </div>

                <div class="mt-5 grid gap-5 xl:grid-cols-2">
                    <div class="detail-card p-4 sm:p-5">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-[13px] uppercase tracking-[0.08em] text-[#6b7280]">INE</p>
                            <a id="detail-ine-link" href="#" target="_blank" rel="noopener noreferrer"
                                class="inline-flex items-center rounded-full border border-[#2563eb] px-3 py-2 text-sm font-medium text-[#2563eb] transition hover:bg-[#eff6ff]">
                                Abrir PDF
                            </a>
                        </div>
                        <div class="mt-4 overflow-hidden rounded-[18px] border border-[#e5e7eb] bg-white p-5">
                            <div id="detail-ine-placeholder" class="text-center text-[14px] text-[#6b7280]">
                                Abre el PDF manualmente para revisarlo.
                            </div>
                        </div>
                    </div>

                    <div class="detail-card p-4 sm:p-5">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-[13px] uppercase tracking-[0.08em] text-[#6b7280]">Licencia de funcionamiento</p>
                            <a id="detail-licencia-link" href="#" target="_blank" rel="noopener noreferrer"
                                class="inline-flex items-center rounded-full border border-[#2563eb] px-3 py-2 text-sm font-medium text-[#2563eb] transition hover:bg-[#eff6ff]">
                                Abrir PDF
                            </a>
                        </div>
                        <div class="mt-4 overflow-hidden rounded-[18px] border border-[#e5e7eb] bg-white p-5">
                            <div id="detail-licencia-placeholder" class="text-center text-[14px] text-[#6b7280]">
                                Abre el PDF manualmente para revisarlo.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="detail-card mt-5 p-4 sm:p-5">
                    <p class="text-[13px] uppercase tracking-[0.08em] text-[#6b7280]">Fotografia del establecimiento</p>
                    <div class="mt-4 overflow-hidden rounded-[18px] border border-[#e5e7eb] bg-white p-3">
                        <img id="detail-foto" src="" alt="Fotografia del establecimiento" class="max-h-[460px] object-contain">
                    </div>
                </div>
            </div>

            <div class="border-t border-[#e5e7eb] bg-white px-5 py-4">
                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <form id="detail-approve-form" method="POST" action="">
                        @csrf
                        @method('PATCH')
                        <button type="submit" id="detail-approve-button"
                            class="inline-flex items-center justify-center rounded-[10px] bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">
                            Aprobar
                        </button>
                    </form>

                    <form id="detail-correction-form" method="POST" action="">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="observacion_registro" id="detail-correction-observation" value="">
                        <button type="button" id="detail-correction-button"
                            class="inline-flex items-center justify-center rounded-[10px] bg-sky-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-sky-700">
                            Solicitar correccion
                        </button>
                    </form>

                    <form id="detail-reject-form" method="POST" action="">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="observacion_registro" id="detail-reject-observation" value="">
                        <button type="button" id="detail-reject-button"
                            class="inline-flex items-center justify-center rounded-[10px] bg-rose-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-rose-700">
                            Rechazar definitivo
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        $(function() {
            const successMessage = @json(session('success'));
            const errorMessage = @json(session('error'));
            const detailModal = document.getElementById('detail-modal');
            const detailModalClose = document.getElementById('detail-modal-close');
            const detailMapElement = document.getElementById('detail-map');
            const detailMapEmpty = document.getElementById('detail-map-empty');
            const detailApproveForm = document.getElementById('detail-approve-form');
            const detailApproveButton = document.getElementById('detail-approve-button');
            const detailCorrectionForm = document.getElementById('detail-correction-form');
            const detailCorrectionButton = document.getElementById('detail-correction-button');
            const detailCorrectionObservation = document.getElementById('detail-correction-observation');
            const detailRejectForm = document.getElementById('detail-reject-form');
            const detailRejectButton = document.getElementById('detail-reject-button');
            const detailRejectObservation = document.getElementById('detail-reject-observation');
            const nezaCenter = [19.4006, -99.0148];

            let detailMap = null;
            let detailMarker = null;

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

            function ensureDetailMap() {
                if (detailMap) {
                    return detailMap;
                }

                detailMap = L.map(detailMapElement, {
                    scrollWheelZoom: false
                }).setView(nezaCenter, 14);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(detailMap);

                return detailMap;
            }

            function updateDetailMap(lat, lng) {
                const parsedLat = Number(lat);
                const parsedLng = Number(lng);
                const hasCoordinates = Number.isFinite(parsedLat) && Number.isFinite(parsedLng);

                if (!hasCoordinates) {
                    detailMapElement.classList.add('hidden');
                    detailMapEmpty.classList.remove('hidden');
                    return;
                }

                detailMapEmpty.classList.add('hidden');
                detailMapElement.classList.remove('hidden');

                const mapInstance = ensureDetailMap();
                const nextPosition = [parsedLat, parsedLng];

                if (!detailMarker) {
                    detailMarker = L.marker(nextPosition).addTo(mapInstance);
                } else {
                    detailMarker.setLatLng(nextPosition);
                }

                mapInstance.setView(nextPosition, 16, {
                    animate: true
                });

                setTimeout(() => {
                    mapInstance.invalidateSize();
                }, 0);
            }

            function setDocumentPreview(placeholderId, linkId, url) {
                const placeholder = document.getElementById(placeholderId);
                const link = document.getElementById(linkId);

                if (!url) {
                    placeholder.textContent = 'No hay archivo disponible.';
                    placeholder.classList.remove('hidden');
                    link.classList.add('hidden');
                    return;
                }

                placeholder.textContent = 'Abre el PDF manualmente para revisarlo.';
                placeholder.classList.remove('hidden');
                link.setAttribute('href', url);
                link.classList.remove('hidden');
            }

            function openDetailModal(payload) {
                document.getElementById('detail-establecimiento').textContent = payload.establecimiento || '-';
                document.getElementById('detail-titular').textContent = payload.titular || '-';
                document.getElementById('detail-razon-social').textContent = payload.razon_social || '-';
                document.getElementById('detail-telefono').textContent = payload.telefono || '-';
                document.getElementById('detail-correo').textContent = payload.correo || '-';
                document.getElementById('detail-tipo').textContent = payload.tipo || '-';
                document.getElementById('detail-descripcion').textContent = payload.descripcion || '-';
                document.getElementById('detail-motivo').textContent = payload.motivo_rechazo || '-';
                document.getElementById('detail-latitud').textContent = payload.latitud ?? '-';
                document.getElementById('detail-longitud').textContent = payload.longitud ?? '-';

                const estadoBadge = document.getElementById('detail-estado-badge');
                estadoBadge.textContent = payload.estado || '-';
                estadoBadge.className = payload.estado === 'Pendiente'
                    ? 'inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700'
                    : (payload.estado === 'Requiere correccion'
                        ? 'inline-flex rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700'
                        : (payload.estado === 'Rechazado definitivo'
                            ? 'inline-flex rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700'
                            : 'inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700'));

                setDocumentPreview('detail-ine-placeholder', 'detail-ine-link', payload.ine_url);
                setDocumentPreview('detail-licencia-placeholder', 'detail-licencia-link', payload.licencia_url);

                const foto = document.getElementById('detail-foto');
                if (payload.foto_url) {
                    foto.setAttribute('src', payload.foto_url);
                    foto.classList.remove('hidden');
                } else {
                    foto.setAttribute('src', '');
                    foto.classList.add('hidden');
                }

                detailApproveForm.setAttribute('action', payload.approve_url || '');
                detailCorrectionForm.setAttribute('action', payload.correction_url || '');
                detailRejectForm.setAttribute('action', payload.reject_url || '');
                detailCorrectionObservation.value = '';
                detailRejectObservation.value = '';

                if (payload.allows_review) {
                    detailApproveForm.classList.remove('hidden');
                    detailCorrectionForm.classList.remove('hidden');
                    detailRejectForm.classList.remove('hidden');
                    detailApproveButton.disabled = false;
                    detailCorrectionButton.disabled = false;
                    detailRejectButton.disabled = false;
                } else {
                    detailApproveForm.classList.add('hidden');
                    detailCorrectionForm.classList.add('hidden');
                    detailRejectForm.classList.add('hidden');
                }

                updateDetailMap(payload.latitud, payload.longitud);

                detailModal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function closeDetailModal() {
                detailModal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            $('.btn-ver-comercio').on('click', function() {
                const payload = this.dataset.comercio ? JSON.parse(this.dataset.comercio) : null;

                if (!payload) {
                    return;
                }

                openDetailModal(payload);
            });

            detailModalClose?.addEventListener('click', closeDetailModal);
            detailModal?.addEventListener('click', function(event) {
                if (event.target === detailModal) {
                    closeDetailModal();
                }
            });

            detailApproveForm?.addEventListener('submit', function(event) {
                event.preventDefault();

                Swal.fire({
                    icon: 'question',
                    title: 'Aprobar comercio',
                    text: 'El preregistro cambiara a estado aceptado.',
                    showCancelButton: true,
                    confirmButtonText: 'Si, aprobar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#047857',
                    cancelButtonColor: '#64748b'
                }).then((result) => {
                    if (result.isConfirmed) {
                        detailApproveForm.submit();
                    }
                });
            });

            detailCorrectionButton?.addEventListener('click', function() {
                Swal.fire({
                    icon: 'info',
                    title: 'Solicitar correccion',
                    input: 'textarea',
                    inputLabel: 'Indicaciones para el comercio',
                    inputPlaceholder: 'Escribe que necesita corregir el solicitante...',
                    inputAttributes: {
                        'aria-label': 'Indicaciones para el comercio'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Enviar correccion',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#0284c7',
                    cancelButtonColor: '#64748b',
                    inputValidator: (value) => {
                        if (!value || !value.trim()) {
                            return 'Debes escribir las indicaciones de correccion.';
                        }

                        return null;
                    }
                }).then((result) => {
                    if (!result.isConfirmed) {
                        return;
                    }

                    detailCorrectionObservation.value = result.value.trim();
                    Swal.fire({
                        title: 'Enviando correccion...',
                        text: 'Espera un momento mientras procesamos la solicitud.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    detailCorrectionForm.submit();
                });
            });

            detailRejectButton?.addEventListener('click', function() {
                Swal.fire({
                    icon: 'warning',
                    title: 'Rechazar definitivamente',
                    input: 'textarea',
                    inputLabel: 'Motivo del rechazo definitivo',
                    inputPlaceholder: 'Escribe el motivo del rechazo definitivo...',
                    inputAttributes: {
                        'aria-label': 'Motivo del rechazo definitivo'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Guardar rechazo definitivo',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#be123c',
                    cancelButtonColor: '#64748b',
                    showLoaderOnConfirm: true,
                    allowOutsideClick: () => !Swal.isLoading(),
                    inputValidator: (value) => {
                        if (!value || !value.trim()) {
                            return 'Debes escribir un motivo de rechazo definitivo.';
                        }

                        return null;
                    }
                }).then((result) => {
                    if (!result.isConfirmed) {
                        return;
                    }

                    detailRejectObservation.value = result.value.trim();
                    Swal.fire({
                        title: 'Guardando rechazo definitivo...',
                        text: 'Espera un momento mientras procesamos la solicitud.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    detailRejectForm.submit();
                });
            });
        });
    </script>
@endpush
