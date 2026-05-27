@extends('layouts.app')
@section('title', 'Puntos Mapa')
@section('title-section', 'Editar Punto Mapa')
@section('description', 'Actualiza la informacion del punto en el mapa y reemplaza su imagen si hace falta.')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
@endpush

@section('content')
    <div class="w-full rounded-[24px] bg-white p-6 shadow-[0_24px_60px_rgba(32,24,21,0.12)]">
        <div class="mb-6 flex items-start justify-between gap-4 border-b border-[#efe6dd] pb-4">
            <div>
                <h2 class="text-xl font-semibold text-[#201815]">Editar Punto</h2>
                <p class="mt-1 text-sm text-[#7d6870]">Actualiza la informacion del punto y valida su ubicacion en el mapa.</p>
            </div>
            <a href="{{ route('admin.puntos-mapa') }}"
                class="inline-flex items-center justify-center rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">
                Regresar
            </a>
        </div>

        <form class="space-y-6" enctype="multipart/form-data" method="POST" action="{{ route('admin.puntos-mapa.update', $puntoMapa) }}">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    Revisa los campos marcados para continuar.
                </div>
            @endif

            <div class="grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label for="nombre_punto" class="mb-1 block text-sm font-medium text-[#3e2d31]">Nombre</label>
                    <input type="text" id="nombre_punto" name="nombre_punto" value="{{ old('nombre_punto', $puntoMapa->nombre_punto) }}"
                        class="w-full rounded-2xl border {{ $errors->has('nombre_punto') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    @error('nombre_punto')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="descripcion" class="mb-1 block text-sm font-medium text-[#3e2d31]">Descripcion</label>
                    <textarea id="descripcion" name="descripcion" rows="4"
                        class="w-full rounded-2xl border {{ $errors->has('descripcion') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">{{ old('descripcion', $puntoMapa->descripcion) }}</textarea>
                    @error('descripcion')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="foto_principal" class="mb-1 block text-sm font-medium text-[#3e2d31]">Foto principal</label>
                    <input type="file" id="foto_principal" name="foto_principal" accept="image/*"
                        class="w-full rounded-2xl border {{ $errors->has('foto_principal') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition file:mr-4 file:rounded-full file:border-0 file:bg-[#63102a] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-[#7f173c] focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    @error('foto_principal')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                    <div id="foto-preview-wrapper"
                        class="{{ $puntoMapa->foto_principal ? 'mt-4 flex' : 'mt-4 hidden' }} min-h-[280px] items-center justify-center overflow-hidden rounded-[24px] border border-[#eadde2] bg-[#fffafc] p-4">
                        <img id="foto-preview"
                            src="{{ $puntoMapa->foto_principal ? route('admin.puntos-mapa.foto', $puntoMapa) : '' }}"
                            alt="Vista previa de la foto principal" class="max-h-[420px] max-w-full object-contain">
                    </div>
                    <p id="foto-preview-empty" class="{{ $puntoMapa->foto_principal ? 'hidden' : 'mt-3' }} text-[13px] leading-6 text-[#6f6166]">
                        Selecciona una imagen para ver la vista previa.
                    </p>
                </div>

                <div>
                    <label for="categoria_id" class="mb-1 block text-sm font-medium text-[#3e2d31]">Categoria</label>
                    <select id="categoria_id" name="categoria_id"
                        class="w-full rounded-2xl border {{ $errors->has('categoria_id') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                        <option value="">Selecciona una categoria</option>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->id }}" @selected(old('categoria_id', $puntoMapa->categoria_id) == $categoria->id)>{{ $categoria->tipo }}</option>
                        @endforeach
                    </select>
                    @error('categoria_id')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="calle" class="mb-1 block text-sm font-medium text-[#3e2d31]">Calle</label>
                    <input type="text" id="calle" name="calle" list="calles-sugeridas" value="{{ old('calle', $puntoMapa->calle) }}"
                        class="w-full rounded-2xl border {{ $errors->has('calle') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    <datalist id="calles-sugeridas"></datalist>
                    <p class="mt-1 text-[13px] leading-6 text-[#6f6166]">
                        Selecciona una calle del catalogo y captura el numero exterior para autocompletar la ubicacion.
                    </p>
                    @error('calle')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="numero_exterior" class="mb-1 block text-sm font-medium text-[#3e2d31]">Numero exterior</label>
                    <input type="text" id="numero_exterior" name="numero_exterior" value="{{ old('numero_exterior', $puntoMapa->numero_exterior) }}"
                        class="w-full rounded-2xl border {{ $errors->has('numero_exterior') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    @error('numero_exterior')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="numero_interior" class="mb-1 block text-sm font-medium text-[#3e2d31]">Numero interior</label>
                    <input type="text" id="numero_interior" name="numero_interior" value="{{ old('numero_interior', $puntoMapa->numero_interior) }}"
                        class="w-full rounded-2xl border {{ $errors->has('numero_interior') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    @error('numero_interior')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="cp" class="mb-1 block text-sm font-medium text-[#3e2d31]">Codigo postal</label>
                    <input type="text" id="cp" name="cp" value="{{ old('cp', $puntoMapa->cp) }}"
                        class="w-full rounded-2xl border {{ $errors->has('cp') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    @error('cp')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="colonia" class="mb-1 block text-sm font-medium text-[#3e2d31]">Colonia</label>
                    <input type="text" id="colonia" name="colonia" value="{{ old('colonia', $puntoMapa->colonia) }}"
                        class="w-full rounded-2xl border {{ $errors->has('colonia') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    @error('colonia')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="latitud" class="mb-1 block text-sm font-medium text-[#3e2d31]">Latitud</label>
                    <input type="number" step="0.00000001" id="latitud" name="latitud" value="{{ old('latitud', $puntoMapa->latitud) }}"
                        class="w-full rounded-2xl border {{ $errors->has('latitud') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    @error('latitud')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="longitud" class="mb-1 block text-sm font-medium text-[#3e2d31]">Longitud</label>
                    <input type="number" step="0.00000001" id="longitud" name="longitud" value="{{ old('longitud', $puntoMapa->longitud) }}"
                        class="w-full rounded-2xl border {{ $errors->has('longitud') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    @error('longitud')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <p id="coordenadas-status" class="text-[13px] leading-6 text-[#6f6166]">
                        Selecciona una calle y captura el numero exterior para completar la ubicacion automaticamente.
                    </p>
                </div>

                <div class="md:col-span-2">
                    <div class="overflow-hidden rounded-[24px] border border-[#eadde2] bg-[#fffafc]">
                        <div id="mapa-punto" class="hidden h-[320px] w-full"></div>
                        <div id="mapa-empty" class="px-5 py-8 text-center text-[14px] leading-7 text-[#6f6166]">
                            Cuando se encuentren coordenadas validas, aqui se mostrara el punto en el mapa.
                        </div>
                    </div>
                </div>

                <div>
                    <label for="telefono" class="mb-1 block text-sm font-medium text-[#3e2d31]">Telefono</label>
                    <input type="text" id="telefono" name="telefono" value="{{ old('telefono', $puntoMapa->telefono) }}"
                        class="w-full rounded-2xl border {{ $errors->has('telefono') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    @error('telefono')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="mb-1 block text-sm font-medium text-[#3e2d31]">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $puntoMapa->email) }}"
                        class="w-full rounded-2xl border {{ $errors->has('email') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    @error('email')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="horarios" class="mb-1 block text-sm font-medium text-[#3e2d31]">Horarios</label>
                    <textarea id="horarios" name="horarios" rows="3"
                        class="w-full rounded-2xl border {{ $errors->has('horarios') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">{{ old('horarios', $puntoMapa->horarios) }}</textarea>
                    @error('horarios')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-[#efe6dd] pt-4 sm:flex-row sm:justify-end">
                <a href="{{ route('admin.puntos-mapa') }}"
                    class="inline-flex items-center justify-center rounded-full bg-slate-100 px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">
                    Cancelar
                </a>
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-full bg-[#63102a] px-5 py-2.5 text-sm font-semibold text-white shadow-[0_10px_24px_rgba(99,16,42,0.22)] transition hover:bg-[#7f173c]">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const coordenadasUrl = @json(asset('data/coordenadas.json'));
            const nezaCenter = [19.4006, -99.0148];
            const fotoInput = document.getElementById('foto_principal');
            const fotoPreview = document.getElementById('foto-preview');
            const fotoPreviewWrapper = document.getElementById('foto-preview-wrapper');
            const fotoPreviewEmpty = document.getElementById('foto-preview-empty');
            const calleInput = document.getElementById('calle');
            const numeroExteriorInput = document.getElementById('numero_exterior');
            const coloniaInput = document.getElementById('colonia');
            const cpInput = document.getElementById('cp');
            const latitudInput = document.getElementById('latitud');
            const longitudInput = document.getElementById('longitud');
            const callesDatalist = document.getElementById('calles-sugeridas');
            const coordenadasStatus = document.getElementById('coordenadas-status');
            const mapaEmpty = document.getElementById('mapa-empty');
            const mapElement = document.getElementById('mapa-punto');

            let coverPreviewUrl = null;
            let coordenadasLookup = null;
            let callesLoaded = false;
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

            function normalizeText(value) {
                return String(value ?? '')
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .trim()
                    .replace(/\s+/g, ' ')
                    .toUpperCase();
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

            function buildLookupKey(calle, numeroExterior) {
                return `${normalizeText(calle)}|${normalizeText(numeroExterior)}`;
            }

            function isValidCoordinatePair(lat, lng) {
                return Number.isFinite(lat) && Number.isFinite(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180;
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

            async function loadCoordenadas() {
                if (coordenadasLookup) {
                    return coordenadasLookup;
                }

                const payload = await fetchJson(coordenadasUrl);
                const features = Array.isArray(payload?.features) ? payload.features : [];
                const streets = new Set();
                const lookup = {};

                features.forEach((feature) => {
                    const properties = feature?.properties ?? {};
                    const calle = decodeText(properties.NOMVIAL);
                    const numeroExterior = decodeText(properties.NUMEXT);

                    if (calle) {
                        streets.add(calle);
                    }

                    if (!calle || !numeroExterior) {
                        return;
                    }

                    lookup[buildLookupKey(calle, numeroExterior)] = {
                        calle,
                        numeroExterior,
                        colonia: decodeText(properties.NOMBRE_CUA || properties.NOMREF1 || ''),
                        cp: decodeText(properties.CP || ''),
                        latitud: properties.latitud,
                        longitud: properties.longitud,
                    };
                });

                coordenadasLookup = {
                    lookup,
                    streets: Array.from(streets).sort((a, b) => a.localeCompare(b, 'es')),
                };

                return coordenadasLookup;
            }

            async function loadStreetSuggestions() {
                if (callesLoaded) {
                    return;
                }

                const coordenadas = await loadCoordenadas();

                callesDatalist.innerHTML = coordenadas.streets.map((street) => {
                    const safeStreet = String(street).replace(/"/g, '&quot;');
                    return `<option value="${safeStreet}"></option>`;
                }).join('');

                callesLoaded = true;
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

                mapInstance.setView(nextPosition, 17, {
                    animate: true
                });

                setTimeout(() => {
                    mapInstance.invalidateSize();
                }, 0);
            }

            function clearCoordinateFields() {
                coloniaInput.value = '';
                cpInput.value = '';
                latitudInput.value = '';
                longitudInput.value = '';
                updateMap(null, null);
            }

            async function resolveCoordinates() {
                const calle = calleInput.value.trim();
                const numeroExterior = numeroExteriorInput.value.trim();

                if (!calle || !numeroExterior) {
                    clearCoordinateFields();
                    coordenadasStatus.textContent = 'Selecciona una calle y captura el numero exterior para completar la ubicacion automaticamente.';
                    return;
                }

                coordenadasStatus.textContent = 'Buscando coincidencia en coordenadas.json...';

                try {
                    const coordenadas = await loadCoordenadas();
                    const match = coordenadas.lookup[buildLookupKey(calle, numeroExterior)] ?? null;

                    if (!match) {
                        clearCoordinateFields();
                        coordenadasStatus.textContent = 'No se encontro una coincidencia para esa calle y numero exterior.';
                        return;
                    }

                    coloniaInput.value = match.colonia ?? '';
                    cpInput.value = match.cp ?? '';
                    latitudInput.value = match.latitud != null ? String(match.latitud) : '';
                    longitudInput.value = match.longitud != null ? String(match.longitud) : '';
                    coordenadasStatus.textContent = 'Ubicacion completada automaticamente desde coordenadas.json.';
                    updateMap(latitudInput.value, longitudInput.value);
                } catch (error) {
                    clearCoordinateFields();
                    coordenadasStatus.textContent = 'No fue posible cargar el archivo coordenadas.json.';
                }
            }

            fotoInput?.addEventListener('change', function(event) {
                const file = event.target.files?.[0];

                if (coverPreviewUrl) {
                    URL.revokeObjectURL(coverPreviewUrl);
                    coverPreviewUrl = null;
                }

                if (!file) {
                    return;
                }

                coverPreviewUrl = URL.createObjectURL(file);
                fotoPreview.src = coverPreviewUrl;
                fotoPreviewWrapper.classList.remove('hidden');
                fotoPreviewWrapper.classList.add('flex');
                fotoPreviewEmpty.classList.add('hidden');
            });

            calleInput?.addEventListener('input', resolveCoordinates);
            numeroExteriorInput?.addEventListener('input', resolveCoordinates);
            latitudInput?.addEventListener('input', () => updateMap(latitudInput.value, longitudInput.value));
            longitudInput?.addEventListener('input', () => updateMap(latitudInput.value, longitudInput.value));

            loadStreetSuggestions()
                .then(() => {
                    if (calleInput?.value.trim() && numeroExteriorInput?.value.trim()) {
                        resolveCoordinates();
                    }
                })
                .catch(() => {
                    coordenadasStatus.textContent = 'No fue posible cargar el archivo coordenadas.json.';
                });

            updateMap(latitudInput.value, longitudInput.value);
        });
    </script>
@endpush
