@extends('layouts.app')

@php
    $fechaPublicacion = old('fecha_publicacion', optional($historia->fecha_publicacion)->format('Y-m-d') ?? now()->format('Y-m-d'));
    $desarrollo = old('desarrollo', $historia->desarrollo);
    $fuentesTitulo = old('fuentes_titulo', $historia->fuentes->pluck('titulo')->all() ?: ['']);
    $fuentesDescripcion = old('fuentes_descripcion', $historia->fuentes->pluck('descripcion')->all() ?: ['']);
    $fuentesUrl = old('fuentes_url', $historia->fuentes->pluck('url')->all() ?: ['']);
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
                <h2 class="text-xl font-semibold text-[#201815]">Editar Dato Historico</h2>
                <p class="mt-1 text-sm text-[#7d6870]">
                    Actualiza portada, textos principales, galeria de imagenes y fuentes asociadas al contenido historico.
                </p>
            </div>
            <a href="{{ route('admin.historia') }}"
                class="inline-flex items-center justify-center rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">
                Regresar
            </a>
        </div>

        <form class="space-y-6" enctype="multipart/form-data" method="POST"
            action="{{ route('admin.historia.update', $historia) }}" id="form-editar-historia">
            @csrf
            @method('PUT')

            <div class="grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label for="portada" class="mb-1 block text-sm font-medium text-[#3e2d31]">Portada</label>
                    <input type="file" id="portada" name="portada" accept="image/*"
                        class="w-full rounded-2xl border {{ $errors->has('portada') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition file:mr-4 file:rounded-full file:border-0 file:bg-[#63102a] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-[#7f173c] focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    @error('portada')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                    <div id="portada-preview-wrapper"
                        class="mt-4 {{ $historia->portada ? 'flex' : 'hidden' }} min-h-[240px] items-center justify-center overflow-hidden rounded-[24px] border border-[#eadde2] bg-[#fffafc] p-4">
                        <img id="portada-preview"
                            src="{{ $historia->portada ? \App\Support\ImageManager::publicUrl($historia->portada) : '' }}"
                            alt="Vista previa de portada" class="max-h-[320px] w-full object-contain">
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label for="titulo" class="mb-1 block text-sm font-medium text-[#3e2d31]">Titulo</label>
                    <input type="text" id="titulo" name="titulo" value="{{ old('titulo', $historia->titulo) }}"
                        class="w-full rounded-2xl border {{ $errors->has('titulo') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    @error('titulo')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="autor" class="mb-1 block text-sm font-medium text-[#3e2d31]">Autor</label>
                    <input type="text" id="autor" name="autor" value="{{ old('autor', $historia->autor) }}"
                        class="w-full rounded-2xl border {{ $errors->has('autor') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    @error('autor')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="resumen_corto" class="mb-1 block text-sm font-medium text-[#3e2d31]">Resumen corto</label>
                    <textarea id="resumen_corto" name="resumen_corto" rows="3"
                        class="w-full rounded-2xl border {{ $errors->has('resumen_corto') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">{{ old('resumen_corto', $historia->resumen_corto) }}</textarea>
                    @error('resumen_corto')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="periodo" class="mb-1 block text-sm font-medium text-[#3e2d31]">Periodo</label>
                    <input type="text" id="periodo" name="periodo" value="{{ old('periodo', $historia->periodo) }}"
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
                    @error('desarrollo')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="galeria" class="mb-1 block text-sm font-medium text-[#3e2d31]">Galeria</label>
                    <input type="file" id="galeria" name="galeria[]" accept="image/*" multiple
                        class="w-full rounded-2xl border {{ $errors->has('galeria') || $errors->has('galeria.*') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition file:mr-4 file:rounded-full file:border-0 file:bg-[#63102a] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-[#7f173c] focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    <p id="galeria-ayuda" class="mt-2 text-[13px] leading-6 text-[#6f6166]">
                        Si seleccionas nuevas imagenes, la galeria actual sera reemplazada. Maximo 5 imagenes.
                    </p>
                    @error('galeria')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                    @error('galeria.*')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                    <div id="galeria-preview" class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($historia->galeria as $imagen)
                            <div class="overflow-hidden rounded-[20px] border border-[#eadde2] bg-[#fffafc]">
                                <img src="{{ \App\Support\ImageManager::publicUrl($imagen->imagen) }}" alt="Imagen actual de galeria" class="h-40 w-full object-cover">
                            </div>
                        @endforeach
                    </div>
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
                        @foreach ($fuentesTitulo as $index => $fuenteTitulo)
                            <div class="fuente-item rounded-[20px] border border-[#e8d9cb] bg-[#fffdfa] p-4">
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div class="md:col-span-2">
                                        <label class="mb-1 block text-sm font-medium text-[#3e2d31]">Titulo de la fuente</label>
                                        <input type="text" name="fuentes_titulo[]" value="{{ $fuenteTitulo }}"
                                            class="w-full rounded-2xl border border-[#e8d9cb] bg-white px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                                        @error("fuentes_titulo.$index")
                                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="mb-1 block text-sm font-medium text-[#3e2d31]">Descripcion</label>
                                        <textarea name="fuentes_descripcion[]" rows="3"
                                            class="w-full rounded-2xl border border-[#e8d9cb] bg-white px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">{{ $fuentesDescripcion[$index] ?? '' }}</textarea>
                                        @error("fuentes_descripcion.$index")
                                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-[#3e2d31]">URL</label>
                                        <input type="text" name="fuentes_url[]" value="{{ $fuentesUrl[$index] ?? '' }}"
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
                        <option value="1" @selected(old('estatus', (string) $historia->estatus) == '1')>Publicado</option>
                        <option value="0" @selected(old('estatus', (string) $historia->estatus) == '0')>Borrador</option>
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
                    Guardar Cambios
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
                    return;
                }

                portadaPreview.src = URL.createObjectURL(file);
                portadaPreviewWrapper.classList.remove('hidden');
                portadaPreviewWrapper.classList.add('flex');
            });

            galeriaInput?.addEventListener('change', function(event) {
                const files = Array.from(event.target.files || []);

                if (files.length > 5) {
                    event.target.value = '';
                    galeriaAyuda.textContent = 'Solo puedes seleccionar hasta 5 imagenes para la galeria.';
                    return;
                }

                if (!files.length) {
                    galeriaAyuda.textContent = 'Si seleccionas nuevas imagenes, la galeria actual sera reemplazada. Maximo 5 imagenes.';
                    return;
                }

                galeriaAyuda.textContent = `${files.length} imagen(es) seleccionada(s). Al guardar se reemplazara la galeria actual.`;
                galeriaPreview.innerHTML = '';

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
                            <input type="url" name="fuentes_url[]" class="w-full rounded-2xl border border-[#e8d9cb] bg-white px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
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
