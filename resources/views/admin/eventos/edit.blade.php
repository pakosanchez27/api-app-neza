@extends('layouts.app')
@section('title', 'Eventos')
@section('title-section', 'Editar Evento')
@section('description', 'Actualiza la información del evento turístico y ajusta su ubicación, portada y configuración de publicación.')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
@endpush

@section('content')
    <div class="w-full rounded-[24px] bg-white p-6 shadow-[0_24px_60px_rgba(32,24,21,0.12)]">
        <div class="mb-6 flex items-start justify-between gap-4 border-b border-[#efe6dd] pb-4">
            <div>
                <h2 class="text-xl font-semibold text-[#201815]">Editar Evento</h2>
                <p class="mt-1 text-sm text-[#7d6870]">Modifica los datos del evento y conserva la consistencia del destacado actual.</p>
            </div>
            <a href="{{ route('admin.eventos') }}"
                class="inline-flex items-center justify-center rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">
                Regresar
            </a>
        </div>

        <form class="space-y-6" enctype="multipart/form-data" method="POST"
            action="{{ route('admin.eventos.update', $evento) }}" id="form-editar-evento">
            @csrf
            @method('PUT')
            <input type="hidden" name="force_change_destacado" id="force_change_destacado"
                value="{{ old('force_change_destacado', '0') }}">

            @if ($errors->any())
                <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    Revisa los campos marcados para continuar.
                </div>
            @endif

            <div class="grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label for="titulo" class="mb-1 block text-sm font-medium text-[#3e2d31]">Título</label>
                    <input type="text" id="titulo" name="titulo" value="{{ old('titulo', $evento->titulo) }}"
                        class="w-full rounded-2xl border {{ $errors->has('titulo') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    @error('titulo')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="portada" class="mb-1 block text-sm font-medium text-[#3e2d31]">Foto de portada</label>
                    <input type="file" id="portada" name="portada" accept="image/*"
                        class="w-full rounded-2xl border {{ $errors->has('portada') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition file:mr-4 file:rounded-full file:border-0 file:bg-[#63102a] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-[#7f173c] focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    @error('portada')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                    <div id="portada-preview-wrapper"
                        class="mt-4 overflow-hidden rounded-[24px] border border-[#eadde2] bg-[#fffafc] {{ old('portada') || $evento->portada ? '' : 'hidden' }}">
                        <img id="portada-preview"
                            src="{{ old('portada') ? '' : ($evento->portada ? \App\Support\ImageManager::publicUrl($evento->portada) : '') }}"
                            alt="Vista previa de portada" class="h-[240px] w-full object-cover">
                    </div>
                    <p id="portada-preview-empty"
                        class="mt-3 text-[13px] leading-6 text-[#6f6166] {{ old('portada') || $evento->portada ? 'hidden' : '' }}">
                        Selecciona una imagen para ver la vista previa de la portada.
                    </p>
                </div>

                <div>
                    <label for="fecha" class="mb-1 block text-sm font-medium text-[#3e2d31]">Fecha</label>
                    <input type="date" id="fecha" name="fecha" value="{{ old('fecha', $evento->fecha) }}"
                        class="w-full rounded-2xl border {{ $errors->has('fecha') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    @error('fecha')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="hora" class="mb-1 block text-sm font-medium text-[#3e2d31]">Hora</label>
                    <input type="time" id="hora" name="hora"
                        value="{{ old('hora', \Carbon\Carbon::parse($evento->hora)->format('H:i')) }}"
                        class="w-full rounded-2xl border {{ $errors->has('hora') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    @error('hora')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="calle" class="mb-1 block text-sm font-medium text-[#3e2d31]">Calle</label>
                    <input type="text" id="calle" name="calle" list="calles-sugeridas"
                        value="{{ old('calle', $evento->calle) }}"
                        class="w-full rounded-2xl border {{ $errors->has('calle') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    <datalist id="calles-sugeridas"></datalist>
                    @error('calle')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="numero" class="mb-1 block text-sm font-medium text-[#3e2d31]">Número</label>
                    <input type="text" id="numero" name="numero" value="{{ old('numero', $evento->numero) }}"
                        class="w-full rounded-2xl border {{ $errors->has('numero') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    @error('numero')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="col" class="mb-1 block text-sm font-medium text-[#3e2d31]">Colonia</label>
                    <input type="text" id="col" name="col" value="{{ old('col', $evento->colonia) }}"
                        class="w-full rounded-2xl border {{ $errors->has('col') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    @error('col')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="latitud" class="mb-1 block text-sm font-medium text-[#3e2d31]">Latitud</label>
                    <input type="number" step="0.00000001" id="latitud" name="latitud" readonly
                        value="{{ old('latitud', $evento->latitud) }}"
                        class="w-full rounded-2xl border {{ $errors->has('latitud') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#f8f3ef]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    @error('latitud')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="longitud" class="mb-1 block text-sm font-medium text-[#3e2d31]">Longitud</label>
                    <input type="number" step="0.00000001" id="longitud" name="longitud" readonly
                        value="{{ old('longitud', $evento->longitud) }}"
                        class="w-full rounded-2xl border {{ $errors->has('longitud') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#f8f3ef]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    @error('longitud')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <p id="coordenadas-status" class="text-[13px] leading-6 text-[#6f6166]">
                        Ingresa calle y número para completar las coordenadas automáticamente.
                    </p>
                </div>

                <div class="md:col-span-2">
                    <div class="overflow-hidden rounded-[24px] border border-[#eadde2] bg-[#fffafc]">
                        <div id="mapa-evento" class="h-[320px] w-full"></div>
                        <div id="mapa-empty" class="px-5 py-8 text-center text-[14px] leading-7 text-[#6f6166]">
                            Agrega una calle y un número válidos para visualizar la ubicación del evento.
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label for="acerca" class="mb-1 block text-sm font-medium text-[#3e2d31]">Acerca del Evento</label>
                    <textarea id="acerca" name="acerca" rows="4"
                        class="w-full rounded-2xl border {{ $errors->has('acerca') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">{{ old('acerca', $evento->acerca) }}</textarea>
                    @error('acerca')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="estatus" class="mb-1 block text-sm font-medium text-[#3e2d31]">Estatus</label>
                    <select id="estatus" name="estatus"
                        class="w-full rounded-2xl border {{ $errors->has('estatus') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                        <option value="1" @selected(old('estatus', (string) $evento->estatus) == '1')>Activo</option>
                        <option value="0" @selected(old('estatus', (string) $evento->estatus) == '0')>Inactivo</option>
                        <option value="2" @selected(old('estatus', (string) $evento->estatus) == '2')>Vencido</option>
                    </select>
                    @error('estatus')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="id_categoria" class="mb-1 block text-sm font-medium text-[#3e2d31]">Categoría</label>
                    <select id="id_categoria" name="id_categoria"
                        class="w-full rounded-2xl border {{ $errors->has('id_categoria') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                        <option value="">Selecciona una categoría</option>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria['id'] }}"
                                @selected(old('id_categoria', $evento->category_id) == $categoria['id'])>
                                {{ $categoria['nombre'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_categoria')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="flex items-center gap-3 rounded-2xl border border-[#e8d9cb] bg-[#fffdfa] px-4 py-3 text-sm text-[#201815]">
                        <input type="checkbox" name="is_destacado" value="1" id="is_destacado"
                            @checked(old('is_destacado', $evento->is_destacado))
                            class="h-4 w-4 rounded border-[#d6c3b1] text-[#63102a] focus:ring-[#63102a]/30">
                        <span>Marcar como destacado (solo un evento puede ser el destacado)</span>
                    </label>
                    @if ($eventoDestacadoActual)
                        <p class="mt-1 text-sm text-amber-700">
                            Evento destacado actual: {{ $eventoDestacadoActual->titulo }}
                        </p>
                    @endif
                    @error('is_destacado')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-[#efe6dd] pt-4 sm:flex-row sm:justify-end">
                <a href="{{ route('admin.eventos') }}"
                    class="inline-flex items-center justify-center rounded-full bg-slate-100 px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">
                    Cancelar
                </a>
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-full bg-[#63102a] px-5 py-2.5 text-sm font-semibold text-white shadow-[0_10px_24px_rgba(99,16,42,0.22)] transition hover:bg-[#7f173c]">
                    Actualizar Evento
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.11.0/proj4.min.js"
        integrity="sha512-gMZ92sl9n4l4aCqvPU4jMK8v6QNCj20lA4kHU9AEPBaclSJNfVx5A6MDE9K9oNw1b8NPks8v3nZSxv0ypUj4hw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const coordenadasUrl = @json(asset('data/coordenadas.json'));
            const nezaCenter = [19.4006, -99.0148];
            const eventoDestacadoActual = @json($eventoDestacadoActual?->titulo);
            const formEditarEvento = document.getElementById('form-editar-evento');
            const destacadoInput = document.getElementById('is_destacado');
            const forceChangeDestacadoInput = document.getElementById('force_change_destacado');
            const portadaInput = document.getElementById('portada');
            const portadaPreview = document.getElementById('portada-preview');
            const portadaPreviewWrapper = document.getElementById('portada-preview-wrapper');
            const portadaPreviewEmpty = document.getElementById('portada-preview-empty');
            const calleInput = document.getElementById('calle');
            const numeroInput = document.getElementById('numero');
            const coloniaInput = document.getElementById('col');
            const latitudInput = document.getElementById('latitud');
            const longitudInput = document.getElementById('longitud');
            const callesDatalist = document.getElementById('calles-sugeridas');
            const coordenadasStatus = document.getElementById('coordenadas-status');
            const mapaEmpty = document.getElementById('mapa-empty');
            const mapElement = document.getElementById('mapa-evento');

            let coverPreviewUrl = null;
            let coordinateIndex = null;
            let map = null;
            let marker = null;

            const markerIcon = L.divIcon({
                className: '',
                html: `
                    <div style="
                        width: 24px;
                        height: 24px;
                        border-radius: 9999px;
                        background: #611232;
                        border: 4px solid #ffffff;
                        box-shadow: 0 10px 24px rgba(97,18,50,0.3);
                    "></div>
                `,
                iconSize: [24, 24],
                iconAnchor: [12, 12],
            });

            if (typeof proj4 !== 'undefined') {
                proj4.defs('EPSG:6372',
                    '+proj=lcc +lat_0=12 +lon_0=-102 +lat_1=17.5 +lat_2=29.5 +x_0=2500000 +y_0=0 +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +units=m +no_defs +type=crs'
                );
            }

            function normalizeCoordinateKeyPart(value) {
                return String(value ?? '')
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .trim()
                    .replace(/\s+/g, ' ')
                    .toUpperCase();
            }

            function toCoordinateValue(value) {
                if (value === null || value === undefined || value === '') {
                    return '';
                }

                const parsed = Number(value);
                return Number.isFinite(parsed) ? parsed : '';
            }

            function buildCoordinateLookupKey(street, exteriorNumber) {
                return `${normalizeCoordinateKeyPart(street)}|${normalizeCoordinateKeyPart(exteriorNumber)}`;
            }

            function isValidCoordinatePair(lat, lng) {
                return Number.isFinite(lat) && Number.isFinite(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180;
            }

            function resolveFeatureCoordinates(feature) {
                const props = feature?.properties ?? {};
                const geometryCoordinates = Array.isArray(feature?.geometry?.coordinates) ? feature.geometry.coordinates : [];

                const propertyLat = props.lattud ?? props.latitud ?? props.LATITUD ?? '';
                const propertyLng = props.longitud ?? props.LONGITUD ?? '';
                const geometryLng = geometryCoordinates[0] ?? '';
                const geometryLat = geometryCoordinates[1] ?? '';

                const latCandidate = toCoordinateValue(propertyLat);
                const lngCandidate = toCoordinateValue(propertyLng);

                if (isValidCoordinatePair(latCandidate, lngCandidate)) {
                    return {
                        latitud: latCandidate,
                        longitud: lngCandidate,
                    };
                }

                if (typeof proj4 !== 'undefined' && Number.isFinite(latCandidate) && Number.isFinite(lngCandidate)) {
                    try {
                        const [convertedLng, convertedLat] = proj4('EPSG:6372', 'EPSG:4326', [lngCandidate, latCandidate]);

                        if (isValidCoordinatePair(convertedLat, convertedLng)) {
                            return {
                                latitud: convertedLat,
                                longitud: convertedLng,
                            };
                        }
                    } catch (error) {
                    }
                }

                const geometryLatCandidate = toCoordinateValue(geometryLat);
                const geometryLngCandidate = toCoordinateValue(geometryLng);

                if (isValidCoordinatePair(geometryLatCandidate, geometryLngCandidate)) {
                    return {
                        latitud: geometryLatCandidate,
                        longitud: geometryLngCandidate,
                    };
                }

                return {
                    latitud: latCandidate,
                    longitud: lngCandidate,
                };
            }

            function ensureMap() {
                if (map) {
                    return map;
                }

                map = L.map(mapElement, {
                    scrollWheelZoom: false
                }).setView(nezaCenter, 14);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                return map;
            }

            function updateMap(lat, lng) {
                const parsedLat = Number(lat);
                const parsedLng = Number(lng);
                const hasCoordinates = isValidCoordinatePair(parsedLat, parsedLng);

                if (!hasCoordinates) {
                    mapElement.classList.add('hidden');
                    mapaEmpty.classList.remove('hidden');
                    return;
                }

                mapaEmpty.classList.add('hidden');
                mapElement.classList.remove('hidden');

                const mapInstance = ensureMap();
                const nextPosition = [parsedLat, parsedLng];

                if (!marker) {
                    marker = L.marker(nextPosition, {
                        icon: markerIcon
                    }).addTo(mapInstance);
                } else {
                    marker.setLatLng(nextPosition);
                }

                mapInstance.setView(nextPosition, 16, {
                    animate: true
                });

                setTimeout(() => {
                    mapInstance.invalidateSize();
                }, 0);
            }

            function setCoordinateFields(match) {
                coloniaInput.value = match?.colonia != null ? String(match.colonia) : '';
                latitudInput.value = match?.latitud != null ? String(match.latitud) : '';
                longitudInput.value = match?.longitud != null ? String(match.longitud) : '';
                updateMap(latitudInput.value, longitudInput.value);
            }

            async function loadCoordinateDataset() {
                const response = await fetch(coordenadasUrl);

                if (!response.ok) {
                    throw new Error('No fue posible cargar el archivo de coordenadas.');
                }

                return response.json();
            }

            async function loadCoordinateIndex() {
                if (coordinateIndex) {
                    return coordinateIndex;
                }

                const data = await loadCoordinateDataset();
                const index = new Map();
                const streets = new Set();

                for (const feature of data?.features ?? []) {
                    const props = feature?.properties ?? {};
                    const street = String(props.NOMVIAL ?? '').trim();
                    const number = String(props.NUMEXT ?? '').trim();
                    const key = buildCoordinateLookupKey(street, number);
                    const coordinates = resolveFeatureCoordinates(feature);

                    if (street) {
                        streets.add(street);
                    }

                    if (!key || key === '|') {
                        continue;
                    }

                    if (!index.has(key)) {
                        index.set(key, {
                            colonia: props.NOMBRE_CUA ?? props.nombre_cua ?? '',
                            latitud: coordinates.latitud,
                            longitud: coordinates.longitud,
                        });
                    }
                }

                coordinateIndex = index;

                const streetOptions = Array.from(streets).sort((a, b) => a.localeCompare(b, 'es'));
                callesDatalist.innerHTML = streetOptions.map((street) => `<option value="${street.replace(/"/g, '&quot;')}"></option>`).join('');

                return coordinateIndex;
            }

            async function resolveCoordinates() {
                const street = calleInput.value.trim();
                const number = numeroInput.value.trim();

                if (!street || !number) {
                    setCoordinateFields(null);
                    coordenadasStatus.textContent = 'Ingresa calle y número para completar las coordenadas automáticamente.';
                    return;
                }

                coordenadasStatus.textContent = 'Buscando coordenadas con la calle y el número...';

                try {
                    const index = await loadCoordinateIndex();
                    const match = index.get(buildCoordinateLookupKey(street, number));

                    setCoordinateFields(match);

                    if (match) {
                        coordenadasStatus.textContent = 'Coordenadas rellenadas automáticamente desde coordenadas.json.';
                    } else {
                        coordenadasStatus.textContent = 'No se encontró una coincidencia para esa calle y número.';
                    }
                } catch (error) {
                    setCoordinateFields(null);
                    coordenadasStatus.textContent = 'No fue posible cargar el catálogo de coordenadas.';
                }
            }

            portadaInput?.addEventListener('change', function(event) {
                const file = event.target.files?.[0];

                if (coverPreviewUrl) {
                    URL.revokeObjectURL(coverPreviewUrl);
                    coverPreviewUrl = null;
                }

                if (!file) {
                    if (!portadaPreview.getAttribute('src')) {
                        portadaPreviewWrapper.classList.add('hidden');
                        portadaPreviewEmpty.classList.remove('hidden');
                    }
                    return;
                }

                coverPreviewUrl = URL.createObjectURL(file);
                portadaPreview.src = coverPreviewUrl;
                portadaPreviewWrapper.classList.remove('hidden');
                portadaPreviewEmpty.classList.add('hidden');
            });

            calleInput?.addEventListener('input', resolveCoordinates);
            numeroInput?.addEventListener('input', resolveCoordinates);

            formEditarEvento?.addEventListener('submit', function(event) {
                const debeConfirmarCambioDestacado = destacadoInput?.checked && eventoDestacadoActual && forceChangeDestacadoInput?.value !== '1';

                if (!debeConfirmarCambioDestacado) {
                    return;
                }

                event.preventDefault();

                Swal.fire({
                    icon: 'warning',
                    title: 'Cambiar evento destacado',
                    text: `Actualmente "${eventoDestacadoActual}" está marcado como destacado. ¿Seguro que deseas reemplazarlo por este evento?`,
                    showCancelButton: true,
                    confirmButtonText: 'Sí, cambiar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#63102a',
                    cancelButtonColor: '#94a3b8'
                }).then((result) => {
                    if (!result.isConfirmed) {
                        return;
                    }

                    forceChangeDestacadoInput.value = '1';
                    formEditarEvento.submit();
                });
            });

            loadCoordinateIndex().catch(function() {
                coordenadasStatus.textContent = 'No fue posible cargar el catálogo de coordenadas.';
            });

            updateMap(latitudInput.value, longitudInput.value);
        });
    </script>
@endpush
