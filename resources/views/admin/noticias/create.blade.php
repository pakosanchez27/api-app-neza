@extends('layouts.app')

@php
    $isEdit = isset($noticia);
    $pageTitle = $isEdit ? 'Editar Noticia' : 'Crear Noticia';
    $pageDescription = $isEdit
        ? 'Actualiza la informacion principal de la noticia y conserva los recursos actuales si no seleccionas nuevos archivos.'
        : 'Completa la informacion principal de la noticia para preparar su publicacion.';
    $existingGallery = collect($noticia->galeria ?? [])->filter()->values();
    $galleryDefaultMessage = $isEdit && $existingGallery->isNotEmpty()
        ? 'Si no seleccionas nuevas imagenes, se conservaran las ' . $existingGallery->count() . ' imagen(es) actuales de la galeria.'
        : 'Puedes seleccionar una o varias imagenes para la galeria.';
    $fechaPublicacion = $isEdit
        ? old('fecha_publicacion', isset($noticia->fecha_publicacion) && $noticia->fecha_publicacion
            ? \Illuminate\Support\Carbon::parse($noticia->fecha_publicacion)->format('Y-m-d')
            : now()->format('Y-m-d'))
        : now()->format('Y-m-d');
@endphp

@section('title', 'Noticias')
@section('title-section', $pageTitle)
@section('description', $pageDescription)

