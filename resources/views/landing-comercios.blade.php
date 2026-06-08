
<!DOCTYPE html>
<html lang="es" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExploraNeza Comercios | Haz visible tu negocio en la app de la ciudad</title>
    <meta name="description"
        content="Registra tu comercio en ExploraNeza y administra tu información, horarios, fotos, cupones y participación en recorridos desde un panel pensado para negocios locales.">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @keyframes floatSoft {
            0%, 100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-12px);
            }
        }

        @keyframes fadeLift {
            from {
                opacity: 0;
                transform: translateY(22px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideShine {
            0% {
                transform: translateX(-140%);
            }

            100% {
                transform: translateX(140%);
            }
        }

        .fx-float {
            animation: floatSoft 6s ease-in-out infinite;
        }

        .fx-appear {
            animation: fadeLift .75s ease-out both;
        }

        .glass-card {
            backdrop-filter: blur(16px);
        }

        .shine-wrap {
            position: relative;
            overflow: hidden;
        }

        .shine-wrap::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(110deg, transparent 20%, rgba(255, 255, 255, .24) 48%, transparent 74%);
            transform: translateX(-140%);
            animation: slideShine 5.5s linear infinite;
            pointer-events: none;
        }
    </style>
</head>

<body class="bg-[#fbf5eb] text-[#201815] antialiased">
    @php
        $frontendUrl = rtrim((string) env('FRONTEND_URL', config('app.url')), '/');
        $frontendCommerceRegisterUrl = $frontendUrl . '/auth/comercios/registro';
        $frontendCommerceLoginUrl = $frontendUrl . '/auth/comercios/login';

        $logoLanding = asset('img/landing/logo-landing.png');
        $heroPhone = asset('img/landing/tel-hero.png');
        $heroVector = asset('img/landing/Vector.png');
        $mapPreview = asset('img/landing/mapas.png');
    @endphp

    <div class="relative overflow-hidden bg-[#fbf5eb]" x-data="{ mobileMenu: false }">
        <div
            class="pointer-events-none absolute inset-x-0 top-0 h-[760px] bg-[radial-gradient(circle_at_top_left,rgba(188,149,92,0.18),transparent_34%),radial-gradient(circle_at_top_right,rgba(99,16,42,0.13),transparent_30%)]">
        </div>

        {{-- HEADER --}}
        <header
            class="sticky top-0 z-50 border-b border-[#63102a]/10 bg-white/88 shadow-[0_10px_30px_rgba(99,16,42,0.06)] backdrop-blur-xl">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
                <a href="{{ route('landing.comercios') }}"
                    class="flex shrink-0 items-center rounded-2xl px-2 py-1 transition hover:bg-[#f7ecd8]">
                    <img src="{{ $logoLanding }}" alt="ExploraNeza Comercios" class="h-11 w-auto sm:h-13">
                </a>

                <nav
                    class="hidden items-center gap-1 rounded-full border border-[#63102a]/10 bg-[#fbf5eb]/80 p-1.5 text-[13px] font-bold text-[#201815] shadow-[0_14px_28px_rgba(99,16,42,0.05)] lg:flex">
                    <a href="#inicio" class="rounded-full px-4 py-2 transition hover:bg-white hover:text-[#63102a]">
                        Inicio
                    </a>
                    <a href="#beneficios" class="rounded-full px-4 py-2 transition hover:bg-white hover:text-[#63102a]">
                        Beneficios
                    </a>
                    <a href="#panel" class="rounded-full px-4 py-2 transition hover:bg-white hover:text-[#63102a]">
                        Panel
                    </a>
                    <a href="#modulos" class="rounded-full px-4 py-2 transition hover:bg-white hover:text-[#63102a]">
                        Módulos
                    </a>
                    <a href="#registro" class="rounded-full px-4 py-2 transition hover:bg-white hover:text-[#63102a]">
                        Registro
                    </a>
                </nav>

                <div class="flex items-center gap-2">
                    <a href="{{ route('landing') }}"
                        class="hidden rounded-xl border border-[#63102a]/12 bg-white px-4 py-3 text-sm font-black text-[#63102a] transition hover:bg-[#f7ecd8] sm:inline-flex">
                        Ver app
                    </a>

                    <a href="{{ $frontendCommerceRegisterUrl }}"
                        class="shine-wrap inline-flex items-center justify-center rounded-xl bg-[#63102a] px-4 py-3 text-sm font-black text-white shadow-[0_14px_28px_rgba(99,16,42,0.22)] transition hover:-translate-y-0.5 hover:bg-[#4f0c22] sm:px-5">
                        Registrar comercio
                    </a>

                    <button type="button" @click="mobileMenu = !mobileMenu"
                        class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-[#63102a]/10 bg-white text-[#63102a] lg:hidden">
                        <svg x-show="!mobileMenu" class="h-5 w-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-show="mobileMenu" x-cloak class="h-5 w-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div x-show="mobileMenu" x-transition x-cloak class="border-t border-[#63102a]/8 bg-white lg:hidden">
                <nav class="mx-auto grid max-w-7xl gap-1 px-4 py-4 text-sm font-bold text-[#4f0c22]/80 sm:px-6">
                    <a @click="mobileMenu = false" href="#inicio" class="rounded-xl px-3 py-3 hover:bg-[#fbf5eb]">
                        Inicio
                    </a>
                    <a @click="mobileMenu = false" href="#beneficios" class="rounded-xl px-3 py-3 hover:bg-[#fbf5eb]">
                        Beneficios
                    </a>
                    <a @click="mobileMenu = false" href="#panel" class="rounded-xl px-3 py-3 hover:bg-[#fbf5eb]">
                        Panel
                    </a>
                    <a @click="mobileMenu = false" href="#modulos" class="rounded-xl px-3 py-3 hover:bg-[#fbf5eb]">
                        Módulos
                    </a>
                    <a @click="mobileMenu = false" href="#registro" class="rounded-xl px-3 py-3 hover:bg-[#fbf5eb]">
                        Registro
                    </a>
                    <a @click="mobileMenu = false" href="{{ route('landing') }}"
                        class="rounded-xl px-3 py-3 hover:bg-[#fbf5eb]">
                        Ver landing de usuarios
                    </a>
                </nav>
            </div>
        </header>

        <main>
            {{-- HERO --}}
            <section id="inicio" class="relative overflow-hidden bg-[#63102a] bg-cover bg-center bg-no-repeat"
                style="background-image: url('{{ $heroVector }}');">
                <div
                    class="absolute inset-0 bg-[linear-gradient(90deg,rgba(79,12,34,0.96),rgba(99,16,42,0.9),rgba(99,16,42,0.72))]">
                </div>
                <div
                    class="pointer-events-none absolute left-[8%] top-20 h-36 w-36 rounded-full bg-[#f2cf91]/14 blur-3xl">
                </div>
                <div
                    class="pointer-events-none absolute right-[12%] bottom-20 h-44 w-44 rounded-full bg-white/10 blur-3xl">
                </div>

                <div
                    class="relative mx-auto grid max-w-7xl items-center gap-10 px-4 py-14 sm:px-6 lg:grid-cols-[minmax(0,1fr)_430px] lg:px-8 lg:py-20">
                    <div class="text-white fx-appear">
                        <div
                            class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-4 py-2 text-sm font-black text-[#f2cf91] glass-card">
                            <span class="h-2 w-2 rounded-full bg-[#f2cf91]"></span>
                            ExploraNeza para comercios
                        </div>

                        <h1 class="mt-5 max-w-3xl text-4xl font-black leading-tight tracking-tight sm:text-5xl lg:text-[58px]">
                            Haz visible tu negocio en la app de la ciudad.
                        </h1>

                        <p class="mt-5 max-w-2xl text-lg leading-8 text-white/86">
                            Registra tu comercio, muestra fotos, horarios y ubicación, publica promociones y participa
                            en recorridos con sellos dentro de ExploraNeza.
                        </p>

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ $frontendCommerceRegisterUrl }}"
                                class="shine-wrap inline-flex items-center justify-center rounded-xl bg-[#f2cf91] px-6 py-3.5 text-sm font-black text-[#63102a] shadow-[0_18px_38px_rgba(188,149,92,0.24)] transition hover:-translate-y-0.5 hover:bg-white">
                                Registrar mi comercio
                            </a>

                            <a href="{{ $frontendCommerceLoginUrl }}"
                                class="inline-flex items-center justify-center rounded-xl border border-white/14 bg-white/10 px-6 py-3.5 text-sm font-black text-white transition hover:bg-white/16">
                                Ingresar al panel
                            </a>

                            <a href="{{ route('landing') }}"
                                class="inline-flex items-center justify-center rounded-xl border border-white/14 bg-white/10 px-6 py-3.5 text-sm font-black text-white transition hover:bg-white/16">
                                Ver app para usuarios
                            </a>
                        </div>

                        <div class="mt-10 grid gap-3 sm:grid-cols-3">
                            <div class="rounded-2xl border border-white/10 bg-white/10 p-4 glass-card">
                                <p class="text-[11px] font-black uppercase tracking-[0.14em] text-[#f2cf91]">
                                    Visibilidad
                                </p>
                                <p class="mt-2 text-sm leading-6 text-white/82">
                                    Aparece en el mapa de la app.
                                </p>
                            </div>

                            <div class="rounded-2xl border border-white/10 bg-white/10 p-4 glass-card">
                                <p class="text-[11px] font-black uppercase tracking-[0.14em] text-[#f2cf91]">
                                    Promociones
                                </p>
                                <p class="mt-2 text-sm leading-6 text-white/82">
                                    Publica cupones y beneficios.
                                </p>
                            </div>

                            <div class="rounded-2xl border border-white/10 bg-white/10 p-4 glass-card">
                                <p class="text-[11px] font-black uppercase tracking-[0.14em] text-[#f2cf91]">
                                    Recorridos
                                </p>
                                <p class="mt-2 text-sm leading-6 text-white/82">
                                    Participa con sellos y QR.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="relative flex justify-center lg:justify-end">
                        <div
                            class="pointer-events-none absolute bottom-4 h-14 w-[72%] rounded-full bg-black/30 blur-2xl lg:right-0">
                        </div>

                        <img src="{{ $heroPhone }}" alt="Panel de comercios de ExploraNeza"
                            class="fx-float relative w-full max-w-[330px] drop-shadow-[0_30px_46px_rgba(0,0,0,0.44)] sm:max-w-[370px] lg:max-w-[420px]">
                    </div>
                </div>
            </section>

            {{-- BENEFICIOS RAPIDOS --}}
            <section id="beneficios"
                class="relative overflow-hidden border-y border-[#63102a]/8 bg-[linear-gradient(180deg,#ffffff_0%,#fbf5eb_100%)] py-16">
                <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mx-auto max-w-3xl text-center">
                        <span
                            class="inline-flex rounded-full bg-[#63102a]/8 px-4 py-2 text-[11px] font-black uppercase tracking-[0.18em] text-[#63102a]">
                            Beneficios para tu negocio
                        </span>

                        <h2 class="mt-4 text-3xl font-black leading-tight text-[#201815] sm:text-4xl">
                            Una forma sencilla de conectar con vecinos y visitantes.
                        </h2>

                        <p class="mt-4 text-base leading-7 text-[#4f0c22]/72 sm:text-lg">
                            ExploraNeza ayuda a que tu comercio sea más fácil de encontrar, consultar y visitar.
                        </p>
                    </div>

                    <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <article
                            class="group relative overflow-hidden rounded-[28px] border border-[#63102a]/8 bg-white p-6 shadow-[0_16px_32px_rgba(99,16,42,0.06)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_24px_48px_rgba(99,16,42,0.14)]">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#f7ecd8] text-[#63102a] transition group-hover:scale-105">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>

                            <p class="mt-5 text-[11px] font-black uppercase tracking-[0.16em] text-[#bc955c]">
                                Mapa
                            </p>
                            <h3 class="mt-2 text-xl font-black text-[#201815]">
                                Aparece en la ciudad digital
                            </h3>
                            <p class="mt-3 text-sm leading-6 text-[#4f0c22]/72">
                                Haz que las personas encuentren tu negocio, sepan dónde está y cómo llegar.
                            </p>
                        </article>

                        <article
                            class="group relative overflow-hidden rounded-[28px] border border-[#63102a]/8 bg-white p-6 shadow-[0_16px_32px_rgba(99,16,42,0.06)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_24px_48px_rgba(99,16,42,0.14)]">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#fff0d8] text-[#bc955c] transition group-hover:scale-105">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>

                            <p class="mt-5 text-[11px] font-black uppercase tracking-[0.16em] text-[#bc955c]">
                                Perfil
                            </p>
                            <h3 class="mt-2 text-xl font-black text-[#201815]">
                                Muestra lo mejor de tu local
                            </h3>
                            <p class="mt-3 text-sm leading-6 text-[#4f0c22]/72">
                                Agrega fotos, descripción, horarios, teléfono, ubicación y datos útiles.
                            </p>
                        </article>

                        <article
                            class="group relative overflow-hidden rounded-[28px] border border-[#63102a]/8 bg-white p-6 shadow-[0_16px_32px_rgba(99,16,42,0.06)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_24px_48px_rgba(99,16,42,0.14)]">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#ecf6f2] text-[#235b4e] transition group-hover:scale-105">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-10c1.11 0 2.08.402 2.599 1M12 8V7m0 11v-1m0 0c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                            </div>

                            <p class="mt-5 text-[11px] font-black uppercase tracking-[0.16em] text-[#bc955c]">
                                Cupones
                            </p>
                            <h3 class="mt-2 text-xl font-black text-[#201815]">
                                Publica promociones
                            </h3>
                            <p class="mt-3 text-sm leading-6 text-[#4f0c22]/72">
                                Comparte beneficios y descuentos para atraer visitas a tu establecimiento.
                            </p>
                        </article>

                        <article
                            class="group relative overflow-hidden rounded-[28px] border border-[#63102a]/8 bg-[#63102a] p-6 text-white shadow-[0_18px_38px_rgba(99,16,42,0.18)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_26px_52px_rgba(99,16,42,0.26)]">
                            <div
                                class="absolute right-0 top-0 h-28 w-28 translate-x-1/3 -translate-y-1/3 rounded-full bg-[#f2cf91]/16 blur-2xl">
                            </div>

                            <div
                                class="relative flex h-12 w-12 items-center justify-center rounded-2xl bg-white/12 text-[#f2cf91] transition group-hover:scale-105">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v1m6.364 1.636l-.707.707M20 12h-1M17.657 17.657l-.707-.707M12 20v-1M6.343 17.657l.707-.707M4 12h1M6.343 6.343l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                                </svg>
                            </div>

                            <p class="relative mt-5 text-[11px] font-black uppercase tracking-[0.16em] text-[#f2cf91]">
                                QR y sellos
                            </p>
                            <h3 class="relative mt-2 text-xl font-black text-white">
                                Participa en recorridos
                            </h3>
                            <p class="relative mt-3 text-sm leading-6 text-white/82">
                                Usa códigos QR para registrar visitas en rutas y experiencias locales.
                            </p>
                        </article>
                    </div>
                </div>
            </section>

            {{-- PANEL --}}
            <section id="panel" class="relative overflow-hidden bg-[#fbf5eb] py-20 sm:py-24">
                <div
                    class="pointer-events-none absolute left-0 top-12 h-72 w-72 -translate-x-1/2 rounded-full bg-[#63102a]/8 blur-3xl">
                </div>
                <div
                    class="pointer-events-none absolute right-0 bottom-10 h-72 w-72 translate-x-1/2 rounded-full bg-[#bc955c]/12 blur-3xl">
                </div>

                <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="grid gap-12 lg:grid-cols-12 lg:items-center">
                        <div class="lg:col-span-5">
                            <span
                                class="inline-flex rounded-full bg-[#63102a]/8 px-4 py-2 text-[11px] font-black uppercase tracking-[0.18em] text-[#63102a]">
                                Panel administrativo
                            </span>

                            <h2 class="mt-5 text-4xl font-black leading-tight text-[#201815] sm:text-5xl">
                                Administra cómo se ve tu negocio dentro de la app.
                            </h2>

                            <p class="mt-6 text-lg leading-8 text-[#4f0c22]/72">
                                Desde tu cuenta podrás mantener actualizada la información que verá la población:
                                datos del negocio, horarios, fotos, promociones y participación en recorridos.
                            </p>

                            <div class="mt-10 grid gap-4">
                                <article
                                    class="flex gap-5 rounded-[28px] border border-[#63102a]/8 bg-white p-6 shadow-[0_16px_38px_rgba(99,16,42,0.08)]">
                                    <div
                                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#f7ecd8] text-[#63102a]">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 6h16M4 12h10M4 18h16" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-black text-[#201815]">
                                            Información completa
                                        </h3>
                                        <p class="mt-1 text-sm leading-6 text-[#4f0c22]/70">
                                            Actualiza nombre comercial, descripción, dirección, contacto y servicios.
                                        </p>
                                    </div>
                                </article>

                                <article
                                    class="flex gap-5 rounded-[28px] border border-[#63102a]/8 bg-[#fff9ef] p-6 shadow-[0_16px_38px_rgba(188,149,92,0.12)]">
                                    <div
                                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#f7ecd8] text-[#235b4e]">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3M5 11h14M7 21h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-black text-[#201815]">
                                            Horarios por día
                                        </h3>
                                        <p class="mt-1 text-sm leading-6 text-[#4f0c22]/70">
                                            Indica cuándo abres, cuándo cierras y qué días no das servicio.
                                        </p>
                                    </div>
                                </article>

                                <article
                                    class="flex gap-5 rounded-[28px] border border-[#63102a]/8 bg-white p-6 shadow-[0_16px_38px_rgba(99,16,42,0.08)]">
                                    <div
                                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#fff0d8] text-[#bc955c]">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-black text-[#201815]">
                                            Galería visual
                                        </h3>
                                        <p class="mt-1 text-sm leading-6 text-[#4f0c22]/70">
                                            Sube fotos del lugar, menú, productos o servicios para generar confianza.
                                        </p>
                                    </div>
                                </article>
                            </div>
                        </div>

                        <div class="relative lg:col-span-7">
                            <div
                                class="absolute -right-10 -top-10 h-64 w-64 rounded-full bg-[#bc955c]/10 blur-[100px]">
                            </div>
                            <div
                                class="absolute -bottom-10 -left-10 h-64 w-64 rounded-full bg-[#63102a]/10 blur-[100px]">
                            </div>

                            <div
                                class="relative overflow-hidden rounded-[44px] border border-[#63102a]/5 bg-white p-3 shadow-[0_40px_80px_rgba(99,16,42,0.12)]">
                                <div
                                    class="absolute inset-x-6 top-4 z-10 hidden items-center justify-between rounded-full border border-white/60 bg-white/84 px-5 py-2 backdrop-blur-md sm:flex">
                                    <div
                                        class="flex items-center gap-2 text-[11px] font-black uppercase tracking-[0.16em] text-[#63102a]">
                                        <span class="h-2.5 w-2.5 rounded-full bg-[#bc955c]"></span>
                                        Vista ilustrativa
                                    </div>
                                    <span class="text-xs font-semibold text-[#4f0c22]/55">
                                        Perfil + mapa
                                    </span>
                                </div>

                                <div class="h-[380px] overflow-hidden rounded-[34px] bg-[#f7ecd8] sm:h-[520px] lg:h-[600px]">
                                    <img src="{{ $mapPreview }}" alt="Vista ilustrativa de ExploraNeza Comercios"
                                        class="h-full w-full object-contain">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- MODULOS --}}
            <section id="modulos" class="relative overflow-hidden bg-white py-20 sm:py-24">
                <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mx-auto mb-12 max-w-3xl text-center">
                        <span
                            class="inline-flex rounded-full bg-[#63102a]/8 px-4 py-2 text-[11px] font-black uppercase tracking-[0.18em] text-[#63102a]">
                            Módulos principales
                        </span>

                        <h2 class="mt-5 text-4xl font-black leading-tight text-[#201815] sm:text-5xl">
                            Herramientas simples para mostrar y promover tu comercio.
                        </h2>

                        <p class="mt-5 text-lg leading-8 text-[#4f0c22]/72">
                            Cada módulo está pensado para que mantengas tu información actualizada y aproveches mejor la
                            presencia de tu negocio dentro de ExploraNeza.
                        </p>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                        <article
                            class="group relative overflow-hidden rounded-[32px] border border-[#63102a]/8 bg-[#fbf5eb] p-7 shadow-[0_20px_40px_rgba(99,16,42,0.06)] transition duration-300 hover:-translate-y-1 hover:bg-white hover:shadow-[0_26px_54px_rgba(99,16,42,0.12)]">
                            <p class="text-[11px] font-black uppercase tracking-[0.16em] text-[#bc955c]">
                                Registro
                            </p>
                            <h3 class="mt-3 text-2xl font-black text-[#201815]">
                                Alta de establecimiento
                            </h3>
                            <p class="mt-4 text-[15px] leading-7 text-[#4f0c22]/72">
                                Comparte tus datos principales para iniciar el proceso y formar parte de la app.
                            </p>
                        </article>

                        <article
                            class="group relative overflow-hidden rounded-[32px] border border-[#63102a]/8 bg-[#fbf5eb] p-7 shadow-[0_20px_40px_rgba(99,16,42,0.06)] transition duration-300 hover:-translate-y-1 hover:bg-white hover:shadow-[0_26px_54px_rgba(99,16,42,0.12)]">
                            <p class="text-[11px] font-black uppercase tracking-[0.16em] text-[#bc955c]">
                                Perfil público
                            </p>
                            <h3 class="mt-3 text-2xl font-black text-[#201815]">
                                Cómo te verá la ciudad
                            </h3>
                            <p class="mt-4 text-[15px] leading-7 text-[#4f0c22]/72">
                                Controla tu nombre comercial, descripción, dirección, ubicación y datos de contacto.
                            </p>
                        </article>

                        <article
                            class="group relative overflow-hidden rounded-[32px] border border-[#63102a]/8 bg-[#63102a] p-7 text-white shadow-[0_22px_48px_rgba(99,16,42,0.18)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_28px_60px_rgba(99,16,42,0.26)]">
                            <p class="text-[11px] font-black uppercase tracking-[0.16em] text-[#f2cf91]">
                                Cupones
                            </p>
                            <h3 class="mt-3 text-2xl font-black text-white">
                                Promociones visibles
                            </h3>
                            <p class="mt-4 text-[15px] leading-7 text-white/82">
                                Publica beneficios para que más personas conozcan y visiten tu comercio.
                            </p>
                        </article>

                        <article
                            class="group relative overflow-hidden rounded-[32px] border border-[#63102a]/8 bg-[#fbf5eb] p-7 shadow-[0_20px_40px_rgba(99,16,42,0.06)] transition duration-300 hover:-translate-y-1 hover:bg-white hover:shadow-[0_26px_54px_rgba(99,16,42,0.12)]">
                            <p class="text-[11px] font-black uppercase tracking-[0.16em] text-[#bc955c]">
                                Horarios
                            </p>
                            <h3 class="mt-3 text-2xl font-black text-[#201815]">
                                Apertura por día
                            </h3>
                            <p class="mt-4 text-[15px] leading-7 text-[#4f0c22]/72">
                                Mantén actualizados tus horarios, días de cierre y disponibilidad.
                            </p>
                        </article>

                        <article
                            class="group relative overflow-hidden rounded-[32px] border border-[#63102a]/8 bg-[#fbf5eb] p-7 shadow-[0_20px_40px_rgba(99,16,42,0.06)] transition duration-300 hover:-translate-y-1 hover:bg-white hover:shadow-[0_26px_54px_rgba(99,16,42,0.12)]">
                            <p class="text-[11px] font-black uppercase tracking-[0.16em] text-[#bc955c]">
                                Galería
                            </p>
                            <h3 class="mt-3 text-2xl font-black text-[#201815]">
                                Fotos y contenido visual
                            </h3>
                            <p class="mt-4 text-[15px] leading-7 text-[#4f0c22]/72">
                                Muestra tu local, productos, menú, servicios o ambiente con imágenes.
                            </p>
                        </article>

                        <article
                            class="group relative overflow-hidden rounded-[32px] border border-[#63102a]/8 bg-[#fbf5eb] p-7 shadow-[0_20px_40px_rgba(99,16,42,0.06)] transition duration-300 hover:-translate-y-1 hover:bg-white hover:shadow-[0_26px_54px_rgba(99,16,42,0.12)]">
                            <p class="text-[11px] font-black uppercase tracking-[0.16em] text-[#bc955c]">
                                QR y recorridos
                            </p>
                            <h3 class="mt-3 text-2xl font-black text-[#201815]">
                                Sellos y visitas
                            </h3>
                            <p class="mt-4 text-[15px] leading-7 text-[#4f0c22]/72">
                                Participa en experiencias locales donde las personas escanean códigos en tu comercio.
                            </p>
                        </article>
                    </div>
                </div>
            </section>

            {{-- PROCESO --}}
            <section class="relative overflow-hidden bg-[#fbf5eb] py-20 sm:py-24">
                <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="grid gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:items-start">
                        <div>
                            <span
                                class="inline-flex rounded-full bg-[#63102a]/8 px-4 py-2 text-[11px] font-black uppercase tracking-[0.18em] text-[#63102a]">
                                Cómo funciona
                            </span>

                            <h2 class="mt-5 text-4xl font-black leading-tight text-[#201815] sm:text-5xl">
                                Regístrate y empieza a preparar tu perfil.
                            </h2>

                            <p class="mt-5 text-lg leading-8 text-[#4f0c22]/72">
                                El objetivo es que tu comercio tenga información clara, útil y atractiva para las
                                personas que usan ExploraNeza.
                            </p>
                        </div>

                        <div class="grid gap-4">
                            <article class="rounded-[30px] border border-[#63102a]/8 bg-white p-6 shadow-[0_18px_40px_rgba(99,16,42,0.06)]">
                                <p class="text-4xl font-black text-[#bc955c]/30">01</p>
                                <h3 class="mt-2 text-xl font-black text-[#201815]">
                                    Crea tu cuenta de comercio
                                </h3>
                                <p class="mt-3 text-sm leading-7 text-[#4f0c22]/72">
                                    Ingresa al registro y comparte la información básica de tu establecimiento.
                                </p>
                            </article>

                            <article class="rounded-[30px] border border-[#63102a]/8 bg-white p-6 shadow-[0_18px_40px_rgba(99,16,42,0.06)]">
                                <p class="text-4xl font-black text-[#bc955c]/30">02</p>
                                <h3 class="mt-2 text-xl font-black text-[#201815]">
                                    Completa tu perfil
                                </h3>
                                <p class="mt-3 text-sm leading-7 text-[#4f0c22]/72">
                                    Agrega descripción, horarios, ubicación, fotos y datos para que las personas te
                                    conozcan mejor.
                                </p>
                            </article>

                            <article class="rounded-[30px] border border-[#63102a]/8 bg-[#63102a] p-6 text-white shadow-[0_22px_48px_rgba(99,16,42,0.18)]">
                                <p class="text-4xl font-black text-[#f2cf91]/40">03</p>
                                <h3 class="mt-2 text-xl font-black">
                                    Publica beneficios y participa
                                </h3>
                                <p class="mt-3 text-sm leading-7 text-white/82">
                                    Usa cupones, promociones o códigos QR para integrarte a recorridos y experiencias
                                    locales.
                                </p>
                            </article>
                        </div>
                    </div>
                </div>
            </section>

            {{-- CTA FINAL --}}
            <section id="registro" class="relative overflow-hidden bg-white py-20">
                <div
                    class="pointer-events-none absolute left-0 top-1/2 h-72 w-72 -translate-x-1/2 -translate-y-1/2 rounded-full bg-[#bc955c]/10 blur-3xl">
                </div>
                <div
                    class="pointer-events-none absolute right-0 top-1/2 h-72 w-72 translate-x-1/2 -translate-y-1/2 rounded-full bg-[#63102a]/10 blur-3xl">
                </div>

                <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div
                        class="shine-wrap overflow-hidden rounded-[40px] bg-[linear-gradient(135deg,#4f0c22,#63102a_55%,#7f173c)] px-6 py-10 text-white shadow-[0_28px_70px_rgba(99,16,42,0.18)] sm:px-10 lg:px-12 lg:py-12">
                        <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_320px] lg:items-center">
                            <div>
                                <p class="text-sm font-black uppercase tracking-[0.18em] text-[#f2cf91]">
                                    Únete a ExploraNeza
                                </p>

                                <h2 class="mt-4 max-w-4xl text-4xl font-black leading-tight tracking-tight sm:text-5xl">
                                    Tu comercio puede formar parte del mapa, las rutas y los beneficios de la ciudad.
                                </h2>

                                <p class="mt-5 max-w-3xl text-base leading-8 text-white/78 sm:text-lg">
                                    Regístrate para administrar tu negocio, publicar promociones y participar en
                                    experiencias locales.
                                </p>

                                <div class="mt-6 flex flex-wrap gap-3 text-sm font-semibold text-white/80">
                                    <span class="rounded-full border border-white/12 bg-white/8 px-4 py-2">
                                        Perfil
                                    </span>
                                    <span class="rounded-full border border-white/12 bg-white/8 px-4 py-2">
                                        Mapa
                                    </span>
                                    <span class="rounded-full border border-white/12 bg-white/8 px-4 py-2">
                                        Cupones
                                    </span>
                                    <span class="rounded-full border border-white/12 bg-white/8 px-4 py-2">
                                        QR
                                    </span>
                                </div>
                            </div>

                            <div class="rounded-[30px] border border-white/12 bg-white/8 p-5 backdrop-blur">
                                <div class="flex flex-col gap-3">
                                    <a href="{{ $frontendCommerceRegisterUrl }}"
                                        class="inline-flex items-center justify-center rounded-full bg-white px-6 py-3.5 text-sm font-black text-[#63102a] shadow-[0_16px_34px_rgba(0,0,0,0.12)] transition hover:-translate-y-0.5 hover:bg-[#fbf5eb]">
                                        Registrar mi comercio
                                    </a>

                                    <a href="{{ $frontendCommerceLoginUrl }}"
                                        class="inline-flex items-center justify-center rounded-full border border-white/16 bg-white/10 px-6 py-3.5 text-sm font-black text-white transition hover:bg-white/16">
                                        Ingresar al panel
                                    </a>

                                    <a href="{{ route('landing') }}"
                                        class="inline-flex items-center justify-center rounded-full border border-white/16 bg-transparent px-6 py-3.5 text-sm font-black text-white/90 transition hover:bg-white/10">
                                        Ver app de usuarios
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        {{-- FOOTER --}}
        <footer class="border-t border-[#63102a]/8 bg-white/90">
            <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
                <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-start">
                    <div>
                        <p class="text-lg font-black text-[#201815]">
                            ExploraNeza Comercios
                        </p>

                        <p class="mt-2 max-w-2xl text-sm leading-7 text-[#4f0c22]/75">
                            Una forma más simple de mostrar tu negocio, compartir información útil y conectar con la
                            población desde la app de la ciudad.
                        </p>

                        <p class="mt-4 text-xs font-semibold uppercase tracking-[0.16em] text-[#bc955c]">
                            Haz visible tu comercio local
                        </p>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-[auto_auto] lg:gap-10">
                        <div>
                            <p class="text-xs font-black uppercase tracking-[0.16em] text-[#63102a]">
                                Secciones
                            </p>

                            <div class="mt-4 flex flex-col gap-2 text-sm font-semibold text-[#4f0c22]/75">
                                <a href="#inicio" class="transition hover:text-[#63102a]">
                                    Inicio
                                </a>
                                <a href="#beneficios" class="transition hover:text-[#63102a]">
                                    Beneficios
                                </a>
                                <a href="#panel" class="transition hover:text-[#63102a]">
                                    Panel
                                </a>
                                <a href="#modulos" class="transition hover:text-[#63102a]">
                                    Módulos
                                </a>
                                <a href="#registro" class="transition hover:text-[#63102a]">
                                    Registro
                                </a>
                            </div>
                        </div>

                        <div>
                            <p class="text-xs font-black uppercase tracking-[0.16em] text-[#63102a]">
                                Accesos
                            </p>

                            <div class="mt-4 flex flex-col gap-2 text-sm font-semibold text-[#4f0c22]/75">
                                <a href="{{ $frontendCommerceRegisterUrl }}"
                                    class="transition hover:text-[#63102a]">
                                    Registrar comercio
                                </a>
                                <a href="{{ $frontendCommerceLoginUrl }}"
                                    class="transition hover:text-[#63102a]">
                                    Ingresar al panel
                                </a>
                                <a href="{{ route('landing') }}" class="transition hover:text-[#63102a]">
                                    App para usuarios
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="mt-8 flex flex-col gap-4 border-t border-[#63102a]/8 pt-6 text-xs text-[#4f0c22]/60 sm:flex-row sm:items-center sm:justify-between">
                    <p>
                        © {{ date('Y') }} ExploraNeza Comercios. Todos los derechos reservados.
                    </p>

                    <div class="flex flex-wrap gap-4">
                        <a href="{{ $frontendCommerceRegisterUrl }}"
                            class="font-bold text-[#63102a] transition hover:text-[#4f0c22]">
                            Registrar comercio
                        </a>
                        <a href="{{ $frontendCommerceLoginUrl }}"
                            class="font-bold text-[#63102a] transition hover:text-[#4f0c22]">
                            Ingresar al panel
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>

</html>

