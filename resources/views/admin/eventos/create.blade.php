@extends('layouts.app')
@section('title', 'Eventos')
@section('title-section', 'Crear Evento')
@section('description', 'Completa la información del evento turístico y ubícalo en el mapa con apoyo del catálogo de coordenadas.')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
@endpush

@section('content')
    <div class="w-full rounded-[24px] bg-white p-6 shadow-[0_24px_60px_rgba(32,24,21,0.12)]">
        <div class="mb-6 flex items-start justify-between gap-4 border-b border-[#efe6dd] pb-4">
            <div>
                <h2 class="text-xl font-semibold text-[#201815]">Crear Evento</h2>
                <p class="mt-1 text-sm text-[#7d6870]">Usa la calle y el número para completar las coordenadas automáticamente.</p>
            </div>
            <a href="{{ route('admin.eventos') }}"
                class="inline-flex items-center justify-center rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">
                Regresar
            </a>
        </div>

        <form class="space-y-6" enctype="multipart/form-data" method="POST" action="{{ route('admin.eventos.store') }}"
            id="form-crear-evento">
            @csrf
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
                    <input type="text" id="titulo" name="titulo" value="{{ old('titulo') }}"
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
                        class="mt-4 hidden overflow-hidden rounded-[24px] border border-[#eadde2] bg-[#fffafc]">
                        <img id="portada-preview" src="" alt="Vista previa de portada"
                            class="h-[240px] w-full object-cover">
                    </div>
                    <p id="portada-preview-empty" class="mt-3 text-[13px] leading-6 text-[#6f6166]">
                        Selecciona una imagen para ver la vista previa de la portada.
                    </p>
                </div>

                <div>
                    <label for="fecha" class="mb-1 block text-sm font-medium text-[#3e2d31]">Fecha</label>
                    <input type="date" id="fecha" name="fecha" value="{{ old('fecha') }}"
                        class="w-full rounded-2xl border {{ $errors->has('fecha') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    @error('fecha')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="hora" class="mb-1 block text-sm font-medium text-[#3e2d31]">Hora</label>
                    <input type="time" id="hora" name="hora" value="{{ old('hora') }}"
                        class="w-full rounded-2xl border {{ $errors->has('hora') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    @error('hora')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="nom_lugar" class="mb-1 block text-sm font-medium text-[#3e2d31]">Recinto existente</label>
                    <select id="nom_lugar" name="nom_lugar"
                        class="w-full rounded-2xl border {{ $errors->has('nom_lugar') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                        <option value="">Selecciona un recinto</option>
                        <option value="155499" @selected(old('nom_lugar') == '155499')>ESTADIO NEZA 86</option>
                        <option value="19054" @selected(old('nom_lugar') == '19054')>EXPLANADA PALACIO MUNICIPAL</option>
                        <option value="77543" @selected(old('nom_lugar') == '77543')>CENTRO PLURICULTURAL EMILIANO ZAPATA</option>
                        <option value="79599" @selected(old('nom_lugar') == '79599')>PARQUE DEL PUEBLO</option>
                        <option value="84429" @selected(old('nom_lugar') == '84429')>PLAZA CIUDAD JARDÍN</option>
                        <option value="96940" @selected(old('nom_lugar') == '96940')>AUDITORIO ALFREDO DEL MAZO</option>
                    </select>
                    <p class="mt-1 text-[13px] leading-6 text-[#6f6166]">
                        Si no se muestra el establecimiento que buscas, puedes dejar este campo vacío e ingresar la calle y número para completar las coordenadas automáticamente.
                    </p>
                    @error('nom_lugar')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="calle" class="mb-1 block text-sm font-medium text-[#3e2d31]">Calle</label>
                    <input type="text" id="calle" name="calle" list="calles-sugeridas" value="{{ old('calle') }}"
                        class="w-full rounded-2xl border {{ $errors->has('calle') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    <datalist id="calles-sugeridas"></datalist>
                    @error('calle')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="numero" class="mb-1 block text-sm font-medium text-[#3e2d31]">Número</label>
                    <input type="text" id="numero" name="numero" value="{{ old('numero') }}"
                        class="w-full rounded-2xl border {{ $errors->has('numero') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    @error('numero')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="col" class="mb-1 block text-sm font-medium text-[#3e2d31]">Colonia</label>
                    <input type="text" id="col" name="col" value="{{ old('col') }}"
                        class="w-full rounded-2xl border {{ $errors->has('col') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    @error('col')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="latitud" class="mb-1 block text-sm font-medium text-[#3e2d31]">Latitud</label>
                    <input type="number" step="0.00000001" id="latitud" name="latitud" readonly value="{{ old('latitud') }}"
                        class="w-full rounded-2xl border {{ $errors->has('latitud') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#f8f3ef]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    @error('latitud')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="longitud" class="mb-1 block text-sm font-medium text-[#3e2d31]">Longitud</label>
                    <input type="number" step="0.00000001" id="longitud" name="longitud" readonly value="{{ old('longitud') }}"
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
                        class="w-full rounded-2xl border {{ $errors->has('acerca') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">{{ old('acerca') }}</textarea>
                    @error('acerca')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="estatus" class="mb-1 block text-sm font-medium text-[#3e2d31]">Estatus</label>
                    <select id="estatus" name="estatus"
                        class="w-full rounded-2xl border {{ $errors->has('estatus') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                        <option value="1" @selected(old('estatus', '1') == '1')>Activo</option>
                        <option value="0" @selected(old('estatus') == '0')>Inactivo</option>
                        <option value="2" @selected(old('estatus') == '2')>Vencido</option>
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
                            <option value="{{ $categoria['id'] }}" @selected(old('id_categoria') == $categoria['id'])>{{ $categoria['nombre'] }}</option>
                        @endforeach
                    </select>
                    @error('id_categoria')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="flex items-center gap-3 rounded-2xl border border-[#e8d9cb] bg-[#fffdfa] px-4 py-3 text-sm text-[#201815]">
                        <input type="checkbox" name="is_destacado" value="1" id="is_destacado"
                            @checked(old('is_destacado'))
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
                    Guardar Evento
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const recintosEventosUrl = @json(asset('data/recintos-eventos.json'));
            const callesUrl = @json(asset('data/calles-catalogo.json'));
            const direccionesLookupUrl = @json(asset('data/direcciones-lookup.json'));
            const nezaCenter = [19.4006, -99.0148];
            const eventoDestacadoActual = @json($eventoDestacadoActual?->titulo);
            const formCrearEvento = document.getElementById('form-crear-evento');
            const destacadoInput = document.getElementById('is_destacado');
            const forceChangeDestacadoInput = document.getElementById('force_change_destacado');
            const portadaInput = document.getElementById('portada');
            const portadaPreview = document.getElementById('portada-preview');
            const portadaPreviewWrapper = document.getElementById('portada-preview-wrapper');
            const portadaPreviewEmpty = document.getElementById('portada-preview-empty');
            const nomLugarSelect = document.getElementById('nom_lugar');
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
            let callesLoaded = false;
            let recintosById = null;
            let direccionesLookup = null;
            let map = null;
            let marker = null;
            let isApplyingRecintoSelection = false;

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

            function buildCoordinateLookupKey(street, exteriorNumber) {
                return `${normalizeCoordinateKeyPart(street)}|${normalizeCoordinateKeyPart(exteriorNumber)}`;
            }

            function normalizeCoordinateKeyPart(value) {
                return String(value ?? '')
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .trim()
                    .replace(/\s+/g, ' ')
                    .toUpperCase();
            }

            function isValidCoordinatePair(lat, lng) {
                return Number.isFinite(lat) && Number.isFinite(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180;
            }

            function decodeText(value) {
                const text = String(value ?? '').trim();

                if (!text) {
                    return '';
                }

                try {
                    return decodeURIComponent(escape(text));
                } catch (error) {
                    return text;
                }
            }

            async function fetchJson(url) {
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (!response.ok) {
                    throw new Error('No fue posible completar la solicitud.');
                }

                return response.json();
            }

            async function loadRecintosById() {
                if (recintosById) {
                    return recintosById;
                }

                const payload = await fetchJson(recintosEventosUrl);
                recintosById = Array.isArray(payload)
                    ? payload.reduce((index, item) => {
                        if (item?.identifica) {
                            index[String(item.identifica)] = item;
                        }
                        return index;
                    }, {})
                    : {};

                return recintosById;
            }

            async function loadDireccionesLookup() {
                if (direccionesLookup) {
                    return direccionesLookup;
                }

                const payload = await fetchJson(direccionesLookupUrl);
                direccionesLookup = payload && typeof payload === 'object' ? payload : {};

                return direccionesLookup;
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

            function setLocationFields(match) {
                calleInput.value = match?.calle != null ? String(match.calle) : '';
                numeroInput.value = match?.numero != null ? String(match.numero) : '';
                setCoordinateFields(match);
            }

            async function loadStreetSuggestions() {
                if (callesLoaded) {
                    return;
                }

                const payload = await fetchJson(callesUrl);
                const streetOptions = Array.isArray(payload)
                    ? payload
                    : (Array.isArray(payload?.calles) ? payload.calles : []);

                callesDatalist.innerHTML = streetOptions.map((street) => {
                    const safeStreet = String(street).replace(/"/g, '&quot;');
                    return `<option value="${safeStreet}"></option>`;
                }).join('');

                callesLoaded = true;
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
                    const direcciones = await loadDireccionesLookup();
                    const lookupKey = buildCoordinateLookupKey(street, number);
                    const match = direcciones[lookupKey] ?? null;

                    if (match) {
                        setLocationFields({
                            calle: decodeText(match.calle),
                            numero: decodeText(match.numero),
                            colonia: decodeText(match.colonia),
                            latitud: match.latitud,
                            longitud: match.longitud,
                        });
                    } else {
                        setCoordinateFields(null);
                    }

                    if (match) {
                        coordenadasStatus.textContent = 'Ubicación rellenada automáticamente desde coordenadas.json.';
                    } else {
                        coordenadasStatus.textContent = 'No se encontró una coincidencia para esa calle y número.';
                    }
                } catch (error) {
                    setCoordinateFields(null);
                    coordenadasStatus.textContent = 'No fue posible cargar el catálogo de coordenadas.';
                }
            }

            async function resolveCoordinatesFromRecinto() {
                const recintoId = nomLugarSelect?.value?.trim() ?? '';

                if (!recintoId) {
                    resolveCoordinates();
                    return;
                }

                coordenadasStatus.textContent = 'Buscando coordenadas del recinto seleccionado...';

                try {
                    const recintos = await loadRecintosById();
                    const match = recintos[recintoId] ?? null;

                    isApplyingRecintoSelection = true;
                    setLocationFields(match);
                    isApplyingRecintoSelection = false;

                    if (match) {
                        coordenadasStatus.textContent = 'Ubicación rellenada automáticamente desde el recinto seleccionado.';
                    } else {
                        coordenadasStatus.textContent = 'No se encontró una coincidencia para el recinto seleccionado.';
                    }
                } catch (error) {
                    setCoordinateFields(null);
                    coordenadasStatus.textContent = 'No fue posible cargar el catálogo de coordenadas.';
                }
            }

            function handleManualLocationInput() {
                if (!isApplyingRecintoSelection && nomLugarSelect?.value) {
                    nomLugarSelect.value = '';
                }

                resolveCoordinates();
            }

            portadaInput?.addEventListener('change', function(event) {
                const file = event.target.files?.[0];

                if (coverPreviewUrl) {
                    URL.revokeObjectURL(coverPreviewUrl);
                    coverPreviewUrl = null;
                }

                if (!file) {
                    portadaPreviewWrapper.classList.add('hidden');
                    portadaPreviewEmpty.classList.remove('hidden');
                    portadaPreview.removeAttribute('src');
                    return;
                }

                coverPreviewUrl = URL.createObjectURL(file);
                portadaPreview.src = coverPreviewUrl;
                portadaPreviewWrapper.classList.remove('hidden');
                portadaPreviewEmpty.classList.add('hidden');
            });

            calleInput?.addEventListener('input', handleManualLocationInput);
            numeroInput?.addEventListener('input', handleManualLocationInput);
            nomLugarSelect?.addEventListener('change', resolveCoordinatesFromRecinto);

            formCrearEvento?.addEventListener('submit', function(event) {
                const debeConfirmarCambioDestacado = destacadoInput?.checked && eventoDestacadoActual && forceChangeDestacadoInput?.value !== '1';

                if (!debeConfirmarCambioDestacado) {
                    return;
                }

                event.preventDefault();

                Swal.fire({
                    icon: 'warning',
                    title: 'Cambiar evento destacado',
                    text: `Actualmente "${eventoDestacadoActual}" está marcado como destacado. ¿Seguro que deseas reemplazarlo por este nuevo evento?`,
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
                    formCrearEvento.submit();
                });
            });

            Promise.all([
                    loadStreetSuggestions(),
                    loadDireccionesLookup(),
                ])
                .then(function() {
                    if (nomLugarSelect?.value) {
                        resolveCoordinatesFromRecinto();
                    } else if (calleInput?.value.trim() && numeroInput?.value.trim()) {
                        resolveCoordinates();
                    }
                })
                .catch(function() {
                    coordenadasStatus.textContent = 'No fue posible cargar el catálogo de coordenadas.';
                });

            updateMap(null, null);
        });
    </script>
@endpush
