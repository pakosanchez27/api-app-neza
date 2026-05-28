@extends('layouts.app')
@section('title', 'Editar comercio')

@section('content')
    @php
        $nombreCompleto = trim(implode(' ', array_filter([
            old('nombre_p', $user->nombre_p),
            old('app_p', $user->app_p),
            old('apm_p', $user->apm_p),
        ])));
        $nombreCompleto = $nombreCompleto !== '' ? $nombreCompleto : ($user->name ?: 'Usuario');
        $fotoPerfil = $user->foto_perfil ? \App\Support\ImageManager::storageUrl($user->foto_perfil) : null;
    @endphp

    <div class="mx-auto max-w-5xl space-y-5">
        <div class="overflow-hidden rounded-[26px] border border-[#eadde2] bg-white shadow-[0_18px_40px_rgba(97,18,50,0.07)]">
            <div class="bg-[linear-gradient(135deg,#2f1821,#61102a)] px-6 py-6 text-white">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#ffd175]">Edicion de perfil</p>
                <h2 class="mt-3 text-2xl font-semibold">Editar comercio</h2>
                <p class="mt-2 text-sm leading-7 text-white/78">Actualiza los datos principales de la cuenta del comercio.</p>
            </div>

            <div class="grid gap-6 px-6 py-6 lg:grid-cols-[220px_1fr]">
                <div class="rounded-[24px] border border-[#efe2d7] bg-[#fffaf6] p-5">
                    <div class="flex flex-col items-center text-center">
                        @if ($fotoPerfil)
                            <img src="{{ $fotoPerfil }}" alt="Foto de {{ $nombreCompleto }}" class="h-28 w-28 rounded-full border border-[#eadde2] object-cover shadow-sm">
                        @else
                            <div class="flex h-28 w-28 items-center justify-center rounded-full bg-[#f3e6db] text-2xl font-semibold uppercase text-[#7a2144]">
                                {{ collect(explode(' ', $nombreCompleto))->filter()->map(fn ($segmento) => mb_substr($segmento, 0, 1))->take(2)->implode('') ?: 'SN' }}
                            </div>
                        @endif
                        <p class="mt-4 text-lg font-semibold text-[#201815]">{{ $nombreCompleto }}</p>
                        <p class="mt-1 text-sm {{ $user->activo ? 'text-emerald-700' : 'text-rose-700' }}">{{ $user->activo ? 'Cuenta activa' : 'Cuenta inactiva' }}</p>
                        <p class="mt-3 text-xs text-[#7d6870]">Registro: {{ optional($user->created_at)->format('Y-m-d H:i') ?: 'Sin fecha' }}</p>
                    </div>
                </div>

                <div class="rounded-[24px] border border-[#efe2d7] bg-white p-5">
                    <form method="POST" action="{{ route('admin.comercios.update', $user) }}" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label for="nombre_p" class="mb-1 block text-sm font-medium text-[#3e2d31]">Nombre</label>
                                <input type="text" id="nombre_p" name="nombre_p" value="{{ old('nombre_p', $user->nombre_p) }}" class="w-full rounded-2xl border {{ $errors->has('nombre_p') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                                @error('nombre_p') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="app_p" class="mb-1 block text-sm font-medium text-[#3e2d31]">Apellido paterno</label>
                                <input type="text" id="app_p" name="app_p" value="{{ old('app_p', $user->app_p) }}" class="w-full rounded-2xl border {{ $errors->has('app_p') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                                @error('app_p') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="apm_p" class="mb-1 block text-sm font-medium text-[#3e2d31]">Apellido materno</label>
                                <input type="text" id="apm_p" name="apm_p" value="{{ old('apm_p', $user->apm_p) }}" class="w-full rounded-2xl border {{ $errors->has('apm_p') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                                @error('apm_p') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="telefono" class="mb-1 block text-sm font-medium text-[#3e2d31]">Numero</label>
                                <input type="text" id="telefono" name="telefono" value="{{ old('telefono', $user->telefono) }}" class="w-full rounded-2xl border {{ $errors->has('telefono') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                                @error('telefono') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="email" class="mb-1 block text-sm font-medium text-[#3e2d31]">Correo</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="w-full rounded-2xl border {{ $errors->has('email') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm text-[#201815] outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                            @error('email') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-3 border-t border-[#efe6dd] pt-5 sm:flex-row sm:justify-end">
                            <a href="{{ route('admin.comercios') }}" class="inline-flex items-center justify-center rounded-[14px] border border-[#d8c6cb] px-5 py-3 text-sm font-semibold text-[#5d4450] transition hover:bg-[#faf5f7]">Cancelar</a>
                            <button type="submit" class="inline-flex items-center justify-center rounded-[14px] bg-[#63102a] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#7a2144]">Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
