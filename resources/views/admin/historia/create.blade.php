@extends('layouts.app')

@php
    $fechaPublicacion = old('fecha_publicacion', now()->format('Y-m-d'));
    $desarrollo = old('desarrollo', '');
@endphp

@section('title', 'Historia')
@section('title-section', '')
@section('description', '')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
@endpush

@section('content')
    <div class="w-full rounded-[24px] bg-white p-6 shadow-[0_24px_60px_rgba(32,24,21,0.12)]">
        <div class="mb-6 flex items-start justify-between gap-4 border-b border-[#efe6dd] pb-4">
            <div>
                <h2 class="text-xl font-semibold text-[#201815]">Crear Dato Historico</h2>
                <p class="mt-1 text-sm text-[#7d6870]">
                    Registra portada, textos principales, galeria de imagenes y fuentes asociadas al contenido historico.
                </p>
            </div>
            <a href="{{ route('admin.historia') }}"
                class="inline-flex items-center justify-center rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">
                Regresar
            </a>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-[20px] border border-rose-200 bg-rose-50 px-5 py-4 text-rose-800">
                <p class="text-sm font-semibold">No se pudo guardar el dato historico.</p>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="space-y-6" enctype="multipart/form-data" method="POST"  novalidate
            action="{{ route('admin.historia.store') }}" id="form-crear-historia">
            @csrf

            <div class="grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label for="portada" class="mb-1 block text-sm font-medium text-[#3e2d31]">Portada</label>
                    <input type="file" id="portada" name="portada" accept="image/*"
                        class="w-full rounded-2xl border {{ $errors->has('portada') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition file:mr-4 file:rounded-full file:border-0 file:bg-[#63102a] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-[#7f173c] focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    <p class="mt-2 text-[13px] leading-6 text-[#6f6166]">Selecciona una imagen principal para el encabezado. Formatos permitidos: JPG, PNG, GIF y WEBP.</p>
                    @error('portada')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                    <div id="portada-preview-wrapper"
                        class="mt-4 hidden min-h-[240px] items-center justify-center overflow-hidden rounded-[24px] border border-[#eadde2] bg-[#fffafc] p-4">
                        <img id="portada-preview" src="" alt="Vista previa de portada"
                            class="max-h-[320px] w-full object-contain">
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label for="titulo" class="mb-1 block text-sm font-medium text-[#3e2d31]">Titulo</label>
                    <input type="text" id="titulo" name="titulo" value="{{ old('titulo') }}"
                        class="w-full rounded-2xl border {{ $errors->has('titulo') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    @error('titulo')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="autor" class="mb-1 block text-sm font-medium text-[#3e2d31]">Autor</label>
                    <input type="text" id="autor" name="autor" value="{{ old('autor') }}"
                        class="w-full rounded-2xl border {{ $errors->has('autor') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    @error('autor')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="resumen_corto" class="mb-1 block text-sm font-medium text-[#3e2d31]">Resumen corto</label>
                    <textarea id="resumen_corto" name="resumen_corto" rows="3"
                        class="w-full rounded-2xl border {{ $errors->has('resumen_corto') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">{{ old('resumen_corto') }}</textarea>
                    @error('resumen_corto')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="periodo" class="mb-1 block text-sm font-medium text-[#3e2d31]">Periodo</label>
                    <input type="text" id="periodo" name="periodo" value="{{ old('periodo') }}"
                        placeholder="Ej. 1954 - 1970"
                        class="w-full rounded-2xl border {{ $errors->has('periodo') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    @error('periodo')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-[#3e2d31]">Desarrollo</label>
                    <input type="hidden" id="desarrollo" name="desarrollo" value="{{ $desarrollo }}">
                    <div class="overflow-hidden rounded-2xl border {{ $errors->has('desarrollo') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-white' }}">
                        <div id="desarrollo-editor" class="min-h-[260px] text-sm text-[#201815]"></div>
                    </div>
                    <p class="mt-2 text-[13px] leading-6 text-[#6f6166]">
                        Usa el editor para agregar headings, texto en negritas, italic, listas y enlaces.
                    </p>
                    @error('desarrollo')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="galeria" class="mb-1 block text-sm font-medium text-[#3e2d31]">Galeria</label>
                    <input type="file" id="galeria" name="galeria[]" accept="image/*" multiple
                        class="w-full rounded-2xl border {{ $errors->has('galeria') || $errors->has('galeria.*') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition file:mr-4 file:rounded-full file:border-0 file:bg-[#63102a] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-[#7f173c] focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    <p id="galeria-ayuda" class="mt-2 text-[13px] leading-6 text-[#6f6166]">
                        Puedes seleccionar varias imagenes al mismo tiempo para construir la galeria.
                    </p>
                    @error('galeria')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                    @error('galeria.*')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                    <div id="galeria-preview" class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3"></div>
                </div>

                <div class="md:col-span-2">
                    <div class="mb-3 flex items-center justify-between">
                        <label class="block text-sm font-medium text-[#3e2d31]">Fuentes y referencias</label>
                        <button type="button" id="agregar-fuente"
                            class="inline-flex items-center rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">
                            Agregar fuente
                        </button>
                    </div>

                    <div id="fuentes-wrapper" class="space-y-4">
                        @php
                            $oldTitulos = old('fuentes_titulo', ['']);
                            $oldDescripciones = old('fuentes_descripcion', ['']);
                            $oldUrls = old('fuentes_url', ['']);
                        @endphp

                        @foreach ($oldTitulos as $index => $oldTitulo)
                            <div class="fuente-item rounded-[20px] border border-[#e8d9cb] bg-[#fffdfa] p-4">
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div class="md:col-span-2">
                                        <label class="mb-1 block text-sm font-medium text-[#3e2d31]">Titulo de la fuente</label>
                                        <input type="text" name="fuentes_titulo[]" value="{{ $oldTitulo }}"
                                            class="w-full rounded-2xl border border-[#e8d9cb] bg-white px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                                        @error("fuentes_titulo.$index")
                                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="mb-1 block text-sm font-medium text-[#3e2d31]">Descripcion</label>
                                        <textarea name="fuentes_descripcion[]" rows="3"
                                            class="w-full rounded-2xl border border-[#e8d9cb] bg-white px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">{{ $oldDescripciones[$index] ?? '' }}</textarea>
                                        @error("fuentes_descripcion.$index")
                                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-[#3e2d31]">URL</label>
                                        <input type="url" name="fuentes_url[]" value="{{ $oldUrls[$index] ?? '' }}"
                                            class="w-full rounded-2xl border border-[#e8d9cb] bg-white px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                                        @error("fuentes_url.$index")
                                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="flex items-end justify-end">
                                        <button type="button"
                                            class="eliminar-fuente inline-flex items-center rounded-full bg-rose-100 px-4 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-200">
                                            Quitar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label for="fecha_publicacion" class="mb-1 block text-sm font-medium text-[#3e2d31]">Fecha de publicacion</label>
                    <input type="hidden" name="fecha_publicacion" value="{{ $fechaPublicacion }}">
                    <input type="date" id="fecha_publicacion" value="{{ $fechaPublicacion }}" disabled
                        class="w-full cursor-not-allowed rounded-2xl border {{ $errors->has('fecha_publicacion') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-slate-100' }} px-4 py-3 text-sm text-[#201815] outline-none">
                    @error('fecha_publicacion')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="estatus" class="mb-1 block text-sm font-medium text-[#3e2d31]">Estatus</label>
                    <select id="estatus" name="estatus"
                        class="w-full rounded-2xl border {{ $errors->has('estatus') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                        <option value="1" @selected(old('estatus', '1') == '1')>Publicado</option>
                        <option value="0" @selected(old('estatus', '1') == '0')>Borrador</option>
                    </select>
                    @error('estatus')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-[#efe6dd] pt-4 sm:flex-row sm:justify-end">
                <a href="{{ route('admin.historia') }}"
                    class="inline-flex items-center justify-center rounded-full bg-slate-100 px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">
                    Cancelar
                </a>
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-full bg-[#63102a] px-5 py-2.5 text-sm font-semibold text-white shadow-[0_10px_24px_rgba(99,16,42,0.22)] transition hover:bg-[#7f173c]">
                    Guardar Dato Historico
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const wrapper = document.getElementById('fuentes-wrapper');
            const addButton = document.getElementById('agregar-fuente');
            const portadaInput = document.getElementById('portada');
            const portadaPreview = document.getElementById('portada-preview');
            const portadaPreviewWrapper = document.getElementById('portada-preview-wrapper');
            const galeriaInput = document.getElementById('galeria');
            const galeriaPreview = document.getElementById('galeria-preview');
            const galeriaAyuda = document.getElementById('galeria-ayuda');
            const desarrolloInput = document.getElementById('desarrollo');

            const quill = new Quill('#desarrollo-editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ header: [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline'],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        ['link', 'blockquote'],
                        ['clean']
                    ]
                }
            });

            document.querySelectorAll('.ql-toolbar button').forEach((button) => {
                button.setAttribute('type', 'button');
            });

            if (desarrolloInput.value) {
                quill.root.innerHTML = desarrolloInput.value;
            }

            quill.on('text-change', function() {
                desarrolloInput.value = quill.root.innerHTML === '<p><br></p>' ? '' : quill.root.innerHTML;
            });

            portadaInput?.addEventListener('change', function(event) {
                const file = event.target.files?.[0];

                if (!file) {
                    portadaPreview.src = '';
                    portadaPreviewWrapper.classList.add('hidden');
                    portadaPreviewWrapper.classList.remove('flex');
                    return;
                }

                portadaPreview.src = URL.createObjectURL(file);
                portadaPreviewWrapper.classList.remove('hidden');
                portadaPreviewWrapper.classList.add('flex');
            });

            galeriaInput?.addEventListener('change', function(event) {
                const files = Array.from(event.target.files || []);
                galeriaPreview.innerHTML = '';

                if (!files.length) {
                    galeriaAyuda.textContent = 'Puedes seleccionar varias imagenes al mismo tiempo para construir la galeria.';
                    return;
                }

                galeriaAyuda.textContent = `${files.length} imagen(es) seleccionada(s) para la galeria.`;

                files.forEach((file) => {
                    const card = document.createElement('div');
                    card.className = 'overflow-hidden rounded-[20px] border border-[#eadde2] bg-[#fffafc]';
                    card.innerHTML = `
                        <img src="${URL.createObjectURL(file)}" alt="Vista previa de galeria" class="h-40 w-full object-cover">
                        <div class="px-3 py-2 text-[12px] text-[#6f6166]">${file.name}</div>
                    `;
                    galeriaPreview.appendChild(card);
                });
            });

            const bindRemoveButtons = () => {
                wrapper.querySelectorAll('.eliminar-fuente').forEach((button) => {
                    button.onclick = function() {
                        const items = wrapper.querySelectorAll('.fuente-item');

                        if (items.length === 1) {
                            items[0].querySelectorAll('input, textarea').forEach((field) => field.value = '');
                            return;
                        }

                        button.closest('.fuente-item')?.remove();
                    };
                });
            };

            addButton?.addEventListener('click', function() {
                const item = document.createElement('div');
                item.className = 'fuente-item rounded-[20px] border border-[#e8d9cb] bg-[#fffdfa] p-4';
                item.innerHTML = `
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label class="mb-1 block text-sm font-medium text-[#3e2d31]">Titulo de la fuente</label>
                            <input type="text" name="fuentes_titulo[]" class="w-full rounded-2xl border border-[#e8d9cb] bg-white px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-1 block text-sm font-medium text-[#3e2d31]">Descripcion</label>
                            <textarea name="fuentes_descripcion[]" rows="3" class="w-full rounded-2xl border border-[#e8d9cb] bg-white px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15"></textarea>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-[#3e2d31]">URL</label>
                            <input type="text" name="fuentes_url[]" class="w-full rounded-2xl border border-[#e8d9cb] bg-white px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                        </div>
                        <div class="flex items-end justify-end">
                            <button type="button" class="eliminar-fuente inline-flex items-center rounded-full bg-rose-100 px-4 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-200">
                                Quitar
                            </button>
                        </div>
                    </div>
                `;
                wrapper.appendChild(item);
                bindRemoveButtons();
            });

            bindRemoveButtons();
        });
    </script>
@endpush
