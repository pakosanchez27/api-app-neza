@extends('layouts.app')
@section('title', 'Detalle comercio')

@push('styles')
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
            padding: 1.25rem;
            box-shadow: 0 12px 30px rgba(97, 18, 50, 0.05);
        }
    </style>
@endpush

@section('content')
    @php
        $nombreUsuario = trim(implode(' ', array_filter([
            $user->nombre_p,
            $user->app_p,
            $user->apm_p,
        ])));
        $nombreUsuario = $nombreUsuario !== '' ? $nombreUsuario : ($user->name ?: 'Sin nombre');
        $fotoPerfil = $user->foto_perfil ? \App\Support\ImageManager::storageUrl($user->foto_perfil) : null;
        $contacto = $establecimiento?->contacto;
        $domicilio = $establecimiento?->domicilio;
        $direccion = trim(implode(', ', array_filter([
            $domicilio?->calle,
            $domicilio?->num_ext ? 'Ext. ' . $domicilio->num_ext : null,
            $domicilio?->num_int ? 'Int. ' . $domicilio->num_int : null,
            $domicilio?->colonia,
            $domicilio?->localidad,
            $domicilio?->cp ? 'CP ' . $domicilio->cp : null,
        ])));
    @endphp

    <div class="space-y-5">
        <div class="detail-shell overflow-hidden">
            <div class="bg-[linear-gradient(135deg,#2f1821,#61102a)] px-6 py-6 text-white">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div class="max-w-3xl">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#ffd175]">Detalle de comercio</p>
                        <h2 class="mt-3 text-2xl font-semibold">{{ $establecimiento?->nombre_est ?: 'Comercio sin negocio asociado' }}</h2>
                        <p class="mt-2 text-sm leading-7 text-white/78">Informacion completa del usuario administrador y del establecimiento vinculado.</p>
                    </div>
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('admin.comercios') }}"
                            class="inline-flex items-center justify-center rounded-full bg-white/12 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/18">
                            Volver
                        </a>
                        <a href="{{ route('admin.comercios.edit', $user) }}"
                            class="inline-flex items-center justify-center rounded-full bg-white px-4 py-2 text-sm font-semibold text-[#63102a] shadow-[0_10px_24px_rgba(0,0,0,0.14)] transition hover:bg-[#fff2f5]">
                            Editar usuario
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid gap-5 p-5 lg:grid-cols-[280px_minmax(0,1fr)]">
                <div class="detail-card">
                    <div class="flex flex-col items-center text-center">
                        @if ($fotoPerfil)
                            <img src="{{ $fotoPerfil }}" alt="Foto de {{ $nombreUsuario }}"
                                class="h-28 w-28 rounded-full border border-[#eadde2] object-cover shadow-sm">
                        @else
                            <div class="flex h-28 w-28 items-center justify-center rounded-full bg-[#f3e6db] text-2xl font-semibold uppercase text-[#7a2144]">
                                {{ collect(explode(' ', $nombreUsuario))->filter()->map(fn ($segmento) => mb_substr($segmento, 0, 1))->take(2)->implode('') ?: 'SN' }}
                            </div>
                        @endif
                        <h3 class="mt-4 text-lg font-semibold text-[#201815]">{{ $nombreUsuario }}</h3>
                        <p class="mt-1 text-sm {{ $user->activo ? 'text-emerald-700' : 'text-rose-700' }}">
                            {{ $user->activo ? 'Cuenta activa' : 'Cuenta inactiva' }}
                        </p>
                    </div>
                </div>

                <div class="grid gap-5 xl:grid-cols-2">
                    <div class="detail-card">
                        <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[#8d6b10]">Usuario</p>
                        <dl class="mt-4 grid gap-3 text-sm text-[#201815] sm:grid-cols-[150px_1fr]">
                            <dt class="font-semibold">Nombre</dt>
                            <dd>{{ $nombreUsuario }}</dd>
                            <dt class="font-semibold">Numero</dt>
                            <dd>{{ $user->telefono ?: 'Sin numero' }}</dd>
                            <dt class="font-semibold">Correo</dt>
                            <dd>{{ $user->email ?: 'Sin correo' }}</dd>
                            <dt class="font-semibold">Registro</dt>
                            <dd>{{ optional($user->created_at)->format('Y-m-d H:i') ?: 'Sin fecha' }}</dd>
                            <dt class="font-semibold">Ultimo acceso</dt>
                            <dd>{{ optional($user->ultimo_acceso)->format('Y-m-d') ?: 'Sin registro' }}</dd>
                        </dl>
                    </div>

                    <div class="detail-card">
                        <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[#8d6b10]">Comercio</p>
                        <dl class="mt-4 grid gap-3 text-sm text-[#201815] sm:grid-cols-[150px_1fr]">
                            <dt class="font-semibold">Negocio</dt>
                            <dd>{{ $establecimiento?->nombre_est ?: 'Sin negocio' }}</dd>
                            <dt class="font-semibold">Razon social</dt>
                            <dd>{{ $establecimiento?->razon_social ?: 'Sin razon social' }}</dd>
                            <dt class="font-semibold">Tipo</dt>
                            <dd>{{ $establecimiento?->tipo?->nombre ?: 'Sin tipo' }}</dd>
                            <dt class="font-semibold">Aforo</dt>
                            <dd>{{ $establecimiento?->aforo ?? 'Sin dato' }}</dd>
                            <dt class="font-semibold">Visible</dt>
                            <dd>{{ $establecimiento ? ($establecimiento->is_visible ? 'Visible' : 'No visible') : 'Sin negocio' }}</dd>
                            <dt class="font-semibold">Estatus</dt>
                            <dd>{{ $establecimiento ? ($establecimiento->estatus ? 'Activo' : 'Inactivo') : 'Sin negocio' }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-5 xl:grid-cols-2">
            <div class="detail-card">
                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[#8d6b10]">Contacto publico</p>
                <dl class="mt-4 grid gap-3 text-sm text-[#201815] sm:grid-cols-[150px_1fr]">
                    <dt class="font-semibold">Telefono</dt>
                    <dd>{{ $contacto?->telefono ?: 'Sin telefono' }}</dd>
                    <dt class="font-semibold">Correo</dt>
                    <dd>{{ $contacto?->correo ?: 'Sin correo' }}</dd>
                    <dt class="font-semibold">Facebook</dt>
                    <dd>{{ $contacto?->facebook ?: 'Sin dato' }}</dd>
                    <dt class="font-semibold">Instagram</dt>
                    <dd>{{ $contacto?->instagram ?: 'Sin dato' }}</dd>
                    <dt class="font-semibold">TikTok</dt>
                    <dd>{{ $contacto?->tiktok ?: 'Sin dato' }}</dd>
                </dl>
            </div>

            <div class="detail-card">
                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[#8d6b10]">Domicilio</p>
                <dl class="mt-4 grid gap-3 text-sm text-[#201815] sm:grid-cols-[150px_1fr]">
                    <dt class="font-semibold">Direccion</dt>
                    <dd>{{ $direccion ?: 'Sin direccion' }}</dd>
                    <dt class="font-semibold">Referencias</dt>
                    <dd>{{ $domicilio?->referencias ?: 'Sin referencias' }}</dd>
                    <dt class="font-semibold">Latitud</dt>
                    <dd>{{ $domicilio?->latitud ?? 'Sin coordenada' }}</dd>
                    <dt class="font-semibold">Longitud</dt>
                    <dd>{{ $domicilio?->longitud ?? 'Sin coordenada' }}</dd>
                </dl>
            </div>
        </div>

        <div class="detail-card">
            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[#8d6b10]">Descripcion</p>
            <p class="mt-4 text-sm leading-7 text-[#201815]">
                {{ $establecimiento?->descripcion ?: 'Sin descripcion registrada.' }}
            </p>
        </div>

        <div class="detail-card">
            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[#8d6b10]">Amenidades</p>
            <div class="mt-4 flex flex-wrap gap-2">
                @forelse ($establecimiento?->amenidades ?? [] as $amenidad)
                    <span class="inline-flex rounded-full bg-[#f3e6db] px-3 py-1 text-xs font-semibold text-[#7a2144]">
                        {{ $amenidad->nombre ?? $amenidad->descripcion ?? 'Amenidad' }}
                    </span>
                @empty
                    <p class="text-sm text-[#7d6870]">Sin amenidades registradas.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
