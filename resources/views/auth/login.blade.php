<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ExploraNeza Admin | Acceso</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-up {
            animation: fadeUp .65s ease-out both;
        }
    </style>
</head>

<body
    class="min-h-screen bg-[radial-gradient(circle_at_top_left,rgba(188,149,92,0.16),transparent_28%),radial-gradient(circle_at_bottom_right,rgba(99,16,42,0.14),transparent_30%),linear-gradient(180deg,#fffdfa_0%,#fbf5eb_100%)] text-[#201815] antialiased">
    @php
        $landingUrl = route('landing');
        $logoLanding = asset('img/landing/logo-landing.png');
    @endphp

    <main class="mx-auto flex min-h-screen max-w-7xl items-center justify-center px-4 py-6 sm:px-6 lg:px-8">
        <section
            class="w-full max-w-md rounded-[30px] border border-[#63102a]/10 bg-white/90 px-5 py-6 shadow-[0_30px_70px_rgba(99,16,42,0.14)] backdrop-blur-xl sm:px-8 sm:py-8">
            <div class="flex items-center justify-between gap-4">
                <a href="{{ $landingUrl }}"
                    class="inline-flex items-center rounded-2xl bg-[#fff6ea] px-3 py-2 transition hover:bg-[#f8ecdc]">
                    <img src="{{ $logoLanding }}" alt="ExploraNeza" class="h-10 w-auto">
                </a>
                <span
                    class="inline-flex items-center gap-2 rounded-full border border-[#63102a]/10 bg-[#fff8ef] px-4 py-2 text-[11px] font-bold uppercase tracking-[0.2em] text-[#63102a]">
                    <span class="h-2 w-2 rounded-full bg-[#bc955c]"></span>
                    Admin
                </span>
            </div>

            <div class="mt-8 animate-fade-up">
                <p class="text-[12px] font-semibold uppercase tracking-[0.18em] text-[#9c7a44]">
                    Acceso seguro
                </p>
                <h1 class="mt-3 text-3xl font-black tracking-tight text-[#201815] sm:text-[2.35rem]">
                    Inicia sesion
                </h1>
                <p class="mt-4 text-[15px] leading-7 text-[#725f66]">
                    Entra al panel de administracion de la app con tu cuenta autorizada.
                </p>
            </div>

            @if (session('status'))
                <div
                    class="mt-6 rounded-[20px] border border-[#b6dbc8] bg-[#f2fbf6] px-4 py-4 text-sm font-medium leading-6 text-[#1f6a4f]">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div
                    class="mt-6 rounded-[20px] border border-[#e3b7c3] bg-[#fff4f7] px-4 py-4 text-sm font-medium leading-6 text-[#8a2044]">
                    {{ $errors->first() }}
                </div>
            @endif


            <form class="mt-6 space-y-5" method="POST" action="{{ route('admin.login.store') }}">
                @csrf

                <div>
                    <label for="email" class="mb-2 block text-[13px] font-semibold text-[#4b3940]">
                        Correo electronico
                    </label>
                    <input id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}"
                        placeholder="admin@negocio.com"
                        class="h-14 w-full rounded-[18px] border border-[#e4d4b8] bg-[#fffaf4] px-4 text-[15px] text-[#24181d] outline-none transition focus:border-[#63102a] focus:bg-white"
                        required>
                </div>

                <div>
                    <div class="mb-2 flex items-center justify-between gap-3">
                        <label for="password" class="block text-[13px] font-semibold text-[#4b3940]">
                            Contrasena
                        </label>
                        <span class="text-[12px] font-medium text-[#8a6e77]">Minimo 8 caracteres</span>
                    </div>
                    <div class="relative">
                        <input id="password" name="password" type="password" autocomplete="current-password"
                            placeholder="********"
                            class="h-14 w-full rounded-[18px] border border-[#e4d4b8] bg-[#fffaf4] px-4 pr-14 text-[15px] text-[#24181d] outline-none transition focus:border-[#63102a] focus:bg-white"
                            required>
                        <button id="toggle-password" type="button"
                            class="absolute inset-y-0 right-3 my-auto inline-flex h-10 w-10 items-center justify-center rounded-full text-[#63102a] transition hover:bg-[#f8ecdc]"
                            aria-label="Mostrar u ocultar contrasena">
                            <svg id="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7S3.732 16.057 2.458 12Z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z" />
                            </svg>
                            <svg id="eye-closed" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" class="hidden h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m3 3 18 18M10.584 10.587A2 2 0 0 0 13.414 13.4" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9.88 5.09A10.94 10.94 0 0 1 12 4.91c4.478 0 8.27 2.943 9.543 7a10.96 10.96 0 0 1-3.228 4.568M6.228 6.235C4.655 7.418 3.438 9.09 2.458 12c1.274 4.057 5.065 7 9.542 7 1.61 0 3.138-.381 4.493-1.057" />
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit"
                    class="inline-flex h-14 w-full items-center justify-center rounded-full bg-[#63102a] px-5 text-[15px] font-bold text-white shadow-[0_18px_34px_rgba(99,16,42,0.22)] transition hover:-translate-y-0.5 hover:bg-[#4f0c22]">
                    Entrar al panel
                </button>
            </form>

            <a href="{{ $landingUrl }}"
                class="mt-5 inline-flex w-full items-center justify-center rounded-full border border-[#63102a]/14 px-4 py-3 text-[13px] font-bold text-[#63102a] transition hover:bg-[#63102a] hover:text-white">
                Volver al sitio
            </a>
        </section>
    </main>

    <script>
        const passwordInput = document.getElementById('password');
        const togglePasswordButton = document.getElementById('toggle-password');
        const eyeOpenIcon = document.getElementById('eye-open');
        const eyeClosedIcon = document.getElementById('eye-closed');

        togglePasswordButton?.addEventListener('click', () => {
            const shouldShowPassword = passwordInput.type === 'password';
            passwordInput.type = shouldShowPassword ? 'text' : 'password';
            eyeOpenIcon.classList.toggle('hidden', shouldShowPassword);
            eyeClosedIcon.classList.toggle('hidden', !shouldShowPassword);
        });
    </script>
</body>

</html>
