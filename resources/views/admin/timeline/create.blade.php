@extends('layouts.app')

@php
    $isEdit = isset($timeline);
    $pageTitle = $isEdit ? 'Editar Registro Historico' : 'Crear Registro Historico';
    $pageDescription = $isEdit
        ? 'Actualiza la informacion del comparativo historico y conserva las imagenes actuales si no seleccionas nuevos archivos.'
        : 'Completa la informacion del comparativo historico para agregar un nuevo registro al timeline.';
@endphp

@section('title', 'Timeline')
@section('title-section', $pageTitle)
@section('description', $pageDescription)

@section('content')
    <div class="w-full rounded-[24px] bg-white p-6 shadow-[0_24px_60px_rgba(32,24,21,0.12)]">
        <div class="mb-6 flex items-start justify-between gap-4 border-b border-[#efe6dd] pb-4">
            <div>
                <h2 class="text-xl font-semibold text-[#201815]">{{ $pageTitle }}</h2>
                <p class="mt-1 text-sm text-[#7d6870]">
                    {{ $isEdit ? 'Modifica lugar, descripcion, imagenes comparativas, orden y estatus.' : 'Captura lugar, descripcion, imagenes comparativas, orden y estatus.' }}
                </p>
            </div>
            <a href="{{ route('admin.timeline') }}"
                class="inline-flex items-center justify-center rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">
                Regresar
            </a>
        </div>

        <form class="space-y-6" enctype="multipart/form-data" method="POST" novalidate
            action="{{ $isEdit ? route('admin.timeline.update', $timeline) : route('admin.timeline.store') }}"
            id="form-crear-timeline">
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
                    <label for="lugar_turistico" class="mb-1 block text-sm font-medium text-[#3e2d31]">Lugar turistico</label>
                    <input type="text" id="lugar_turistico" name="lugar_turistico"
                        value="{{ old('lugar_turistico', $timeline->lugar_turistico ?? '') }}" maxlength="255" required
                        class="w-full rounded-2xl border {{ $errors->has('lugar_turistico') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    @error('lugar_turistico')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="descripcion" class="mb-1 block text-sm font-medium text-[#3e2d31]">Descripcion</label>
                    <textarea id="descripcion" name="descripcion" rows="5"
                        class="w-full rounded-2xl border {{ $errors->has('descripcion') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">{{ old('descripcion', $timeline->descripcion ?? '') }}</textarea>
                    @error('descripcion')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="imagen_antes" class="mb-1 block text-sm font-medium text-[#3e2d31]">Imagen antes</label>
                    <input type="file" id="imagen_antes" name="imagen_antes" accept="image/*"
                        class="w-full rounded-2xl border {{ $errors->has('imagen_antes') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition file:mr-4 file:rounded-full file:border-0 file:bg-[#63102a] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-[#7f173c] focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    @if ($isEdit && !empty($timeline->imagen_antes))
                        <p class="mt-2 text-[13px] leading-6 text-[#6f6166]">
                            Si no seleccionas una nueva imagen, se conservara la imagen actual.
                        </p>
                    @endif
                    @error('imagen_antes')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                    <div id="imagen-antes-preview-wrapper"
                        class="mt-4 flex min-h-[220px] items-center justify-center overflow-hidden rounded-[24px] border border-[#eadde2] bg-[#fffafc] p-4 {{ !empty($timeline->imagen_antes) ? '' : 'hidden' }}">
                        <img id="imagen-antes-preview"
                            src="{{ !empty($timeline->imagen_antes) ? \App\Support\ImageManager::publicUrl($timeline->imagen_antes) : '' }}"
                            alt="Vista previa de imagen antes" class="max-h-[280px] w-full object-contain">
                    </div>
                </div>

                <div>
                    <label for="imagen_despues" class="mb-1 block text-sm font-medium text-[#3e2d31]">Imagen despues</label>
                    <input type="file" id="imagen_despues" name="imagen_despues" accept="image/*"
                        class="w-full rounded-2xl border {{ $errors->has('imagen_despues') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition file:mr-4 file:rounded-full file:border-0 file:bg-[#63102a] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-[#7f173c] focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    @if ($isEdit && !empty($timeline->imagen_despues))
                        <p class="mt-2 text-[13px] leading-6 text-[#6f6166]">
                            Si no seleccionas una nueva imagen, se conservara la imagen actual.
                        </p>
                    @endif
                    @error('imagen_despues')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                    <div id="imagen-despues-preview-wrapper"
                        class="mt-4 flex min-h-[220px] items-center justify-center overflow-hidden rounded-[24px] border border-[#eadde2] bg-[#fffafc] p-4 {{ !empty($timeline->imagen_despues) ? '' : 'hidden' }}">
                        <img id="imagen-despues-preview"
                            src="{{ !empty($timeline->imagen_despues) ? \App\Support\ImageManager::publicUrl($timeline->imagen_despues) : '' }}"
                            alt="Vista previa de imagen despues" class="max-h-[280px] w-full object-contain">
                    </div>
                </div>

                <div>
                    <label for="orden" class="mb-1 block text-sm font-medium text-[#3e2d31]">Orden</label>
                    <input type="number" id="orden" name="orden" min="0"
                        value="{{ old('orden', $timeline->orden ?? 0) }}"
                        class="w-full rounded-2xl border {{ $errors->has('orden') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    <p class="mt-2 text-[13px] leading-6 text-[#6f6166]">Usa este valor para controlar la posicion del registro en el listado o timeline.</p>
                    @error('orden')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="estatus" class="mb-1 block text-sm font-medium text-[#3e2d31]">Estatus</label>
                    <select id="estatus" name="estatus"
                        class="w-full rounded-2xl border {{ $errors->has('estatus') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                        <option value="1" @selected(old('estatus', (string) ($timeline->estatus ?? '1')) == '1')>Activo</option>
                        <option value="0" @selected(old('estatus', (string) ($timeline->estatus ?? '1')) == '0')>Inactivo</option>
                    </select>
                    @error('estatus')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-[#efe6dd] pt-4 sm:flex-row sm:justify-end">
                <a href="{{ route('admin.timeline') }}"
                    class="inline-flex items-center justify-center rounded-full bg-slate-100 px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">
                    Cancelar
                </a>
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-full bg-[#63102a] px-5 py-2.5 text-sm font-semibold text-white shadow-[0_10px_24px_rgba(99,16,42,0.22)] transition hover:bg-[#7f173c]">
                    {{ $isEdit ? 'Actualizar Registro' : 'Guardar Registro' }}
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const setupPreview = (inputId, previewId, wrapperId) => {
                const input = document.getElementById(inputId);
                const preview = document.getElementById(previewId);
                const wrapper = document.getElementById(wrapperId);
                let previewUrl = null;

                if (!input || !preview || !wrapper) {
                    return;
                }

                if (preview.getAttribute('src')) {
                    preview.dataset.originalSrc = preview.getAttribute('src');
                }

                input.addEventListener('change', function(event) {
                    const file = event.target.files?.[0];

                    if (previewUrl) {
                        URL.revokeObjectURL(previewUrl);
                        previewUrl = null;
                    }

                    if (!file) {
                        if (!preview.dataset.originalSrc) {
                            wrapper.classList.add('hidden');
                        }

                        preview.src = preview.dataset.originalSrc || '';
                        return;
                    }

                    previewUrl = URL.createObjectURL(file);
                    preview.src = previewUrl;
                    wrapper.classList.remove('hidden');
                });
            };

            setupPreview('imagen_antes', 'imagen-antes-preview', 'imagen-antes-preview-wrapper');
            setupPreview('imagen_despues', 'imagen-despues-preview', 'imagen-despues-preview-wrapper');
        });
    </script>
@endpush
