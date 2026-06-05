@extends('layouts.app')
@section('title', 'Comercios')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
    <style>
        .admin-shell {
            border: 1px solid rgba(97, 16, 42, 0.08);
            border-radius: 26px;
            background: #fff;
            box-shadow: 0 18px 40px rgba(97, 18, 50, 0.07);
        }

        .admin-stat {
            border: 1px solid rgba(97, 16, 42, 0.08);
            border-radius: 22px;
            background: linear-gradient(180deg, #fff, #fff8f3);
            padding: 1.1rem 1.25rem;
        }

        .admin-table table.dataTable thead th {
            color: #6b5560;
            font-size: 12px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .admin-table .dataTables_wrapper .dataTables_filter input,
        .admin-table .dataTables_wrapper .dataTables_length select {
            border: 1px solid #e7d8dc;
            border-radius: 999px;
            padding: 0.45rem 0.85rem;
            background: #fff;
        }

    </style>
@endpush

@section('content')
    @php
        $usuarios = $usuarios ?? collect();
        $usuariosActivos = $usuarios->where('activo', true)->count();
        $usuariosConNegocio = $usuarios->filter(fn ($usuario) => $usuario->establecimientos->isNotEmpty())->count();
        $comerciosVisibles = $usuarios->filter(function ($usuario) {
            $establecimiento = $usuario->establecimientos->first();

            return $establecimiento && (bool) $establecimiento->is_visible && (bool) $establecimiento->estatus;
        })->count();
    @endphp

    <div class="admin-shell mb-5 overflow-hidden">
        <div class="bg-[linear-gradient(135deg,#2f1821,#61102a)] px-6 py-6 text-white">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#ffd175]">Registros comerciales</p>
                    <h2 class="mt-3 text-2xl font-semibold">Comercios</h2>
                    <p class="mt-2 text-sm leading-7 text-white/78">Administra las cuentas de comercios y consulta rapidamente los datos del negocio asociado.</p>
                </div>
            </div>
        </div>

        <div class="grid gap-4 px-6 py-5 md:grid-cols-3">
            <div class="admin-stat">
                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[#8d6b10]">Total</p>
                <p class="mt-3 text-4xl font-bold leading-none text-[#201815]">{{ $usuarios->count() }}</p>
                <p class="mt-2 text-sm text-[#6d5a62]">Usuarios con rol comercio registrados.</p>
            </div>
            <div class="admin-stat">
                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-emerald-700">Activos</p>
                <p class="mt-3 text-4xl font-bold leading-none text-[#201815]">{{ $usuariosActivos }}</p>
                <p class="mt-2 text-sm text-[#6d5a62]">Cuentas habilitadas para ingresar.</p>
            </div>
            <div class="admin-stat">
                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-sky-700">Con negocio visible</p>
                <p class="mt-3 text-4xl font-bold leading-none text-[#201815]">{{ $comerciosVisibles }}</p>
                <p class="mt-2 text-sm text-[#6d5a62]">{{ $usuariosConNegocio }} cuentas tienen al menos un establecimiento asociado.</p>
            </div>
        </div>
    </div>

    <div class="admin-shell admin-table p-5">
        <div class="mb-5">
            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[#8d6b10]">Listado</p>
            <h3 class="mt-2 text-lg font-semibold text-[#201815]">Cuentas de comercios</h3>
            <p class="mt-1 text-sm text-[#7d6870]">Busca, ordena y abre el detalle del comercio o del usuario asociado.</p>
        </div>

        <div class="overflow-x-auto">
            <table id="tabla-comercios" class="display stripe hover w-full text-sm">
                <thead>
                    <tr>
                        <th>Foto perfil</th>
                        <th>Nombre</th>
                        <th>Nombre de negocio</th>
                        <th>Numero</th>
                        <th>Correo</th>
                        <th>Fecha de registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($usuarios as $usuario)
                        @php
                            $nombre = trim(implode(' ', array_filter([$usuario->nombre_p, $usuario->app_p, $usuario->apm_p])));
                            $nombre = $nombre !== '' ? $nombre : ($usuario->name ?: 'Sin nombre');
                            $iniciales = collect(explode(' ', $nombre))->filter()->map(fn ($segmento) => mb_substr($segmento, 0, 1))->take(2)->implode('');
                            $fotoPerfil = $usuario->foto_perfil ? \App\Support\ImageManager::storageUrl($usuario->foto_perfil) : null;
                            $establecimiento = $usuario->establecimientos->first();
                        @endphp
                        <tr>
                            <td>
                                <div class="flex min-w-[88px] justify-center">
                                    @if ($fotoPerfil)
                                        <img src="{{ $fotoPerfil }}" alt="Foto de {{ $nombre }}" class="h-14 w-14 rounded-full border border-[#eadde2] object-cover">
                                    @else
                                        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-[#f3e6db] text-sm font-semibold uppercase text-[#7a2144]">
                                            {{ $iniciales ?: 'SN' }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="min-w-[220px]">
                                    <p class="font-semibold text-[#201815]">{{ $nombre }}</p>
                                    <p class="mt-1 text-xs {{ $usuario->activo ? 'text-emerald-700' : 'text-rose-700' }}">{{ $usuario->activo ? 'Activo' : 'Inactivo' }}</p>
                                </div>
                            </td>
                            <td>{{ $establecimiento?->nombre_est ?: 'Sin negocio' }}</td>
                            <td>{{ $usuario->telefono ?: 'Sin numero' }}</td>
                            <td>{{ $usuario->email ?: 'Sin correo' }}</td>
                            <td>{{ optional($usuario->created_at)->format('Y-m-d H:i') ?: 'Sin fecha' }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.comercios.show', $usuario) }}"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-700 transition hover:bg-slate-200"
                                        title="Ver" aria-label="Ver detalle del comercio">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.644C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.433 0 .644C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.comercios.edit', $usuario) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-sky-100 text-sky-700 transition hover:bg-sky-200" title="Editar" aria-label="Editar usuario comercio">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487a2.25 2.25 0 1 1 3.182 3.182L8.25 19.462 4.5 20.5l1.038-3.75L16.862 4.487Z" />
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.comercios.toggle-status', $usuario) }}" class="form-toggle-comercio inline-flex" data-usuario-nombre="{{ $nombre }}" data-is-active="{{ $usuario->activo ? '1' : '0' }}" data-action-label="{{ $usuario->activo ? 'desactivar' : 'activar' }}" data-action-title="{{ $usuario->activo ? 'Desactivar usuario' : 'Activar usuario' }}" data-action-text="{{ $usuario->activo ? 'El usuario dejara de poder iniciar sesion.' : 'El usuario volvera a poder iniciar sesion.' }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="motivo_desactivacion" value="">
                                        <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-full {{ $usuario->activo ? 'bg-rose-100 text-rose-700 hover:bg-rose-200' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' }} transition" title="{{ $usuario->activo ? 'Desactivar' : 'Activar' }}" aria-label="{{ $usuario->activo ? 'Desactivar usuario' : 'Activar usuario' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                                @if ($usuario->activo)
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 7.5h12m-9.75 0V6a1.5 1.5 0 0 1 1.5-1.5h4.5A1.5 1.5 0 0 1 15.75 6v1.5m-7.5 0v10.125A1.875 1.875 0 0 0 10.125 19.5h3.75a1.875 1.875 0 0 0 1.875-1.875V7.5M10.5 10.5v6m3-6v6" />
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                                @endif
                                            </svg>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.comercios.reset-password', $usuario) }}" class="form-reset-comercio inline-flex" data-usuario-nombre="{{ $nombre }}">
                                        @csrf
                                        <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-amber-100 text-amber-700 transition hover:bg-amber-200" title="Reset password" aria-label="Enviar reset password">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A3.75 3.75 0 0 0 12 1.5 3.75 3.75 0 0 0 8.25 5.25V9m-.75 0h9a1.5 1.5 0 0 1 1.5 1.5v8.25a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 6 18.75V10.5A1.5 1.5 0 0 1 7.5 9Z" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(function() {
            const successMessage = @json(session('success'));
            const errorMessage = @json(session('error'));

            $('#tabla-comercios').DataTable({
                language: { url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json' },
                pageLength: 10,
                order: [[5, 'desc']]
            });

            if (successMessage) {
                Swal.fire({ icon: 'success', title: 'Operacion completada', text: successMessage, confirmButtonColor: '#63102a' });
            }

            if (errorMessage) {
                Swal.fire({ icon: 'error', title: 'No fue posible completar la accion', text: errorMessage, confirmButtonColor: '#63102a' });
            }

            $('.form-toggle-comercio').on('submit', function(event) {
                event.preventDefault();
                const form = this;
                const nombre = form.dataset.usuarioNombre || 'este usuario';
                const isActive = form.dataset.isActive === '1';
                const actionLabel = form.dataset.actionLabel || 'actualizar';
                const actionTitle = form.dataset.actionTitle || 'Actualizar usuario';
                const actionText = form.dataset.actionText || 'Se actualizara el usuario.';
                const reasonInput = form.querySelector('input[name="motivo_desactivacion"]');

                if (isActive) {
                    Swal.fire({
                        icon: 'warning',
                        title: actionTitle,
                        input: 'textarea',
                        inputLabel: `Motivo de desactivacion para ${nombre}`,
                        inputPlaceholder: 'Escribe por que se desactivara la cuenta...',
                        inputAttributes: { 'aria-label': 'Motivo de desactivacion' },
                        showCancelButton: true,
                        confirmButtonText: `Si, ${actionLabel}`,
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#63102a',
                        cancelButtonColor: '#64748b',
                        inputValidator: (value) => !value || !value.trim() ? 'Debes escribir el motivo de la desactivacion.' : null
                    }).then((result) => {
                        if (result.isConfirmed) {
                            if (reasonInput) reasonInput.value = result.value.trim();
                            form.submit();
                        }
                    });
                    return;
                }

                Swal.fire({
                    icon: 'warning',
                    title: actionTitle,
                    text: `${actionText} (${nombre})`,
                    showCancelButton: true,
                    confirmButtonText: `Si, ${actionLabel}`,
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#63102a',
                    cancelButtonColor: '#64748b'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });

            $('.form-reset-comercio').on('submit', function(event) {
                event.preventDefault();
                const form = this;
                const nombre = form.dataset.usuarioNombre || 'este usuario';
                Swal.fire({
                    icon: 'question',
                    title: 'Enviar reset password',
                    text: `Se enviara un correo de restablecimiento a ${nombre}.`,
                    showCancelButton: true,
                    confirmButtonText: 'Si, enviar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#b45309',
                    cancelButtonColor: '#64748b'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    </script>
@endpush
