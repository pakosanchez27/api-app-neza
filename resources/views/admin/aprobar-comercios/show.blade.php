@extends('layouts.app')
@section('title', 'Detalle de preregistro')
@section('title-section', 'Detalle de preregistro')
@section('description', 'Consulta la solicitud del comercio y realiza la revision desde una pagina dedicada.')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <style>
        .detail-shell {
            border: 1px solid rgba(97, 16, 42, 0.08);
            border-radius: 26px;
            background: #fff;
            box-shadow: 0 18px 40px rgba(97, 18, 50, 0.07);
        }

        .detail-card {
            border: 1px solid rgba(97, 16, 42, 0.08);
            border-radius: 22px;
            background: #fff;
            box-shadow: 0 12px 30px rgba(97, 18, 50, 0.08);
        }

        .detail-card img {
            width: 100%;
            border-radius: 18px;
            background: #f8fafc;
        }
    </style>
@endpush

@section('content')
    @php
        $titular = collect([
            $preregistro->nombre_p,
            $preregistro->app_p,
            $preregistro->apm_p,
        ])->filter()->implode(' ');
        $titular = $titular !== '' ? $titular : 'Sin titular';
        $telefono = $preregistro->telefono ?: 'Sin telefono';
        $correo = $preregistro->correo ?: 'Sin correo';
        $tipo = $preregistro->tipoRelacion?->nombre ?: 'Sin tipo';
        $estado = $preregistro->estatusEtiqueta();
        $descripcion = $preregistro->descripcion_est ?: 'Sin descripcion.';
        $observaciones = $preregistro->observacion_registro ?: 'Sin observaciones.';
        $direccion = trim(implode(', ', array_filter([
            $preregistro->calle,
            $preregistro->numero,
            $preregistro->colonia,
            $preregistro->codigo_postal ? 'CP ' . $preregistro->codigo_postal : null,
        ])));
        $ineUrl = $preregistro->ine ? \App\Support\ImageManager::storageUrl($preregistro->ine) : null;
        $licenciaUrl = $preregistro->lic_fun ? \App\Support\ImageManager::storageUrl($preregistro->lic_fun) : null;
        $fotoUrl = $preregistro->foto_est ? \App\Support\ImageManager::storageUrl($preregistro->foto_est) : null;
        $allowsReview = $preregistro->permiteRevision();
        $estadoClasses = match ((int) ($preregistro->estatus_registro ?? \App\Models\Preregistro::ESTATUS_PENDIENTE)) {
            \App\Models\Preregistro::ESTATUS_PENDIENTE => 'bg-amber-100 text-amber-700',
            \App\Models\Preregistro::ESTATUS_ACEPTADO => 'bg-emerald-100 text-emerald-700',
            \App\Models\Preregistro::ESTATUS_REQUIERE_CORRECCION => 'bg-sky-100 text-sky-700',
            \App\Models\Preregistro::ESTATUS_RECHAZADO_DEFINITIVO => 'bg-rose-100 text-rose-700',
            default => 'bg-slate-200 text-slate-700',
        };
    @endphp

    <div class="space-y-5">
        <div class="detail-shell overflow-hidden">
            <div class="bg-[linear-gradient(135deg,#2f1821,#61102a)] px-6 py-6 text-white">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div class="max-w-3xl">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#ffd175]">Revision de preregistro</p>
                        <h2 class="mt-3 text-2xl font-semibold">{{ $preregistro->nombre_est ?: 'Sin nombre' }}</h2>
                        <p class="mt-2 text-sm leading-7 text-white/78">Folio {{ $preregistro->id_preresgistro }}. Revisa la informacion enviada y decide el siguiente paso.</p>
                    </div>
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('admin.aprobar-comercios') }}"
                            class="inline-flex items-center justify-center rounded-full bg-white/12 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/18">
                            Volver al listado
                        </a>
                    </div>
                </div>
            </div>

            <div class="p-5">
                <div class="grid gap-5 xl:grid-cols-[1.05fr_1fr]">
                    <div class="detail-card p-4 sm:p-5">
                        <p class="text-[13px] uppercase tracking-[0.08em] text-[#6b7280]">Informacion general</p>
                        <dl class="mt-4 grid gap-3 text-[15px] text-[#1f2937] sm:grid-cols-[160px_1fr]">
                            <dt class="font-semibold">Establecimiento</dt>
                            <dd>{{ $preregistro->nombre_est ?: 'Sin nombre' }}</dd>
                            <dt class="font-semibold">Titular</dt>
                            <dd>{{ $titular }}</dd>
                            <dt class="font-semibold">Razon social</dt>
                            <dd>{{ $preregistro->razon_social ?: 'Sin razon social' }}</dd>
                            <dt class="font-semibold">Telefono</dt>
                            <dd>{{ $telefono }}</dd>
                            <dt class="font-semibold">Correo</dt>
                            <dd>{{ $correo }}</dd>
                            <dt class="font-semibold">Tipo</dt>
                            <dd>{{ $tipo }}</dd>
                            <dt class="font-semibold">Estatus</dt>
                            <dd><span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $estadoClasses }}">{{ $estado }}</span></dd>
                            <dt class="font-semibold">Registro</dt>
                            <dd>{{ optional($preregistro->created_at)->format('Y-m-d H:i') ?: 'Sin fecha' }}</dd>
                            <dt class="font-semibold">Latitud</dt>
                            <dd>{{ $preregistro->latitud ?? 'Sin coordenada' }}</dd>
                            <dt class="font-semibold">Longitud</dt>
                            <dd>{{ $preregistro->longitud ?? 'Sin coordenada' }}</dd>
                        </dl>
                    </div>

                    <div class="detail-card p-4 sm:p-5">
                        <p class="text-[13px] uppercase tracking-[0.08em] text-[#6b7280]">Descripcion</p>
                        <p class="mt-4 text-[15px] leading-7 text-[#1f2937]">{{ $descripcion }}</p>
                        <div class="my-5 border-t border-[#e5e7eb]"></div>
                        <p class="text-[13px] uppercase tracking-[0.08em] text-[#6b7280]">Direccion</p>
                        <p class="mt-4 text-[15px] leading-7 text-[#1f2937]">{{ $direccion !== '' ? $direccion : 'Sin direccion registrada.' }}</p>
                        <div class="my-5 border-t border-[#e5e7eb]"></div>
                        <p class="text-[13px] uppercase tracking-[0.08em] text-[#6b7280]">Observaciones de revision</p>
                        <p class="mt-4 text-[15px] leading-7 text-[#1f2937]">{{ $observaciones }}</p>
                    </div>
                </div>

                <div class="detail-card mt-5 p-4 sm:p-5">
                    <p class="text-[13px] uppercase tracking-[0.08em] text-[#6b7280]">Ubicacion en mapa</p>
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
                            @if ($ineUrl)
                                <a href="{{ $ineUrl }}" target="_blank" rel="noopener noreferrer"
                                    class="inline-flex items-center rounded-full border border-[#2563eb] px-3 py-2 text-sm font-medium text-[#2563eb] transition hover:bg-[#eff6ff]">
                                    Abrir archivo
                                </a>
                            @endif
                        </div>
                        <div class="mt-4 rounded-[18px] border border-[#e5e7eb] bg-white p-5 text-[14px] text-[#6b7280]">
                            {{ $ineUrl ? 'Abre el archivo en una nueva pestana para revisarlo.' : 'No hay archivo disponible.' }}
                        </div>
                    </div>

                    <div class="detail-card p-4 sm:p-5">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-[13px] uppercase tracking-[0.08em] text-[#6b7280]">Licencia de funcionamiento</p>
                            @if ($licenciaUrl)
                                <a href="{{ $licenciaUrl }}" target="_blank" rel="noopener noreferrer"
                                    class="inline-flex items-center rounded-full border border-[#2563eb] px-3 py-2 text-sm font-medium text-[#2563eb] transition hover:bg-[#eff6ff]">
                                    Abrir archivo
                                </a>
                            @endif
                        </div>
                        <div class="mt-4 rounded-[18px] border border-[#e5e7eb] bg-white p-5 text-[14px] text-[#6b7280]">
                            {{ $licenciaUrl ? 'Abre el archivo en una nueva pestana para revisarlo.' : 'No hay archivo disponible.' }}
                        </div>
                    </div>
                </div>

                <div class="detail-card mt-5 p-4 sm:p-5">
                    <p class="text-[13px] uppercase tracking-[0.08em] text-[#6b7280]">Fotografia del establecimiento</p>
                    <div class="mt-4 overflow-hidden rounded-[18px] border border-[#e5e7eb] bg-white p-3">
                        @if ($fotoUrl)
                            <img src="{{ $fotoUrl }}" alt="Fotografia del establecimiento" class="max-h-[460px] object-contain">
                        @else
                            <div class="px-5 py-10 text-center text-[14px] text-[#6b7280]">No hay imagen disponible.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if ($allowsReview)
            <div class="detail-shell p-5">
                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <form id="detail-approve-form" method="POST" action="{{ route('admin.aprobar-comercios.approve', $preregistro) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            class="inline-flex items-center justify-center rounded-[10px] bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">
                            Aprobar
                        </button>
                    </form>

                    <form id="detail-correction-form" method="POST" action="{{ route('admin.aprobar-comercios.correction', $preregistro) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="observacion_registro" id="detail-correction-observation" value="">
                        <button type="button" id="detail-correction-button"
                            class="inline-flex items-center justify-center rounded-[10px] bg-sky-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-sky-700">
                            Solicitar correccion
                        </button>
                    </form>

                    <form id="detail-reject-form" method="POST" action="{{ route('admin.aprobar-comercios.reject', $preregistro) }}">
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
        @endif
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = @json(session('success'));
            const errorMessage = @json(session('error'));
            const detailMapElement = document.getElementById('detail-map');
            const detailMapEmpty = document.getElementById('detail-map-empty');
            const detailApproveForm = document.getElementById('detail-approve-form');
            const detailCorrectionForm = document.getElementById('detail-correction-form');
            const detailCorrectionButton = document.getElementById('detail-correction-button');
            const detailCorrectionObservation = document.getElementById('detail-correction-observation');
            const detailRejectForm = document.getElementById('detail-reject-form');
            const detailRejectButton = document.getElementById('detail-reject-button');
            const detailRejectObservation = document.getElementById('detail-reject-observation');
            const parsedLat = Number(@json($preregistro->latitud));
            const parsedLng = Number(@json($preregistro->longitud));
            const hasCoordinates = Number.isFinite(parsedLat) && Number.isFinite(parsedLng);

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

            if (hasCoordinates && detailMapElement && detailMapEmpty) {
                detailMapEmpty.classList.add('hidden');
                detailMapElement.classList.remove('hidden');

                const map = L.map(detailMapElement, {
                    scrollWheelZoom: false
                }).setView([parsedLat, parsedLng], 16);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                L.marker([parsedLat, parsedLng]).addTo(map);

                setTimeout(() => {
                    map.invalidateSize();
                }, 0);
            }

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
                    detailRejectForm.submit();
                });
            });
        });
    </script>
@endpush