@section('content')
    <div class="w-full rounded-[24px] bg-white p-6 shadow-[0_24px_60px_rgba(32,24,21,0.12)]">
        <div class="mb-6 flex items-start justify-between gap-4 border-b border-[#efe6dd] pb-4">
            <div>
                <h2 class="text-xl font-semibold text-[#201815]">{{ $pageTitle }}</h2>
                <p class="mt-1 text-sm text-[#7d6870]">
                    {{ $isEdit ? 'Modifica portada, textos clave, galeria, CTA, fecha de publicacion y estatus.' : 'Captura portada, textos clave, galeria, CTA, fecha de publicacion y estatus.' }}
                </p>
            </div>
            <a href="{{ route('admin.noticias') }}"
                class="inline-flex items-center justify-center rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">
                Regresar
            </a>
        </div>

        <form class="space-y-6" enctype="multipart/form-data" method="POST"
            action="{{ $isEdit ? route('admin.noticias.update', $noticia) : route('admin.noticias.store') }}"
            id="form-crear-noticia">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            @if ($errors->any())
                <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    Revisa los campos marcados para continuar.
                </div>
            @endif

            <div class="grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label for="portada" class="mb-1 block text-sm font-medium text-[#3e2d31]">Portada</label>
                    <input type="file" id="portada" name="portada" accept="image/*"
                        class="w-full rounded-2xl border {{ $errors->has('portada') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition file:mr-4 file:rounded-full file:border-0 file:bg-[#63102a] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-[#7f173c] focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    <p class="mt-2 text-[13px] leading-6 text-[#6f6166]">
                        Recomendacion: usa una imagen horizontal de al menos 1600 x 900 px para que la portada se vea bien en el carrusel y detalle.
                    </p>
                    @if ($isEdit && !empty($noticia->portada))
                        <p class="mt-1 text-[13px] leading-6 text-[#6f6166]">
                            Si no seleccionas una nueva imagen, se conservara la portada actual.
                        </p>
                    @endif
                    @error('portada')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                    <div id="portada-preview-wrapper"
                        class="mt-4 flex min-h-[240px] items-center justify-center overflow-hidden rounded-[24px] border border-[#eadde2] bg-[#fffafc] p-4 {{ !empty($noticia->portada) ? '' : 'hidden' }}">
                        <img id="portada-preview" src="{{ !empty($noticia->portada) ? \App\Support\ImageManager::publicUrl($noticia->portada) : '' }}"
                            alt="Vista previa de portada" class="max-h-[320px] w-full object-contain">
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label for="titulo" class="mb-1 block text-sm font-medium text-[#3e2d31]">Titulo</label>
                    <input type="text" id="titulo" name="titulo" value="{{ old('titulo', $noticia->titulo ?? '') }}"
                        maxlength="50" required
                        class="w-full rounded-2xl border {{ $errors->has('titulo') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    <p class="mt-2 text-[13px] leading-6 text-[#6f6166]">Maximo 50 caracteres.</p>
                    @error('titulo')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="subtitulo" class="mb-1 block text-sm font-medium text-[#3e2d31]">Subtitulo</label>
                    <input type="text" id="subtitulo" name="subtitulo"
                        value="{{ old('subtitulo', $noticia->subtitulo ?? '') }}"
                        maxlength="255"
                        class="w-full rounded-2xl border {{ $errors->has('subtitulo') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    <p class="mt-2 text-[13px] leading-6 text-[#6f6166]">Maximo 255 caracteres.</p>
                    @error('subtitulo')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="resumen" class="mb-1 block text-sm font-medium text-[#3e2d31]">Resumen</label>
                    <textarea id="resumen" name="resumen" rows="4"
                        class="w-full rounded-2xl border {{ $errors->has('resumen') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">{{ old('resumen', $noticia->resumen ?? '') }}</textarea>
                    @error('resumen')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="galeria" class="mb-1 block text-sm font-medium text-[#3e2d31]">Galeria</label>
                    <input type="file" id="galeria" name="galeria[]" accept="image/*" multiple
                        class="w-full rounded-2xl border {{ $errors->has('galeria') || $errors->has('galeria.*') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition file:mr-4 file:rounded-full file:border-0 file:bg-[#63102a] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-[#7f173c] focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    @error('galeria')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                    @error('galeria.*')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-[13px] leading-6 text-[#6f6166]">
                        Recomendacion: usa imagenes de al menos 1200 x 900 px y con proporcion similar entre si para que la galeria se vea uniforme.
                    </p>
                    <p id="galeria-status" class="mt-2 text-[13px] leading-6 text-[#6f6166]">
                        {{ $galleryDefaultMessage }}
                    </p>
                    @if ($isEdit && $existingGallery->isNotEmpty())
                        <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($existingGallery as $image)
                                <div class="overflow-hidden rounded-[20px] border border-[#eadde2] bg-[#fffafc]">
                                    <img src="{{ \App\Support\ImageManager::publicUrl($image) }}" alt="Imagen actual de la galeria"
                                        class="h-40 w-full object-cover">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="md:col-span-2">
                    <label for="cta" class="mb-1 block text-sm font-medium text-[#3e2d31]">URL para ver mas</label>
                    <input type="url" id="cta" name="cta" value="{{ old('cta', $noticia->cta ?? '') }}"
                        placeholder="https://ejemplo.com/mas-informacion"
                        class="w-full rounded-2xl border {{ $errors->has('cta') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    <p class="mt-2 text-[13px] leading-6 text-[#6f6166]">
                        Ingresa el enlace que abrira el boton "Ver mas" en el detalle de la noticia.
                    </p>
                    @error('cta')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="fecha_publicacion" class="mb-1 block text-sm font-medium text-[#3e2d31]">Fecha de publicacion</label>
                    @if ($isEdit)
                        <input type="date" id="fecha_publicacion" name="fecha_publicacion"
                            value="{{ $fechaPublicacion }}"
                            class="w-full rounded-2xl border {{ $errors->has('fecha_publicacion') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    @else
                        <input type="hidden" name="fecha_publicacion" value="{{ $fechaPublicacion }}">
                        <input type="date" id="fecha_publicacion" value="{{ $fechaPublicacion }}" disabled
                            class="w-full cursor-not-allowed rounded-2xl border {{ $errors->has('fecha_publicacion') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-slate-100' }} px-4 py-3 text-sm text-[#201815] outline-none">
                    @endif
                    @error('fecha_publicacion')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="estatus" class="mb-1 block text-sm font-medium text-[#3e2d31]">Estatus</label>
                    <select id="estatus" name="estatus"
                        class="w-full rounded-2xl border {{ $errors->has('estatus') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                        <option value="1" @selected(old('estatus', (string) ($noticia->estatus ?? '1')) == '1')>Activo</option>
                        <option value="0" @selected(old('estatus', (string) ($noticia->estatus ?? '1')) == '0')>Inactivo</option>
                    </select>
                    @error('estatus')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-[#efe6dd] pt-4 sm:flex-row sm:justify-end">
                <a href="{{ route('admin.noticias') }}"
                    class="inline-flex items-center justify-center rounded-full bg-slate-100 px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">
                    Cancelar
                </a>
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-full bg-[#63102a] px-5 py-2.5 text-sm font-semibold text-white shadow-[0_10px_24px_rgba(99,16,42,0.22)] transition hover:bg-[#7f173c]">
                    {{ $isEdit ? 'Actualizar Noticia' : 'Guardar Noticia' }}
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const portadaInput = document.getElementById('portada');
            const portadaPreview = document.getElementById('portada-preview');
            const portadaPreviewWrapper = document.getElementById('portada-preview-wrapper');
            const galeriaInput = document.getElementById('galeria');
            const galeriaStatus = document.getElementById('galeria-status');
            const galleryDefaultMessage = @json($galleryDefaultMessage);
            let coverPreviewUrl = null;

            portadaInput?.addEventListener('change', function(event) {
                const file = event.target.files?.[0];

                if (coverPreviewUrl) {
                    URL.revokeObjectURL(coverPreviewUrl);
                    coverPreviewUrl = null;
                }

                if (!file) {
                    if (!portadaPreview.dataset.originalSrc) {
                        portadaPreviewWrapper.classList.add('hidden');
                    }

                    portadaPreview.src = portadaPreview.dataset.originalSrc || '';
                    return;
                }

                coverPreviewUrl = URL.createObjectURL(file);
                portadaPreview.src = coverPreviewUrl;
                portadaPreviewWrapper.classList.remove('hidden');
            });

            if (portadaPreview && portadaPreview.getAttribute('src')) {
                portadaPreview.dataset.originalSrc = portadaPreview.getAttribute('src');
            }

            galeriaInput?.addEventListener('change', function(event) {
                const totalFiles = event.target.files?.length ?? 0;
                galeriaStatus.textContent = totalFiles
                    ? `${totalFiles} archivo(s) seleccionado(s) para la galeria.`
                    : galleryDefaultMessage;
            });
        });
    </script>
@endpush
