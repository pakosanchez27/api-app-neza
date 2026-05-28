@extends('layouts.app')
@section('title', $isEditing ? 'Editar categoria evento' : 'Crear categoria evento')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="overflow-hidden rounded-[26px] border border-[#eadde2] bg-white shadow-[0_18px_40px_rgba(97,18,50,0.07)]">
            <div class="bg-[linear-gradient(135deg,#2f1821,#61102a)] px-6 py-6 text-white">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#ffd175]">Catalogos</p>
                <h2 class="mt-3 text-2xl font-semibold">{{ $isEditing ? 'Editar categoria evento' : 'Crear categoria evento' }}</h2>
            </div>

            <form method="POST"
                action="{{ $isEditing ? route('admin.catalogos.categorias-eventos.update', $categoria) : route('admin.catalogos.categorias-eventos.store') }}"
                class="space-y-5 p-6">
                @csrf
                @if ($isEditing)
                    @method('PUT')
                @endif

                <div>
                    <label for="nombre" class="mb-1 block text-sm font-medium text-[#3e2d31]">Nombre</label>
                    <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $categoria->nombre) }}"
                        class="w-full rounded-2xl border {{ $errors->has('nombre') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    @error('nombre')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="slug" class="mb-1 block text-sm font-medium text-[#3e2d31]">Slug</label>
                    <input type="text" id="slug" name="slug" value="{{ old('slug', $categoria->slug) }}"
                        class="w-full rounded-2xl border {{ $errors->has('slug') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                    <p class="mt-2 text-xs text-[#7d6870]">Si lo dejas vacio, se genera automaticamente desde el nombre.</p>
                    @error('slug')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="descripcion" class="mb-1 block text-sm font-medium text-[#3e2d31]">Descripcion</label>
                    <textarea id="descripcion" name="descripcion" rows="5"
                        class="w-full rounded-2xl border {{ $errors->has('descripcion') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">{{ old('descripcion', $categoria->descripcion) }}</textarea>
                    @error('descripcion')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col gap-3 border-t border-[#efe6dd] pt-5 sm:flex-row sm:justify-end">
                    <a href="{{ route('admin.catalogos.categorias-eventos') }}"
                        class="inline-flex items-center justify-center rounded-[14px] border border-[#d8c6cb] px-5 py-3 text-sm font-semibold text-[#5d4450] transition hover:bg-[#faf5f7]">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-[14px] bg-[#63102a] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#7a2144]">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
