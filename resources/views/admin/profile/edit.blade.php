@extends('layouts.app')
@section('title', 'Mi perfil')

@section('content')
    @php
        $name = old('name', $user['name'] ?? $user['nombre'] ?? '');
        $email = old('email', $user['email'] ?? '');
    @endphp

    <div class="mx-auto max-w-5xl space-y-5 px-3 sm:px-5">
        <div class="overflow-hidden rounded-[26px] border border-[#eadde2] bg-white shadow-[0_18px_40px_rgba(97,18,50,0.07)]">
            <div class="bg-[linear-gradient(135deg,#2f1821,#61102a)] px-6 py-6 text-white">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#ffd175]">Cuenta</p>
                <h1 class="mt-3 text-2xl font-semibold">Mi perfil</h1>
                <p class="mt-2 text-sm leading-7 text-white/78">Actualiza tu informacion personal o cambia tu contrasena.</p>
            </div>

            <div class="space-y-7 px-6 py-6">
            <form method="POST" action="{{ route('admin.perfil.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                @error('profile')
                    <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ $message }}</div>
                @enderror

                <section>
                    <h2 class="text-lg font-semibold text-[#3e2d31]">Informacion personal</h2>
                    <div class="mt-4 grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="name" class="mb-1 block text-sm font-medium text-[#3e2d31]">Nombre completo</label>
                            <input id="name" name="name" type="text" value="{{ $name }}" required autocomplete="name"
                                class="w-full rounded-2xl border {{ $errors->has('name') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                            @error('name')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="email" class="mb-1 block text-sm font-medium text-[#3e2d31]">Correo electronico</label>
                            <input id="email" name="email" type="email" value="{{ $email }}" required autocomplete="email"
                                class="w-full rounded-2xl border {{ $errors->has('email') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                            @error('email')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </section>

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center justify-center rounded-[14px] bg-[#63102a] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#7a2144]">
                        Guardar informacion
                    </button>
                </div>
            </form>

            <form method="POST" action="{{ route('admin.perfil.password') }}" class="border-t border-[#efe6dd] pt-6">
                @csrf
                @method('PUT')
                <section>
                    <h2 class="text-lg font-semibold text-[#3e2d31]">Cambiar contrasena</h2>
                    <p class="mt-1 text-sm text-[#7d6870]">Deja estos campos vacios si deseas conservar tu contrasena actual.</p>
                    <div class="mt-4 grid gap-5 md:grid-cols-3">
                        <div>
                            <label for="current_password" class="mb-1 block text-sm font-medium text-[#3e2d31]">Contrasena actual</label>
                            <input id="current_password" name="current_password" type="password" autocomplete="current-password"
                                class="w-full rounded-2xl border {{ $errors->has('current_password') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                            @error('current_password')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="password" class="mb-1 block text-sm font-medium text-[#3e2d31]">Nueva contrasena</label>
                            <input id="password" name="password" type="password" autocomplete="new-password"
                                class="w-full rounded-2xl border {{ $errors->has('password') ? 'border-rose-400 bg-rose-50' : 'border-[#e8d9cb] bg-[#fffdfa]' }} px-4 py-3 text-sm outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                            @error('password')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                            @error('password_update')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="mb-1 block text-sm font-medium text-[#3e2d31]">Confirmar nueva contrasena</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                                class="w-full rounded-2xl border border-[#e8d9cb] bg-[#fffdfa] px-4 py-3 text-sm outline-none transition focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/15">
                        </div>
                    </div>
                </section>

                <div class="mt-5 flex justify-end">
                    <button type="submit" class="inline-flex items-center justify-center rounded-[14px] bg-[#63102a] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#7a2144]">
                        Cambiar contrasena
                    </button>
                </div>
            </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const alertData = @json(session('sweet_alert'));
            const validationMessage = @json($errors->first());

            if (typeof Swal === 'undefined') {
                return;
            }

            if (alertData) {
                Swal.fire({
                    icon: alertData.icon || 'info',
                    title: alertData.title || 'Aviso',
                    text: alertData.text || '',
                    confirmButtonColor: '#63102a'
                });
            } else if (validationMessage) {
                Swal.fire({
                    icon: 'error',
                    title: 'Revisa la informacion',
                    text: validationMessage,
                    confirmButtonColor: '#63102a'
                });
            }
        });
    </script>
@endpush
