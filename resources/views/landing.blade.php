<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ExploraNeza | Una app para conocer mejor Nezahualcóyotl</title>
    <meta name="description"
        content="ExploraNeza es una app para conocer Nezahualcóyotl. Encuentra lugares, eventos, rutas, descuentos, historia, transporte, tianguis y comercios desde tu celular.">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes floatSoft {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-12px);
            }
        }

        @keyframes glowPulse {

            0%,
            100% {
                opacity: .55;
                transform: scale(1);
            }

            50% {
                opacity: .9;
                transform: scale(1.06);
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

        .fx-float {
            animation: floatSoft 6s ease-in-out infinite;
        }

        .fx-float-delayed {
            animation: floatSoft 7.5s ease-in-out infinite;
            animation-delay: 0.8s;
        }

        .fx-glow {
            animation: glowPulse 6s ease-in-out infinite;
        }

        .fx-appear {
            animation: fadeLift .7s ease-out both;
        }

        .fx-appear-delay {
            animation: fadeLift .85s ease-out both;
            animation-delay: .12s;
        }

        .glass-card {
            backdrop-filter: blur(14px);
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

        .landing-map .leaflet-pane,
        .landing-map .leaflet-control,
        .landing-map .leaflet-top,
        .landing-map .leaflet-bottom {
            z-index: 10;
        }

        .landing-map .leaflet-popup-content-wrapper {
            border-radius: 18px;
            box-shadow: 0 18px 40px rgba(32, 24, 21, 0.16);
        }

        .landing-map .leaflet-popup-content {
            margin: 14px 16px;
            min-width: 200px;
        }

        .landing-map-marker {
            width: 16px;
            height: 16px;
            border-radius: 999px;
            border: 3px solid #fff;
            box-shadow: 0 10px 22px rgba(32, 24, 21, 0.22);
        }
    </style>
</head>

<body class="bg-[#fbf5eb] text-[#201815] antialiased">
    @php
        $frontendUrl = rtrim((string) env('FRONTEND_URL', config('app.url')), '/');
        $frontendMapUrl = $frontendUrl . '/mapa';
        $frontendPassportUrl = $frontendUrl . '/pasaporte';
        $frontendCouponsUrl = $frontendUrl . '/cuponera';
        $frontendEventsUrl = $frontendUrl . '/eventos';
        $commerceLandingUrl = route('landing.comercios');
        $logoLanding = asset('img/Logo.png');
        $heroPhone = asset('img/landing/tel-hero.png');
        $heroPhone2 = asset('img/landing/landing-hero.png');
        $pasaporte = asset('img/landing/pasaporte.png');
        $heroVector = asset('img/landing/Vector.png');
        $mapPreview = asset('img/landing/mapas.png');
        $installStep2 = asset('img/landing/paso 2.jpeg');
        $installStep3 = asset('img/landing/paso 3.jpeg');
        $installStep4 = asset('img/landing/paso 4.jpeg');
        $installQr = asset('img/landing/qr-instalacion.png');
        $installAndroidStep1 = asset('img/landing/paso 1 - andriod.jpg');
        $installAndroidStep2 = asset('img/landing/paso 2 - andriod.jpg');
        $installAndroidStep3 = asset('img/landing/paso 3 - andriod.jpg');
        $installAndroidStep4 = asset('img/landing/paso 4 - andriod.jpg');
        $installAndroidStep5 = asset('img/landing/paso 5 - andriod.jpg');
        $installIosStep1 = asset('img/landing/paso 1 - ios.jpg');
        $installIosStep2 = asset('img/landing/paso 2 - ios.jpg');
        $installIosStep3 = asset('img/landing/paso 3 - ios.jpg');
        $installIosStep4 = asset('img/landing/paso 4 - ios.jpg');
        $installIosStep5 = asset('img/landing/paso 5 - ios.jpg');
        $installIosStep6 = asset('img/landing/paso 6 - ios.jpg');
    @endphp

    <div class="relative overflow-hidden bg-[#fbf5eb]">
        <div
            class="pointer-events-none absolute inset-x-0 top-0 h-[680px] bg-[radial-gradient(circle_at_top_left,rgba(188,149,92,0.16),transparent_35%),radial-gradient(circle_at_top_right,rgba(99,16,42,0.12),transparent_30%)]">
        </div>

        <header
            class="fixed inset-x-0 top-0 z-50 border-b border-[#63102a]/10 bg-[#ffffff]/88 shadow-[0_10px_30px_rgba(99,16,42,0.05)] backdrop-blur-xl">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('landing') }}"
                    class="flex shrink-0 items-center rounded-2xl px-2 py-1 transition hover:bg-[#f7ecd8]">
                    <img src="{{ $logoLanding }}" alt="ExploraNeza" class="h-12 w-auto sm:h-14">
                </a>

                <nav
                    class="glass-card hidden items-center gap-2 rounded-full border border-[#63102a]/10 bg-[#fbf5eb]/86 p-2 text-[13px] font-semibold text-[#201815] shadow-[0_14px_28px_rgba(99,16,42,0.05)] lg:flex lg:sticky lg:top-4">
                    <a href="#explora"
                        class="rounded-full px-4 py-2 transition hover:bg-[#ffffff] hover:text-[#63102a]">Inicio</a>
                    <a href="#mapa"
                        class="rounded-full px-4 py-2 transition hover:bg-[#ffffff] hover:text-[#63102a]">Mapa</a>
                    <a href="#pasaporte"
                        class="rounded-full px-4 py-2 transition hover:bg-[#ffffff] hover:text-[#63102a]">Pasaporte</a>
                    <a href="#comercios"
                        class="rounded-full px-4 py-2 transition hover:bg-[#ffffff] hover:text-[#63102a]">Comercios</a>
                    <a href="#instala"
                        class="rounded-full px-4 py-2 transition hover:bg-[#ffffff] hover:text-[#63102a]">Instálala</a>
                </nav>

                <div class="flex items-center gap-3">
                    <a href="{{ $commerceLandingUrl }}"
                        class="hidden rounded-xl border border-[#63102a]/12 bg-white px-4 py-3 text-sm font-semibold text-[#63102a] transition hover:bg-[#f7ecd8] sm:inline-flex">
                        Soy comercio
                    </a>
                    <a href="{{ $frontendUrl }}"
                        class="shine-wrap inline-flex items-center justify-center rounded-xl bg-[#63102a] px-5 py-3 text-sm font-semibold text-white shadow-[0_14px_28px_rgba(99,16,42,0.22)] transition hover:-translate-y-0.5 hover:bg-[#4f0c22] sm:px-6">
                        Explorar la app
                    </a>
                </div>
            </div>
        </header>

        <main class="pt-[96px] sm:pt-[104px]">
            <section id="explora" class="relative overflow-hidden bg-[#63102a] bg-cover bg-center bg-no-repeat"
                style="background-image: url('{{ $heroVector }}');">
                <div
                    class="pointer-events-none absolute left-[6%] top-16 h-32 w-32 rounded-full bg-[#f2cf91]/12 blur-3xl fx-glow">
                </div>
                <div
                    class="pointer-events-none absolute right-[10%] top-20 h-36 w-36 rounded-full bg-white/10 blur-3xl fx-glow">
                </div>

                <div
                    class="mx-auto grid max-w-7xl items-center gap-10 px-4 pb-14 pt-8 sm:px-6 md:grid-cols-[minmax(0,1fr)_360px] lg:grid-cols-[minmax(0,1fr)_420px] lg:px-8 lg:pb-16 lg:pt-4">
                    <div class="relative z-10 py-10 text-white lg:py-12 fx-appear">
                        <div
                            class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/8 px-4 py-2 text-[14px] font-extrabold tracking-tight text-[#f2cf91] glass-card">
                            <span class="h-2 w-2 rounded-full bg-[#f2cf91]"></span>
                            Descubre Nezahualcóyotl
                        </div>

                        <h1 class="mt-4 max-w-[620px] text-4xl font-extrabold leading-tight sm:text-5xl lg:text-[54px]">
                            Una app para conocer Neza desde el mapa, los recorridos y los negocios de la ciudad.
                        </h1>

                        <p class="mt-5 max-w-[620px] text-lg leading-8 text-white/92">
                            Encuentra lugares, eventos, descuentos, historia, transporte, tianguis y tu pasaporte de
                            visitas
                            en una app hecha para usarla mientras recorres la ciudad.
                        </p>

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ $frontendUrl }}"
                                class="shine-wrap inline-flex items-center justify-center rounded-xl bg-[#f2cf91] px-5 py-3 text-[15px] font-bold text-[#63102a] shadow-[0_16px_34px_rgba(188,149,92,0.22)] transition hover:-translate-y-0.5 hover:bg-[#bc955c] hover:text-white">
                                Abrir ExploraNeza
                            </a>
                            <a href="{{ $frontendMapUrl }}"
                                class="inline-flex items-center justify-center rounded-xl border border-white/14 bg-white/10 px-5 py-3 text-[15px] font-bold text-white transition hover:bg-white/14">
                                Ver mapa
                            </a>
                            <a href="{{ $commerceLandingUrl }}"
                                class="inline-flex items-center justify-center rounded-xl border border-white/14 bg-white/10 px-5 py-3 text-[15px] font-bold text-white transition hover:bg-white/14">
                                Soy comercio
                            </a>
                        </div>

                        <div class="mt-10 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                            <article class="rounded-[24px] border border-white/10 bg-white/8 p-4 glass-card">
                                <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-[#f2cf91]">Descubre</p>
                                <p class="mt-2 text-sm leading-6 text-white/85">Mapa, eventos, historia, transporte y
                                    tianguis en una sola app.</p>
                            </article>
                            <article class="rounded-[24px] border border-white/10 bg-white/8 p-4 glass-card">
                                <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-[#f2cf91]">Participa
                                </p>
                                <p class="mt-2 text-sm leading-6 text-white/85">Junta sellos, sigue recorridos y guarda
                                    descuentos cerca de ti.</p>
                            </article>
                            <article class="rounded-[24px] border border-white/10 bg-white/8 p-4 glass-card">
                                <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-[#f2cf91]">Conecta</p>
                                <p class="mt-2 text-sm leading-6 text-white/85">Conoce negocios locales con fotos,
                                    horarios y promociones.</p>
                            </article>
                        </div>
                    </div>

                    <div class="relative z-10 flex justify-center md:justify-end fx-appear-delay">
                        <div class="relative flex w-full justify-center md:justify-end">
                            <div
                                class="pointer-events-none absolute bottom-4 h-12 w-[72%] rounded-full bg-black/30 blur-2xl md:right-0 md:w-[82%]">
                            </div>
                            <img src="{{ $heroPhone2 }}" alt="Pasaporte digital de ExploraNeza"
                                class="fx-float relative w-full max-w-[330px] drop-shadow-[0_26px_38px_rgba(0,0,0,0.42)] sm:max-w-[360px] md:max-w-[340px] lg:max-w-[400px] lg:translate-y-3">
                        </div>
                    </div>
                </div>
            </section>

            <section
                class="relative overflow-hidden border-y border-[#63102a]/8 bg-[linear-gradient(180deg,rgba(255,255,255,0.98),rgba(251,245,235,0.96))] py-14">
                <div
                    class="absolute inset-x-0 top-0 h-px bg-[linear-gradient(90deg,transparent,rgba(188,149,92,0.45),transparent)]">
                </div>

                <div
                    class="pointer-events-none absolute -left-24 top-10 h-56 w-56 rounded-full bg-[#bc955c]/10 blur-3xl">
                </div>
                <div
                    class="pointer-events-none absolute -right-24 bottom-0 h-56 w-56 rounded-full bg-[#63102a]/10 blur-3xl">
                </div>

                <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mx-auto mb-9 max-w-3xl text-center">
                        <span
                            class="inline-flex rounded-full bg-[#63102a]/8 px-4 py-2 text-[11px] font-black uppercase tracking-[0.18em] text-[#63102a]">
                            Explora Neza desde tu celular
                        </span>
                        <h2 class="mt-4 text-3xl font-black leading-tight text-[#201815] sm:text-4xl">
                            Todo lo que necesitas para moverte, descubrir y aprovechar la ciudad.
                        </h2>
                        <p class="mt-4 text-base leading-7 text-[#4f0c22]/72 sm:text-lg">
                            Encuentra lugares cercanos, participa en recorridos, guarda beneficios y lleva ExploraNeza
                            como una app
                            en tu pantalla de inicio.
                        </p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-12">
                        <article
                            class="group relative overflow-hidden rounded-[28px] border border-[#63102a]/8 bg-white/95 p-6 shadow-[0_16px_32px_rgba(99,16,42,0.06)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_22px_44px_rgba(99,16,42,0.12)] lg:col-span-3">
                            <div
                                class="absolute right-0 top-0 h-24 w-24 translate-x-1/3 -translate-y-1/3 rounded-full bg-[#63102a]/6 blur-2xl">
                            </div>

                            <div
                                class="relative flex h-12 w-12 items-center justify-center rounded-2xl bg-[#f7ecd8] text-[#63102a] transition group-hover:scale-105">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>

                            <p class="mt-5 text-[11px] font-black uppercase tracking-[0.16em] text-[#bc955c]">
                                Encuentra lugares
                            </p>
                            <h3 class="mt-2 text-xl font-black text-[#201815]">
                                Busca qué hay cerca de ti
                            </h3>
                            <p class="mt-3 text-sm leading-6 text-[#4f0c22]/75">
                                Ubica negocios, mercados, servicios, eventos y puntos de interés desde el mapa
                                interactivo.
                            </p>
                        </article>

                        <article
                            class="group relative overflow-hidden rounded-[28px] border border-[#63102a]/8 bg-white/95 p-6 shadow-[0_16px_32px_rgba(99,16,42,0.06)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_22px_44px_rgba(99,16,42,0.12)] lg:col-span-3">
                            <div
                                class="absolute right-0 top-0 h-24 w-24 translate-x-1/3 -translate-y-1/3 rounded-full bg-[#bc955c]/10 blur-2xl">
                            </div>

                            <div
                                class="relative flex h-12 w-12 items-center justify-center rounded-2xl bg-[#fff0d8] text-[#bc955c] transition group-hover:scale-105">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                </svg>
                            </div>

                            <p class="mt-5 text-[11px] font-black uppercase tracking-[0.16em] text-[#bc955c]">
                                Recorre la ciudad
                            </p>
                            <h3 class="mt-2 text-xl font-black text-[#201815]">
                                Sigue rutas y junta sellos
                            </h3>
                            <p class="mt-3 text-sm leading-6 text-[#4f0c22]/75">
                                Visita puntos participantes, escanea códigos QR y completa experiencias con tu pasaporte
                                digital.
                            </p>
                        </article>

                        <article
                            class="group relative overflow-hidden rounded-[28px] border border-[#63102a]/8 bg-white/95 p-6 shadow-[0_16px_32px_rgba(99,16,42,0.06)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_22px_44px_rgba(99,16,42,0.12)] lg:col-span-3">
                            <div
                                class="absolute right-0 top-0 h-24 w-24 translate-x-1/3 -translate-y-1/3 rounded-full bg-[#235b4e]/8 blur-2xl">
                            </div>

                            <div
                                class="relative flex h-12 w-12 items-center justify-center rounded-2xl bg-[#ecf6f2] text-[#235b4e] transition group-hover:scale-105">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-10c1.11 0 2.08.402 2.599 1M12 8V7m0 11v-1m0 0c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                            </div>

                            <p class="mt-5 text-[11px] font-black uppercase tracking-[0.16em] text-[#bc955c]">
                                Ahorra en comercios
                            </p>
                            <h3 class="mt-2 text-xl font-black text-[#201815]">
                                Guarda cupones y beneficios
                            </h3>
                            <p class="mt-3 text-sm leading-6 text-[#4f0c22]/75">
                                Consulta promociones locales, revisa vigencias y aprovecha descuentos en
                                establecimientos cercanos.
                            </p>
                        </article>

                        <article
                            class="group relative overflow-hidden rounded-[28px] border border-[#63102a]/8 bg-[#63102a] p-6 text-white shadow-[0_18px_38px_rgba(99,16,42,0.18)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_26px_52px_rgba(99,16,42,0.26)] lg:col-span-3">
                            <div
                                class="absolute right-0 top-0 h-28 w-28 translate-x-1/3 -translate-y-1/3 rounded-full bg-[#f2cf91]/16 blur-2xl">
                            </div>
                            <div
                                class="absolute bottom-0 left-0 h-28 w-28 -translate-x-1/3 translate-y-1/3 rounded-full bg-white/10 blur-2xl">
                            </div>

                            <div
                                class="relative flex h-12 w-12 items-center justify-center rounded-2xl bg-white/12 text-[#f2cf91] transition group-hover:scale-105">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>

                            <p class="relative mt-5 text-[11px] font-black uppercase tracking-[0.16em] text-[#f2cf91]">
                                Llévala contigo
                            </p>
                            <h3 class="relative mt-2 text-xl font-black text-white">
                                Instálala como app
                            </h3>
                            <p class="relative mt-3 text-sm leading-6 text-white/82">
                                Agrégala a tu pantalla de inicio y abre ExploraNeza rápido, como cualquier aplicación de
                                tu celular.
                            </p>

                            <a href="#instala"
                                class="relative mt-5 inline-flex items-center justify-center rounded-full bg-[#f2cf91] px-4 py-2.5 text-xs font-black text-[#63102a] transition hover:bg-white">
                                Ver cómo instalarla
                            </a>
                        </article>
                    </div>
                </div>
            </section>




            <section class="relative overflow-hidden bg-[#fbf5eb] py-24">
                <div
                    class="pointer-events-none absolute left-0 top-12 h-72 w-72 -translate-x-1/2 rounded-full bg-[#63102a]/8 blur-3xl">
                </div>
                <div
                    class="pointer-events-none absolute right-0 bottom-10 h-72 w-72 translate-x-1/2 rounded-full bg-[#bc955c]/12 blur-3xl">
                </div>

                <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="grid gap-10 lg:grid-cols-[minmax(0,0.95fr)_minmax(0,1.05fr)] lg:items-end">
                        <div>
                            <span
                                class="inline-flex rounded-full bg-[#63102a]/8 px-4 py-2 text-[11px] font-black uppercase tracking-[0.18em] text-[#63102a]">
                                Todo en un solo lugar
                            </span>

                            <h2 class="mt-5 max-w-3xl text-4xl font-black leading-tight text-[#201815] md:text-5xl">
                                ExploraNeza te ayuda a disfrutar más la ciudad desde tu celular.
                            </h2>
                        </div>

                        <div
                            class="rounded-[30px] border border-[#63102a]/8 bg-white/70 p-6 shadow-[0_18px_40px_rgba(99,16,42,0.06)] backdrop-blur">
                            <p class="text-lg leading-8 text-[#4f0c22]/72">
                                Consulta lugares, eventos, rutas, cupones, historia, tianguis y servicios útiles en una
                                sola app
                                pensada para habitantes, visitantes y comercios de Nezahualcóyotl.
                            </p>
                        </div>
                    </div>

                    <div class="mt-12 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                        <article
                            class="group relative overflow-hidden rounded-[32px] border border-[#63102a]/8 bg-white p-7 shadow-[0_20px_40px_rgba(99,16,42,0.06)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_26px_54px_rgba(99,16,42,0.12)]">
                            <div
                                class="absolute right-0 top-0 h-28 w-28 translate-x-1/3 -translate-y-1/3 rounded-full bg-[#63102a]/7 blur-2xl">
                            </div>

                            <div class="relative flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-[11px] font-black uppercase tracking-[0.16em] text-[#bc955c]">
                                        Inicio
                                    </p>
                                    <h3 class="mt-3 text-2xl font-black text-[#201815]">
                                        Descubre qué hacer hoy
                                    </h3>
                                </div>

                                <div
                                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#f7ecd8] text-[#63102a] transition group-hover:scale-105">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                            </div>

                            <p class="relative mt-4 text-[15px] leading-7 text-[#4f0c22]/72">
                                Encuentra accesos rápidos, recomendaciones, eventos próximos y contenido destacado para
                                empezar a
                                explorar Neza sin perder tiempo.
                            </p>
                        </article>

                        <article
                            class="group relative overflow-hidden rounded-[32px] border border-[#63102a]/8 bg-white p-7 shadow-[0_20px_40px_rgba(99,16,42,0.06)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_26px_54px_rgba(99,16,42,0.12)]">
                            <div
                                class="absolute right-0 top-0 h-28 w-28 translate-x-1/3 -translate-y-1/3 rounded-full bg-[#bc955c]/10 blur-2xl">
                            </div>

                            <div class="relative flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-[11px] font-black uppercase tracking-[0.16em] text-[#bc955c]">
                                        Mapa
                                    </p>
                                    <h3 class="mt-3 text-2xl font-black text-[#201815]">
                                        Encuentra lugares cerca de ti
                                    </h3>
                                </div>

                                <div
                                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#fff0d8] text-[#bc955c] transition group-hover:scale-105">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                            </div>

                            <p class="relative mt-4 text-[15px] leading-7 text-[#4f0c22]/72">
                                Busca comida, mercados, hospitales, iglesias, zonas de interés, comercios y servicios
                                públicos desde
                                un mapa interactivo.
                            </p>
                        </article>

                        <article
                            class="group relative overflow-hidden rounded-[32px] border border-[#63102a]/8 bg-[#63102a] p-7 text-white shadow-[0_22px_48px_rgba(99,16,42,0.18)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_28px_60px_rgba(99,16,42,0.26)]">
                            <div
                                class="absolute right-0 top-0 h-32 w-32 translate-x-1/3 -translate-y-1/3 rounded-full bg-[#f2cf91]/18 blur-2xl">
                            </div>
                            <div
                                class="absolute bottom-0 left-0 h-32 w-32 -translate-x-1/3 translate-y-1/3 rounded-full bg-white/10 blur-2xl">
                            </div>

                            <div class="relative flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-[11px] font-black uppercase tracking-[0.16em] text-[#f2cf91]">
                                        Pasaporte
                                    </p>
                                    <h3 class="mt-3 text-2xl font-black text-white">
                                        Visita, escanea y completa rutas
                                    </h3>
                                </div>

                                <div
                                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white/12 text-[#f2cf91] transition group-hover:scale-105">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6M9 8h6m2 13H7a2 2 0 01-2-2V5a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                            </div>

                            <p class="relative mt-4 text-[15px] leading-7 text-white/82">
                                Recorre puntos participantes, escanea códigos QR y lleva el avance de tus sellos
                                directamente desde
                                tu celular.
                            </p>
                        </article>

                        <article
                            class="group relative overflow-hidden rounded-[32px] border border-[#63102a]/8 bg-white p-7 shadow-[0_20px_40px_rgba(99,16,42,0.06)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_26px_54px_rgba(99,16,42,0.12)]">
                            <div
                                class="absolute right-0 top-0 h-28 w-28 translate-x-1/3 -translate-y-1/3 rounded-full bg-[#235b4e]/9 blur-2xl">
                            </div>

                            <div class="relative flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-[11px] font-black uppercase tracking-[0.16em] text-[#bc955c]">
                                        Cuponera
                                    </p>
                                    <h3 class="mt-3 text-2xl font-black text-[#201815]">
                                        Aprovecha beneficios locales
                                    </h3>
                                </div>

                                <div
                                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#ecf6f2] text-[#235b4e] transition group-hover:scale-105">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-10c1.11 0 2.08.402 2.599 1M12 8V7m0 11v-1m0 0c-1.11 0-2.08-.402-2.599-1" />
                                    </svg>
                                </div>
                            </div>

                            <p class="relative mt-4 text-[15px] leading-7 text-[#4f0c22]/72">
                                Consulta promociones, revisa vigencias, guarda cupones y úsalos en establecimientos
                                participantes
                                de la ciudad.
                            </p>
                        </article>

                        <article
                            class="group relative overflow-hidden rounded-[32px] border border-[#63102a]/8 bg-white p-7 shadow-[0_20px_40px_rgba(99,16,42,0.06)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_26px_54px_rgba(99,16,42,0.12)]">
                            <div
                                class="absolute right-0 top-0 h-28 w-28 translate-x-1/3 -translate-y-1/3 rounded-full bg-[#bc955c]/10 blur-2xl">
                            </div>

                            <div class="relative flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-[11px] font-black uppercase tracking-[0.16em] text-[#bc955c]">
                                        Cultura e historia
                                    </p>
                                    <h3 class="mt-3 text-2xl font-black text-[#201815]">
                                        Conoce la memoria de Neza
                                    </h3>
                                </div>

                                <div
                                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#fff7e8] text-[#bc955c] transition group-hover:scale-105">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253z" />
                                    </svg>
                                </div>
                            </div>

                            <p class="relative mt-4 text-[15px] leading-7 text-[#4f0c22]/72">
                                Descubre eventos, relatos, datos históricos y contenidos que ayudan a entender la
                                identidad de
                                Nezahualcóyotl.
                            </p>
                        </article>

                        <article
                            class="group relative overflow-hidden rounded-[32px] border border-[#63102a]/8 bg-white p-7 shadow-[0_20px_40px_rgba(99,16,42,0.06)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_26px_54px_rgba(99,16,42,0.12)]">
                            <div
                                class="absolute right-0 top-0 h-28 w-28 translate-x-1/3 -translate-y-1/3 rounded-full bg-[#63102a]/7 blur-2xl">
                            </div>

                            <div class="relative flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-[11px] font-black uppercase tracking-[0.16em] text-[#bc955c]">
                                        Utilidad diaria
                                    </p>
                                    <h3 class="mt-3 text-2xl font-black text-[#201815]">
                                        Muévete con más referencias
                                    </h3>
                                </div>

                                <div
                                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#f7ecd8] text-[#63102a] transition group-hover:scale-105">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7h8m-8 5h8m-7 5h6M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z" />
                                    </svg>
                                </div>
                            </div>

                            <p class="relative mt-4 text-[15px] leading-7 text-[#4f0c22]/72">
                                Consulta transporte, tianguis del día y servicios útiles para orientarte mejor en tus
                                recorridos por
                                Neza.
                            </p>
                        </article>
                    </div>

                    <div
                        class="mt-12 flex flex-col items-center justify-between gap-5 rounded-[32px] bg-[#63102a] px-6 py-7 text-white shadow-[0_24px_54px_rgba(99,16,42,0.18)] sm:flex-row sm:px-8">
                        <div>
                            <p class="text-sm font-bold uppercase tracking-[0.16em] text-[#f2cf91]">
                                Empieza a explorar
                            </p>
                            <p class="mt-2 text-xl font-black">
                                Abre ExploraNeza y descubre qué hay cerca de ti.
                            </p>
                        </div>

                        <a href="{{ $frontendUrl }}"
                            class="inline-flex w-full items-center justify-center rounded-full bg-[#f2cf91] px-6 py-3 text-sm font-black text-[#63102a] transition hover:bg-white sm:w-auto">
                            Abrir la app
                        </a>
                    </div>
                </div>
            </section>



            <section id="mapa" class="relative overflow-hidden bg-white py-24">
                <div
                    class="absolute right-0 top-0 h-[520px] w-[520px] -translate-y-1/2 translate-x-1/2 rounded-full bg-[#63102a]/5 blur-[120px]">
                </div>
                <div
                    class="absolute bottom-0 left-0 h-[440px] w-[440px] -translate-x-1/3 translate-y-1/3 rounded-full bg-[#bc955c]/10 blur-[130px]">
                </div>

                <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="grid gap-14 lg:grid-cols-12 lg:items-center">
                        <div class="lg:col-span-5">
                            <span
                                class="inline-flex rounded-full bg-[#63102a]/8 px-4 py-2 text-[11px] font-black uppercase tracking-[0.18em] text-[#63102a]">
                                Mapa interactivo
                            </span>

                            <h2 class="mt-5 text-4xl font-black leading-tight text-[#201815] md:text-5xl">
                                Encuentra lugares de Neza y llega más fácil.
                            </h2>

                            <p class="mt-6 text-lg leading-8 text-[#4f0c22]/72">
                                Explora negocios, mercados, eventos, servicios y puntos de interés desde un mapa pensado
                                para
                                moverte por la ciudad con mejores referencias.
                            </p>

                            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                                <a href="{{ $frontendMapUrl }}"
                                    class="inline-flex items-center justify-center rounded-xl bg-[#63102a] px-5 py-3 text-sm font-black text-white shadow-[0_16px_34px_rgba(99,16,42,0.18)] transition hover:-translate-y-0.5 hover:bg-[#4f0c22]">
                                    Abrir mapa completo
                                </a>

                                <a href="{{ $frontendUrl }}"
                                    class="inline-flex items-center justify-center rounded-xl border border-[#63102a]/12 bg-white px-5 py-3 text-sm font-black text-[#63102a] transition hover:bg-[#f7ecd8]">
                                    Explorar la app
                                </a>
                            </div>

                            <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
                                <article
                                    class="group flex gap-5 rounded-[28px] border border-[#63102a]/8 bg-white/95 p-6 shadow-[0_16px_38px_rgba(99,16,42,0.08)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_22px_46px_rgba(99,16,42,0.14)]">
                                    <div
                                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border border-[#63102a]/5 bg-[#f7ecd8] text-[#63102a] transition group-hover:scale-105">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>

                                    <div>
                                        <h3 class="text-lg font-black text-[#201815]">
                                            Busca por categoría o nombre
                                        </h3>
                                        <p class="mt-1 text-sm leading-6 text-[#4f0c22]/70">
                                            Encuentra comida, mercados, hospitales, iglesias, comercios, espacios
                                            culturales y más.
                                        </p>
                                    </div>
                                </article>

                                <article
                                    class="group flex gap-5 rounded-[28px] border border-[#63102a]/8 bg-[#fff9ef] p-6 shadow-[0_16px_38px_rgba(188,149,92,0.12)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_22px_46px_rgba(99,16,42,0.14)]">
                                    <div
                                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border border-[#63102a]/5 bg-[#f7ecd8] text-[#235b4e] transition group-hover:scale-105">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>

                                    <div>
                                        <h3 class="text-lg font-black text-[#201815]">
                                            Usa tu ubicación
                                        </h3>
                                        <p class="mt-1 text-sm leading-6 text-[#4f0c22]/70">
                                            Revisa qué puntos están cerca de ti y abre indicaciones para llegar desde tu
                                            celular.
                                        </p>
                                    </div>
                                </article>

                                <article
                                    class="group flex gap-5 rounded-[28px] border border-[#63102a]/8 bg-white/95 p-6 shadow-[0_16px_38px_rgba(99,16,42,0.08)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_22px_46px_rgba(99,16,42,0.14)] sm:col-span-2 lg:col-span-1">
                                    <div
                                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border border-[#63102a]/5 bg-[#f7ecd8] text-[#bc955c] transition group-hover:scale-105">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                        </svg>
                                    </div>

                                    <div>
                                        <h3 class="text-lg font-black text-[#201815]">
                                            Consulta información útil
                                        </h3>
                                        <p class="mt-1 text-sm leading-6 text-[#4f0c22]/70">
                                            Revisa dirección, descripción, horarios, imágenes y referencias antes de
                                            visitar un lugar.
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
                                class="relative overflow-hidden rounded-[48px] border border-[#63102a]/5 bg-[linear-gradient(180deg,rgba(255,255,255,0.98),rgba(251,245,235,0.96))] p-3 shadow-[0_40px_80px_rgba(99,16,42,0.12)]">
                                <div
                                    class="absolute inset-x-6 top-4 z-10 hidden items-center justify-between rounded-full border border-white/60 bg-white/80 px-5 py-2 backdrop-blur-md sm:flex">
                                    <div
                                        class="flex items-center gap-2 text-[11px] font-black uppercase tracking-[0.16em] text-[#63102a]">
                                        <span class="h-2.5 w-2.5 rounded-full bg-[#bc955c]"></span>
                                        Lugares disponibles
                                    </div>

                                    <span id="landing-map-count" class="text-xs font-semibold text-[#4f0c22]/55">
                                        Cargando...
                                    </span>
                                </div>

                                <div
                                    class="relative h-[420px] overflow-hidden rounded-[36px] bg-[#f7ecd8] sm:h-[520px] lg:h-[680px] xl:h-[560px]">
                                    <div id="landing-map-loading"
                                        class="absolute inset-0 z-10 flex items-center justify-center bg-[#f7ecd8]/88 text-sm font-semibold text-[#63102a]">
                                        Cargando mapa...
                                    </div>

                                    <div id="landing-map"
                                        class="landing-map absolute inset-0 h-full w-full rounded-[36px]">
                                    </div>
                                </div>

                                <div id="landing-map-filters" class="mt-4 flex flex-wrap justify-center gap-2 p-2">
                                    <span
                                        class="flex items-center gap-2 rounded-2xl bg-[#63102a] px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-[#63102a]/20">
                                        <span class="h-2 w-2 rounded-full bg-[#f2cf91]"></span>
                                        Todos
                                    </span>
                                    <span
                                        class="flex items-center gap-2 rounded-2xl border border-[#63102a]/10 bg-white px-4 py-2.5 text-xs font-bold text-[#201815] shadow-sm">
                                        Comercios
                                    </span>
                                    <span
                                        class="flex items-center gap-2 rounded-2xl border border-[#63102a]/10 bg-white px-4 py-2.5 text-xs font-bold text-[#201815] shadow-sm">
                                        Mercados
                                    </span>
                                    <span
                                        class="flex items-center gap-2 rounded-2xl border border-[#63102a]/10 bg-white px-4 py-2.5 text-xs font-bold text-[#201815] shadow-sm">
                                        Eventos
                                    </span>
                                </div>
                            </div>

                            <div
                                class="mx-auto mt-5 max-w-2xl rounded-[24px] border border-[#63102a]/8 bg-[#fbf5eb] px-5 py-4 text-center text-sm leading-6 text-[#4f0c22]/72">
                                El mapa se actualiza con puntos registrados en la app para que puedas descubrir lugares
                                y servicios
                                de forma más sencilla.
                            </div>
                        </div>
                    </div>
                </div>
            </section>



            <section id="pasaporte" class="relative overflow-hidden bg-[#fbf5eb] py-24">
                <div
                    class="pointer-events-none absolute left-0 top-20 h-72 w-72 -translate-x-1/2 rounded-full bg-[#63102a]/8 blur-3xl">
                </div>
                <div
                    class="pointer-events-none absolute right-0 bottom-10 h-72 w-72 translate-x-1/2 rounded-full bg-[#bc955c]/12 blur-3xl">
                </div>

                <div
                    class="relative mx-auto grid max-w-7xl gap-12 px-4 sm:px-6 lg:grid-cols-[420px_minmax(0,1fr)] lg:items-center lg:px-8">
                    <div class="relative flex justify-center lg:justify-start">
                        <div class="absolute inset-x-12 bottom-2 h-10 rounded-full bg-[#63102a]/14 blur-2xl"></div>

                        <div
                            class="relative rounded-[42px] border border-[#63102a]/8 bg-white/70 p-4 shadow-[0_30px_70px_rgba(99,16,42,0.12)] backdrop-blur">
                            <img src="{{ $pasaporte }}" alt="Pasaporte digital y ruta en ExploraNeza"
                                class="relative z-10 fx-float w-full max-w-[340px] drop-shadow-[0_30px_46px_rgba(99,16,42,0.22)]">
                        </div>

                        <div
                            class="absolute right-4 top-8 hidden rounded-2xl bg-[#63102a] px-4 py-3 text-sm font-black text-white shadow-[0_18px_38px_rgba(99,16,42,0.22)] sm:block lg:right-0">
                            + Sellos
                        </div>

                        <div
                            class="absolute bottom-10 left-2 hidden rounded-2xl bg-[#f2cf91] px-4 py-3 text-sm font-black text-[#63102a] shadow-[0_18px_38px_rgba(188,149,92,0.22)] sm:block">
                            QR
                        </div>
                    </div>

                    <div>
                        <span
                            class="inline-flex rounded-full bg-[#63102a]/8 px-4 py-2 text-[11px] font-black uppercase tracking-[0.18em] text-[#63102a]">
                            Pasaporte digital
                        </span>

                        <h2 class="mt-5 text-4xl font-black leading-tight text-[#201815] sm:text-5xl">
                            Recorre Neza, junta sellos y desbloquea beneficios.
                        </h2>

                        <p class="mt-5 max-w-3xl text-lg leading-8 text-[#4f0c22]/72">
                            El pasaporte de ExploraNeza convierte tus visitas en una experiencia: ve a lugares
                            participantes,
                            escanea códigos QR, registra tus sellos y sigue tu avance desde el celular.
                        </p>

                        <div class="mt-8 grid gap-4 md:grid-cols-3">
                            <article
                                class="rounded-[28px] border border-[#63102a]/8 bg-white p-5 shadow-[0_16px_34px_rgba(99,16,42,0.06)]">
                                <div
                                    class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#f7ecd8] text-[#63102a]">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>

                                <p class="mt-4 text-[11px] font-bold uppercase tracking-[0.14em] text-[#bc955c]">
                                    1. Visita
                                </p>
                                <p class="mt-2 text-[15px] leading-7 text-[#4f0c22]/72">
                                    Llega a un punto participante dentro de una ruta o experiencia local.
                                </p>
                            </article>

                            <article
                                class="rounded-[28px] border border-[#63102a]/8 bg-white p-5 shadow-[0_16px_34px_rgba(99,16,42,0.06)]">
                                <div
                                    class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#fff0d8] text-[#bc955c]">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v1m6.364 1.636l-.707.707M20 12h-1M17.657 17.657l-.707-.707M12 20v-1M6.343 17.657l.707-.707M4 12h1M6.343 6.343l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                                    </svg>
                                </div>

                                <p class="mt-4 text-[11px] font-bold uppercase tracking-[0.14em] text-[#bc955c]">
                                    2. Escanea
                                </p>
                                <p class="mt-2 text-[15px] leading-7 text-[#4f0c22]/72">
                                    Usa el QR del lugar para registrar tu visita de forma rápida.
                                </p>
                            </article>

                            <article
                                class="rounded-[28px] border border-[#63102a]/8 bg-[#63102a] p-5 text-white shadow-[0_18px_38px_rgba(99,16,42,0.18)]">
                                <div
                                    class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white/12 text-[#f2cf91]">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>

                                <p class="mt-4 text-[11px] font-bold uppercase tracking-[0.14em] text-[#f2cf91]">
                                    3. Completa
                                </p>
                                <p class="mt-2 text-[15px] leading-7 text-white/82">
                                    Junta sellos, revisa tu avance y aprovecha beneficios disponibles.
                                </p>
                            </article>
                        </div>

                        <div
                            class="mt-8 rounded-[30px] border border-[#63102a]/8 bg-white/80 p-6 shadow-[0_18px_40px_rgba(99,16,42,0.06)]">
                            <div class="grid gap-5 md:grid-cols-[minmax(0,1fr)_auto] md:items-center">
                                <div>
                                    <p class="text-[11px] font-black uppercase tracking-[0.16em] text-[#bc955c]">
                                        También incluye cupones
                                    </p>
                                    <p class="mt-2 text-base leading-7 text-[#4f0c22]/72">
                                        Consulta promociones, revisa vigencias, guarda descuentos y vuelve a ellos
                                        cuando visites
                                        comercios participantes.
                                    </p>
                                </div>

                                <div class="flex flex-col gap-3 sm:flex-row md:flex-col">
                                    <a href="{{ $frontendPassportUrl }}"
                                        class="inline-flex items-center justify-center rounded-xl bg-[#63102a] px-5 py-3 text-sm font-black text-white shadow-[0_14px_28px_rgba(99,16,42,0.18)] transition hover:-translate-y-0.5 hover:bg-[#4f0c22]">
                                        Ver pasaporte
                                    </a>

                                    <a href="{{ $frontendCouponsUrl }}"
                                        class="inline-flex items-center justify-center rounded-xl border border-[#63102a]/12 bg-white px-5 py-3 text-sm font-black text-[#63102a] transition hover:bg-[#f7ecd8]">
                                        Ver cupones
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>





            <section class="relative overflow-hidden bg-white py-24">
                <div
                    class="pointer-events-none absolute left-0 top-20 h-72 w-72 -translate-x-1/2 rounded-full bg-[#bc955c]/10 blur-3xl">
                </div>
                <div
                    class="pointer-events-none absolute right-0 bottom-0 h-72 w-72 translate-x-1/2 rounded-full bg-[#63102a]/8 blur-3xl">
                </div>

                <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mx-auto mb-12 max-w-3xl text-center">
                        <span
                            class="inline-flex rounded-full bg-[#63102a]/8 px-4 py-2 text-[11px] font-black uppercase tracking-[0.18em] text-[#63102a]">
                            Más que un mapa
                        </span>

                        <h2 class="mt-5 text-4xl font-black leading-tight text-[#201815] sm:text-5xl">
                            Información útil para vivir y recorrer Neza.
                        </h2>

                        <p class="mt-5 text-lg leading-8 text-[#4f0c22]/72">
                            ExploraNeza también reúne actividades, memoria local, transporte y referencias del día a día
                            para que
                            tengas más herramientas al salir por la ciudad.
                        </p>
                    </div>

                    <div class="grid gap-6 lg:grid-cols-3">
                        <article
                            class="group relative overflow-hidden rounded-[34px] border border-[#63102a]/8 bg-[linear-gradient(180deg,#ffffff_0%,#fdf7f0_100%)] p-7 shadow-[0_20px_40px_rgba(99,16,42,0.06)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_26px_54px_rgba(99,16,42,0.12)]">
                            <div
                                class="absolute right-0 top-0 h-28 w-28 translate-x-1/3 -translate-y-1/3 rounded-full bg-[#bc955c]/12 blur-2xl">
                            </div>

                            <div
                                class="relative flex h-12 w-12 items-center justify-center rounded-2xl bg-[#fff0d8] text-[#bc955c] transition group-hover:scale-105">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3M5 11h14M7 21h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>

                            <p class="relative mt-5 text-[11px] font-bold uppercase tracking-[0.14em] text-[#bc955c]">
                                Eventos
                            </p>
                            <h3 class="relative mt-3 text-3xl font-black text-[#201815]">
                                Agenda cultural y comunitaria
                            </h3>
                            <p class="relative mt-4 text-[15px] leading-7 text-[#4f0c22]/72">
                                Encuentra actividades próximas, eventos destacados, ubicaciones, fechas y horarios desde
                                una sola
                                vista.
                            </p>

                            <a href="{{ $frontendEventsUrl }}"
                                class="relative mt-6 inline-flex items-center gap-2 text-sm font-black text-[#63102a] transition hover:gap-3">
                                Ver eventos
                                <span aria-hidden="true">→</span>
                            </a>
                        </article>

                        <article
                            class="group relative overflow-hidden rounded-[34px] border border-[#63102a]/8 bg-[linear-gradient(180deg,#fffaf6_0%,#f7eef1_100%)] p-7 shadow-[0_20px_40px_rgba(99,16,42,0.06)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_26px_54px_rgba(99,16,42,0.12)]">
                            <div
                                class="absolute right-0 top-0 h-28 w-28 translate-x-1/3 -translate-y-1/3 rounded-full bg-[#63102a]/8 blur-2xl">
                            </div>

                            <div
                                class="relative flex h-12 w-12 items-center justify-center rounded-2xl bg-[#f7ecd8] text-[#63102a] transition group-hover:scale-105">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253z" />
                                </svg>
                            </div>

                            <p class="relative mt-5 text-[11px] font-bold uppercase tracking-[0.14em] text-[#bc955c]">
                                Historia
                            </p>
                            <h3 class="relative mt-3 text-3xl font-black text-[#201815]">
                                Conoce la historia de la ciudad
                            </h3>
                            <p class="relative mt-4 text-[15px] leading-7 text-[#4f0c22]/72">
                                Consulta relatos, datos históricos y líneas del tiempo para entender mejor la identidad
                                y evolución
                                de Nezahualcóyotl.
                            </p>
                        </article>

                        <article
                            class="group relative overflow-hidden rounded-[34px] border border-[#63102a]/8 bg-[linear-gradient(180deg,#f9fff8_0%,#eef9f5_100%)] p-7 shadow-[0_20px_40px_rgba(99,16,42,0.06)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_26px_54px_rgba(99,16,42,0.12)]">
                            <div
                                class="absolute right-0 top-0 h-28 w-28 translate-x-1/3 -translate-y-1/3 rounded-full bg-[#235b4e]/9 blur-2xl">
                            </div>

                            <div
                                class="relative flex h-12 w-12 items-center justify-center rounded-2xl bg-[#ecf6f2] text-[#235b4e] transition group-hover:scale-105">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                </svg>
                            </div>

                            <p class="relative mt-5 text-[11px] font-bold uppercase tracking-[0.14em] text-[#bc955c]">
                                Utilidad diaria
                            </p>
                            <h3 class="relative mt-3 text-3xl font-black text-[#201815]">
                                Transporte y tianguis de hoy
                            </h3>
                            <p class="relative mt-4 text-[15px] leading-7 text-[#4f0c22]/72">
                                Consulta rutas, referencias de movilidad y tianguis activos según el día para organizar
                                mejor tus
                                recorridos.
                            </p>
                        </article>
                    </div>
                </div>
            </section>


            <section id="comercios" class="relative overflow-hidden bg-[#fbf5eb] py-24">
                <div
                    class="pointer-events-none absolute inset-x-0 top-0 h-px bg-[linear-gradient(90deg,transparent,rgba(188,149,92,0.45),transparent)]">
                </div>
                <div
                    class="pointer-events-none absolute -right-24 top-20 h-72 w-72 rounded-full bg-[#63102a]/10 blur-3xl">
                </div>
                <div
                    class="pointer-events-none absolute -left-24 bottom-10 h-72 w-72 rounded-full bg-[#bc955c]/12 blur-3xl">
                </div>

                <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div
                        class="grid gap-10 rounded-[44px] border border-[#63102a]/8 bg-white/85 p-6 shadow-[0_28px_70px_rgba(99,16,42,0.08)] backdrop-blur sm:p-8 lg:grid-cols-[minmax(0,1fr)_380px] lg:items-stretch">
                        <div class="py-2 lg:py-4">
                            <span
                                class="inline-flex rounded-full bg-[#63102a]/8 px-4 py-2 text-[11px] font-black uppercase tracking-[0.18em] text-[#63102a]">
                                Para negocios locales
                            </span>

                            <h2 class="mt-5 max-w-4xl text-4xl font-black leading-tight text-[#201815] sm:text-5xl">
                                Haz que más personas encuentren tu negocio en ExploraNeza.
                            </h2>

                            <p class="mt-5 max-w-3xl text-lg leading-8 text-[#4f0c22]/72">
                                Si tienes un comercio, servicio o espacio local en Nezahualcóyotl, puedes conocer cómo
                                aparecer en
                                la app, mostrar información útil y compartir promociones con la comunidad.
                            </p>

                            <div class="mt-10 grid gap-5 md:grid-cols-3">
                                <article
                                    class="group rounded-[28px] border border-[#63102a]/8 bg-[#fbf5eb] p-5 transition duration-300 hover:-translate-y-1 hover:bg-white hover:shadow-[0_18px_38px_rgba(99,16,42,0.08)]">
                                    <div
                                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white text-[#63102a] shadow-sm transition group-hover:scale-105">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>

                                    <h3 class="mt-4 text-lg font-black text-[#201815]">
                                        Aparece en el mapa
                                    </h3>
                                    <p class="mt-2 text-sm leading-6 text-[#4f0c22]/72">
                                        Ayuda a que vecinos y visitantes encuentren tu local y sepan cómo llegar.
                                    </p>
                                </article>

                                <article
                                    class="group rounded-[28px] border border-[#63102a]/8 bg-[#fbf5eb] p-5 transition duration-300 hover:-translate-y-1 hover:bg-white hover:shadow-[0_18px_38px_rgba(99,16,42,0.08)]">
                                    <div
                                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white text-[#bc955c] shadow-sm transition group-hover:scale-105">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>

                                    <h3 class="mt-4 text-lg font-black text-[#201815]">
                                        Muestra lo que ofreces
                                    </h3>
                                    <p class="mt-2 text-sm leading-6 text-[#4f0c22]/72">
                                        Comparte fotos, descripción, horarios, dirección y datos clave para tus
                                        clientes.
                                    </p>
                                </article>

                                <article
                                    class="group rounded-[28px] border border-[#63102a]/8 bg-[#fbf5eb] p-5 transition duration-300 hover:-translate-y-1 hover:bg-white hover:shadow-[0_18px_38px_rgba(99,16,42,0.08)]">
                                    <div
                                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white text-[#235b4e] shadow-sm transition group-hover:scale-105">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-10c1.11 0 2.08.402 2.599 1M12 8V7m0 11v-1m0 0c-1.11 0-2.08-.402-2.599-1" />
                                        </svg>
                                    </div>

                                    <h3 class="mt-4 text-lg font-black text-[#201815]">
                                        Publica beneficios
                                    </h3>
                                    <p class="mt-2 text-sm leading-6 text-[#4f0c22]/72">
                                        Da a conocer cupones, descuentos o participación en sellos y recorridos locales.
                                    </p>
                                </article>
                            </div>

                            <div
                                class="mt-8 rounded-[28px] border border-[#63102a]/8 bg-[#fff9ef] p-5 text-sm leading-7 text-[#4f0c22]/72">
                                ExploraNeza está pensada para conectar a la población con los negocios, servicios y
                                espacios que dan
                                vida a Nezahualcóyotl.
                            </div>
                        </div>

                        <aside
                            class="relative overflow-hidden rounded-[36px] bg-[linear-gradient(180deg,#63102a_0%,#7f173c_100%)] p-7 text-white shadow-[0_24px_54px_rgba(99,16,42,0.18)]">
                            <div
                                class="absolute right-0 top-0 h-28 w-28 translate-x-1/3 -translate-y-1/3 rounded-full bg-[#f2cf91]/18 blur-2xl">
                            </div>
                            <div
                                class="absolute bottom-0 left-0 h-28 w-28 -translate-x-1/3 translate-y-1/3 rounded-full bg-white/10 blur-2xl">
                            </div>

                            <p class="relative text-[11px] font-bold uppercase tracking-[0.14em] text-[#f2cf91]">
                                Página para negocios
                            </p>

                            <h3 class="relative mt-4 text-3xl font-black leading-tight">
                                Conoce cómo registrar o promover tu comercio.
                            </h3>

                            <p class="relative mt-4 text-sm leading-7 text-white/82">
                                Entra a la página para comercios y revisa cómo tu negocio puede integrarse al ecosistema
                                de
                                ExploraNeza.
                            </p>

                            <div class="relative mt-6 space-y-3">
                                <div class="rounded-2xl border border-white/10 bg-white/8 p-4">
                                    <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#f2cf91]">
                                        Ideal para
                                    </p>
                                    <p class="mt-2 text-sm leading-6 text-white/78">
                                        Restaurantes, tiendas, servicios, mercados, espacios culturales y comercios
                                        locales.
                                    </p>
                                </div>

                                <div class="rounded-2xl border border-white/10 bg-white/8 p-4">
                                    <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#f2cf91]">
                                        Puedes mostrar
                                    </p>
                                    <p class="mt-2 text-sm leading-6 text-white/78">
                                        Fotos, horarios, dirección, descripción, promociones y participación en rutas.
                                    </p>
                                </div>
                            </div>

                            <a href="{{ $commerceLandingUrl }}"
                                class="relative mt-6 inline-flex w-full items-center justify-center rounded-xl bg-[#f2cf91] px-5 py-3 text-sm font-black text-[#63102a] shadow-[0_16px_34px_rgba(188,149,92,0.18)] transition hover:-translate-y-0.5 hover:bg-white">
                                Ver página para comercios
                            </a>
                        </aside>
                    </div>
                </div>
            </section>



            <section id="instala" class="relative overflow-hidden bg-[#fbf5eb] py-24" x-data="{ os: 'android' }">
                <div
                    class="pointer-events-none absolute left-0 top-16 h-72 w-72 -translate-x-1/2 rounded-full bg-[#63102a]/8 blur-3xl">
                </div>
                <div
                    class="pointer-events-none absolute right-0 bottom-10 h-72 w-72 translate-x-1/2 rounded-full bg-[#bc955c]/12 blur-3xl">
                </div>
                <div class="relative mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                    <div class="mx-auto mb-14 max-w-3xl text-center"> <span
                            class="inline-flex rounded-full bg-[#63102a]/8 px-4 py-2 text-[11px] font-black uppercase tracking-[0.18em] text-[#63102a]">
                            Instálala en tu celular </span>
                        <h2 class="mt-5 text-4xl font-black leading-tight text-[#201815] sm:text-5xl"> Lleva
                            ExploraNeza en tu pantalla de inicio. </h2>
                        <p class="mx-auto mt-5 max-w-2xl text-lg leading-8 text-[#4f0c22]/70"> Puedes abrir ExploraNeza
                            como si fuera una app, sin buscar el enlace cada vez. Elige tu tipo de celular y sigue los
                            pasos. </p>
                        <div
                            class="mt-8 inline-flex rounded-2xl border border-[#63102a]/8 bg-white/70 p-1 shadow-[0_14px_32px_rgba(99,16,42,0.06)]">
                            <button type="button" @click="os = 'android'"
                                :class="os === 'android' ? 'bg-[#63102a] text-white shadow-md' :
                                    'text-[#4f0c22]/60 hover:text-[#63102a]'"
                                class="rounded-xl px-6 py-2.5 text-sm font-black transition-all"> Android </button>
                            <button type="button" @click="os = 'ios'"
                                :class="os === 'ios' ? 'bg-[#63102a] text-white shadow-md' :
                                    'text-[#4f0c22]/60 hover:text-[#63102a]'"
                                class="rounded-xl px-6 py-2.5 text-sm font-black transition-all"> iPhone </button>
                        </div>
                    </div>
                    <div
                        class="mb-12 rounded-[34px] border border-[#63102a]/8 bg-white/80 p-5 text-center shadow-[0_18px_40px_rgba(99,16,42,0.06)] sm:p-6">
                        <p class="text-sm font-bold uppercase tracking-[0.16em] text-[#bc955c]"> Antes de empezar </p>
                        <p class="mt-2 text-base leading-7 text-[#4f0c22]/72"
                            x-text="os === 'android' ? 'Abre ExploraNeza en Chrome desde tu celular Android para que aparezca la opción de instalar.' : 'Abre ExploraNeza en Safari desde tu iPhone para poder agregarla a tu pantalla de inicio.'">
                        </p>
                        <a href="{{ $frontendUrl }}"
                            class="mt-5 inline-flex items-center justify-center rounded-xl bg-[#63102a] px-5 py-3 text-sm font-black text-white shadow-[0_14px_28px_rgba(99,16,42,0.18)] transition hover:-translate-y-0.5 hover:bg-[#4f0c22]">
                            Abrir ExploraNeza
                        </a>
                    </div>
                    <div class="relative">
                        <div
                            class="hidden lg:block absolute left-1/2 top-8 bottom-8 w-px -translate-x-1/2 border-l-2 border-dashed border-[#bc955c]/30">
                        </div>
                        <div class="relative grid gap-10 lg:grid-cols-2 lg:items-center lg:gap-14">
                            <article class="lg:text-right">
                                <div class="inline-flex items-center gap-3 lg:flex-row-reverse"> <span
                                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#63102a] text-lg font-black text-white shadow-[0_16px_34px_rgba(99,16,42,0.18)]">
                                        01 </span>
                                    <p class="text-[11px] font-black uppercase tracking-[0.16em] text-[#bc955c]"> Abre
                                        la app </p>
                                </div>
                                <h3 class="mt-4 text-2xl font-black text-[#201815]"> Entra a ExploraNeza desde el
                                    navegador correcto </h3>
                                <p class="mt-4 text-lg leading-relaxed text-[#4f0c22]/70"
                                    x-text="os === 'android' ? 'En Android, abre ExploraNeza desde Chrome para iniciar la instalación.' : 'En iPhone, abre ExploraNeza desde Safari para poder agregarla al inicio.'">
                                </p>
                            </article>
                            <div class="flex justify-center lg:justify-start">
                                <div
                                    class="relative w-full max-w-[280px] overflow-hidden rounded-[2.5rem] border-[6px] border-[#201815] bg-white p-3 shadow-2xl">
                                    <div class="aspect-[9/19] overflow-hidden rounded-[1.8rem] bg-gray-100"> <img
                                            :src="os === 'android' ? '{{ $installAndroidStep1 }}' : '{{ $installIosStep1 }}'"
                                            alt="Paso 1 para instalar ExploraNeza"
                                            class="h-full w-full object-contain"> </div>
                                </div>
                            </div>
                        </div>
                        <div class="relative mt-24 grid gap-10 lg:grid-cols-2 lg:items-center lg:gap-14">
                            <article class="lg:order-2">
                                <div class="inline-flex items-center gap-3"> <span
                                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#bc955c] text-lg font-black text-white shadow-[0_16px_34px_rgba(188,149,92,0.2)]">
                                        02 </span>
                                    <p class="text-[11px] font-black uppercase tracking-[0.16em] text-[#bc955c]"> Abre
                                        opciones </p>
                                </div>
                                <h3 class="mt-4 text-2xl font-black text-[#201815]"> Busca el menú para instalar o
                                    compartir </h3>
                                <p class="mt-4 text-lg leading-relaxed text-[#4f0c22]/70"
                                    x-text="os === 'android' ? 'Toca los tres puntos verticales de Chrome para abrir las opciones del navegador.' : 'Toca el ícono de compartir de Safari para ver las acciones disponibles.'">
                                </p>
                            </article>
                            <div class="flex justify-center lg:order-1 lg:justify-end">
                                <div
                                    class="relative w-full max-w-[280px] overflow-hidden rounded-[2.5rem] border-[6px] border-[#201815] bg-white p-3 shadow-2xl">
                                    <div class="aspect-[9/19] overflow-hidden rounded-[1.8rem] bg-gray-100"> <img
                                            :src="os === 'android' ? '{{ $installAndroidStep2 }}' : '{{ $installIosStep2 }}'"
                                            alt="Paso 2 para instalar ExploraNeza"
                                            class="h-full w-full object-contain"> </div>
                                </div>
                            </div>
                        </div>
                        <div class="relative mt-24 grid gap-10 lg:grid-cols-2 lg:items-center lg:gap-14">
                            <article class="lg:text-right">
                                <div class="inline-flex items-center gap-3 lg:flex-row-reverse"> <span
                                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#63102a] text-lg font-black text-white shadow-[0_16px_34px_rgba(99,16,42,0.18)]">
                                        03 </span>
                                    <p class="text-[11px] font-black uppercase tracking-[0.16em] text-[#bc955c]">
                                        Agrega al inicio </p>
                                </div>
                                <h3 class="mt-4 text-2xl font-black text-[#201815]"> Selecciona la opción para instalar
                                </h3>
                                <p class="mt-4 text-lg leading-relaxed text-[#4f0c22]/70"
                                    x-text="os === 'android' ? 'Elige Instalar aplicación o Agregar a pantalla principal, según aparezca en tu celular.' : 'Elige Agregar a pantalla de inicio dentro del menú de compartir.'">
                                </p>
                            </article>
                            <div class="flex justify-center lg:justify-start">
                                <div
                                    class="relative w-full max-w-[280px] overflow-hidden rounded-[2.5rem] border-[6px] border-[#201815] bg-white p-3 shadow-2xl">
                                    <div class="aspect-[9/19] overflow-hidden rounded-[1.8rem] bg-gray-100"> <img
                                            :src="os === 'android' ? '{{ $installAndroidStep3 }}' : '{{ $installIosStep3 }}'"
                                            alt="Paso 3 para instalar ExploraNeza"
                                            class="h-full w-full object-contain"> </div>
                                </div>
                            </div>
                        </div>
                        <div x-show="os === 'android'" x-cloak
                            class="relative mt-24 grid gap-10 lg:grid-cols-2 lg:items-center lg:gap-14">
                            <article class="lg:order-2">
                                <div class="inline-flex items-center gap-3"> <span
                                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#bc955c] text-lg font-black text-white shadow-[0_16px_34px_rgba(188,149,92,0.2)]">
                                        04 </span>
                                    <p class="text-[11px] font-black uppercase tracking-[0.16em] text-[#bc955c]">
                                        Confirma </p>
                                </div>
                                <h3 class="mt-4 text-2xl font-black text-[#201815]"> Revisa la instalación y acepta
                                </h3>
                                <p class="mt-4 text-lg leading-relaxed text-[#4f0c22]/70">
                                    En Android, confirma la opción para instalar y espera a que tu celular termine el
                                    proceso.
                                </p>
                            </article>
                            <div class="flex justify-center lg:order-1 lg:justify-end">
                                <div
                                    class="relative w-full max-w-[280px] overflow-hidden rounded-[2.5rem] border-[6px] border-[#201815] bg-white p-3 shadow-2xl">
                                    <div class="aspect-[9/19] overflow-hidden rounded-[1.8rem] bg-gray-100"> <img
                                            src="{{ $installAndroidStep4 }}"
                                            alt="Paso 4 para instalar ExploraNeza en Android"
                                            class="h-full w-full object-contain"> </div>
                                </div>
                            </div>
                        </div>
                        <div x-show="os === 'android'" x-cloak
                            class="relative mt-24 grid gap-10 lg:grid-cols-2 lg:items-center lg:gap-14">
                            <article class="lg:text-right">
                                <div class="inline-flex items-center gap-3 lg:flex-row-reverse"> <span
                                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#63102a] text-lg font-black text-white shadow-[0_16px_34px_rgba(99,16,42,0.18)]">
                                        05 </span>
                                    <p class="text-[11px] font-black uppercase tracking-[0.16em] text-[#bc955c]">
                                        Termina </p>
                                </div>
                                <h3 class="mt-4 text-2xl font-black text-[#201815]"> Busca el icono en tu inicio </h3>
                                <p class="mt-4 text-lg leading-relaxed text-[#4f0c22]/70">
                                    Cuando termine la instalación en Android, ExploraNeza quedará lista en tu pantalla
                                    principal.
                                </p>
                            </article>
                            <div class="flex justify-center lg:justify-start">
                                <div
                                    class="relative w-full max-w-[280px] overflow-hidden rounded-[2.5rem] border-[6px] border-[#201815] bg-white p-3 shadow-2xl">
                                    <div class="aspect-[9/19] overflow-hidden rounded-[1.8rem] bg-gray-100"> <img
                                            src="{{ $installAndroidStep5 }}"
                                            alt="Paso 5 para instalar ExploraNeza en Android"
                                            class="h-full w-full object-contain"> </div>
                                </div>
                            </div>
                        </div>
                        <div x-show="os === 'ios'" x-cloak
                            class="relative mt-24 grid gap-10 lg:grid-cols-2 lg:items-center lg:gap-14">
                            <article class="lg:order-2">
                                <div class="inline-flex items-center gap-3"> <span
                                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#bc955c] text-lg font-black text-white shadow-[0_16px_34px_rgba(188,149,92,0.2)]">
                                        04 </span>
                                    <p class="text-[11px] font-black uppercase tracking-[0.16em] text-[#bc955c]">
                                        Elige compartir </p>
                                </div>
                                <h3 class="mt-4 text-2xl font-black text-[#201815]"> Abre el menú de compartir </h3>
                                <p class="mt-4 text-lg leading-relaxed text-[#4f0c22]/70">
                                    En iPhone, toca el botón de compartir de Safari para ver las opciones disponibles.
                                </p>
                            </article>
                            <div class="flex justify-center lg:order-1 lg:justify-end">
                                <div
                                    class="relative w-full max-w-[280px] overflow-hidden rounded-[2.5rem] border-[6px] border-[#201815] bg-white p-3 shadow-2xl">
                                    <div class="aspect-[9/19] overflow-hidden rounded-[1.8rem] bg-gray-100"> <img
                                            src="{{ $installIosStep4 }}"
                                            alt="Paso 4 para instalar ExploraNeza en iPhone"
                                            class="h-full w-full object-contain"> </div>
                                </div>
                            </div>
                        </div>
                        <div x-show="os === 'ios'" x-cloak
                            class="relative mt-24 grid gap-10 lg:grid-cols-2 lg:items-center lg:gap-14">
                            <article class="lg:text-right">
                                <div class="inline-flex items-center gap-3 lg:flex-row-reverse"> <span
                                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#63102a] text-lg font-black text-white shadow-[0_16px_34px_rgba(99,16,42,0.18)]">
                                        05 </span>
                                    <p class="text-[11px] font-black uppercase tracking-[0.16em] text-[#bc955c]">
                                        Agrega al inicio </p>
                                </div>
                                <h3 class="mt-4 text-2xl font-black text-[#201815]"> Selecciona agregar a inicio </h3>
                                <p class="mt-4 text-lg leading-relaxed text-[#4f0c22]/70">
                                    Busca la opción para agregar ExploraNeza a tu pantalla de inicio y selecciónala.
                                </p>
                            </article>
                            <div class="flex justify-center lg:justify-start">
                                <div
                                    class="relative w-full max-w-[280px] overflow-hidden rounded-[2.5rem] border-[6px] border-[#201815] bg-white p-3 shadow-2xl">
                                    <div class="aspect-[9/19] overflow-hidden rounded-[1.8rem] bg-gray-100"> <img
                                            src="{{ $installIosStep5 }}"
                                            alt="Paso 5 para instalar ExploraNeza en iPhone"
                                            class="h-full w-full object-contain"> </div>
                                </div>
                            </div>
                        </div>
                        <div x-show="os === 'ios'" x-cloak
                            class="relative mt-24 grid gap-10 lg:grid-cols-2 lg:items-center lg:gap-14">
                            <article class="lg:order-2">
                                <div class="inline-flex items-center gap-3"> <span
                                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#bc955c] text-lg font-black text-white shadow-[0_16px_34px_rgba(188,149,92,0.2)]">
                                        06 </span>
                                    <p class="text-[11px] font-black uppercase tracking-[0.16em] text-[#bc955c]">
                                        Confirma </p>
                                </div>
                                <h3 class="mt-4 text-2xl font-black text-[#201815]"> Confirma y revisa el icono </h3>
                                <p class="mt-4 text-lg leading-relaxed text-[#4f0c22]/70">
                                    Confirma el nombre y agrega ExploraNeza para verla lista en el inicio de tu iPhone.
                                </p>
                            </article>
                            <div class="flex justify-center lg:order-1 lg:justify-end">
                                <div
                                    class="relative w-full max-w-[280px] overflow-hidden rounded-[2.5rem] border-[6px] border-[#201815] bg-white p-3 shadow-2xl">
                                    <div class="aspect-[9/19] overflow-hidden rounded-[1.8rem] bg-gray-100"> <img
                                            src="{{ $installIosStep6 }}"
                                            alt="Paso 6 para instalar ExploraNeza en iPhone"
                                            class="h-full w-full object-contain"> </div>
                                </div>
                            </div>
                        </div>
                        <div
                            class="relative mt-20 rounded-[36px] bg-[linear-gradient(135deg,#4f0c22,#63102a_55%,#7f173c)] p-6 text-white shadow-[0_28px_70px_rgba(99,16,42,0.18)] sm:p-8">
                            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-center">
                                <div>
                                    <p class="text-sm font-black uppercase tracking-[0.18em] text-[#f2cf91]"> Listo
                                    </p>
                                    <h3 class="mt-3 text-3xl font-black"> ExploraNeza quedará en tu pantalla de inicio.
                                    </h3>
                                    <p class="mt-3 max-w-3xl text-sm leading-7 text-white/78"
                                        x-text="os === 'android' ? 'Cuando confirmes la instalación, verás el ícono de ExploraNeza junto a tus demás apps.' : 'Cuando confirmes, el ícono de ExploraNeza aparecerá en el inicio de tu iPhone.'">
                                    </p>
                                </div> <a href="{{ $frontendUrl }}"
                                    class="inline-flex items-center justify-center rounded-full bg-[#f2cf91] px-6 py-3 text-sm font-black text-[#63102a] transition hover:bg-white">
                                    Abrir ExploraNeza </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="relative overflow-hidden bg-white py-20">
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
                                    Empieza ahora
                                </p>

                                <h2
                                    class="mt-4 max-w-4xl text-4xl font-black leading-tight tracking-tight sm:text-5xl">
                                    Abre ExploraNeza y descubre qué hay cerca de ti.
                                </h2>

                                <p class="mt-5 max-w-3xl text-base leading-8 text-white/78 sm:text-lg">
                                    Consulta el mapa, guarda beneficios, encuentra eventos, conoce la historia de la
                                    ciudad y
                                    recorre experiencias locales desde tu celular.
                                </p>

                                <div class="mt-6 flex flex-wrap gap-3 text-sm font-semibold text-white/80">
                                    <span class="rounded-full border border-white/12 bg-white/8 px-4 py-2">
                                        Mapa
                                    </span>
                                    <span class="rounded-full border border-white/12 bg-white/8 px-4 py-2">
                                        Cupones
                                    </span>
                                    <span class="rounded-full border border-white/12 bg-white/8 px-4 py-2">
                                        Eventos
                                    </span>
                                    <span class="rounded-full border border-white/12 bg-white/8 px-4 py-2">
                                        Pasaporte
                                    </span>
                                </div>
                            </div>

                            <div class="rounded-[30px] border border-white/12 bg-white/8 p-5 backdrop-blur">
                                <p class="text-sm font-bold leading-6 text-white/82">
                                    ExploraNeza reúne información útil para habitantes, visitantes y negocios locales de
                                    Nezahualcóyotl.
                                </p>

                                <div class="mt-5 rounded-[24px] border border-white/12 bg-white/8 p-4 text-center">
                                    <img src="{{ $installQr }}" alt="QR para abrir ExploraNeza"
                                        class="mx-auto w-full max-w-[220px] rounded-2xl bg-white p-2 shadow-[0_12px_28px_rgba(0,0,0,0.12)]">
                                    <p class="mt-3 text-[11px] font-black uppercase tracking-[0.16em] text-[#f2cf91]">
                                        Escanea el QR
                                    </p>
                                    <p class="mt-2 text-sm leading-6 text-white/78">
                                        Abre ExploraNeza directo en tu celular y luego sigue la instalación.
                                    </p>
                                </div>

                                <div class="mt-5 flex flex-col gap-3">
                                    <a href="{{ $frontendUrl }}"
                                        class="inline-flex items-center justify-center rounded-full bg-white px-6 py-3.5 text-sm font-black text-[#63102a] shadow-[0_16px_34px_rgba(0,0,0,0.12)] transition hover:-translate-y-0.5 hover:bg-[#fbf5eb]">
                                        Abrir ExploraNeza
                                    </a>

                                    <a href="#instala"
                                        class="inline-flex items-center justify-center rounded-full border border-white/16 bg-white/10 px-6 py-3.5 text-sm font-black text-white transition hover:bg-white/16">
                                        Cómo instalarla
                                    </a>

                                    <a href="{{ $commerceLandingUrl }}"
                                        class="inline-flex items-center justify-center rounded-full border border-white/16 bg-transparent px-6 py-3.5 text-sm font-black text-white/90 transition hover:bg-white/10">
                                        Soy comercio
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>


        </main>


        <footer class="border-t border-[#63102a]/8 bg-white/90">
            <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
                <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-start">
                    <div>
                        <p class="text-lg font-black text-[#201815]">
                            ExploraNeza
                        </p>

                        <p class="mt-2 max-w-2xl text-sm leading-7 text-[#4f0c22]/75">
                            La guía digital para descubrir lugares, eventos, rutas, cupones, historia, tianguis,
                            transporte y
                            experiencias locales en Nezahualcóyotl.
                        </p>

                        <p class="mt-4 text-xs font-semibold uppercase tracking-[0.16em] text-[#bc955c]">
                            Explora la ciudad desde tu celular
                        </p>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-[auto_auto] lg:gap-10">
                        <div>
                            <p class="text-xs font-black uppercase tracking-[0.16em] text-[#63102a]">
                                Secciones
                            </p>

                            <div class="mt-4 flex flex-col gap-2 text-sm font-semibold text-[#4f0c22]/75">
                                <a href="#explora" class="transition hover:text-[#63102a]">
                                    Inicio
                                </a>
                                <a href="#mapa" class="transition hover:text-[#63102a]">
                                    Mapa
                                </a>
                                <a href="#pasaporte" class="transition hover:text-[#63102a]">
                                    Pasaporte
                                </a>
                                <a href="#instala" class="transition hover:text-[#63102a]">
                                    Instálala
                                </a>
                            </div>
                        </div>

                        <div>
                            <p class="text-xs font-black uppercase tracking-[0.16em] text-[#63102a]">
                                Accesos
                            </p>

                            <div class="mt-4 flex flex-col gap-2 text-sm font-semibold text-[#4f0c22]/75">
                                <a href="{{ $frontendUrl }}" class="transition hover:text-[#63102a]">
                                    Abrir app
                                </a>
                                <a href="{{ $frontendMapUrl }}" class="transition hover:text-[#63102a]">
                                    Ver mapa
                                </a>
                                <a href="{{ $frontendEventsUrl }}" class="transition hover:text-[#63102a]">
                                    Ver eventos
                                </a>
                                <a href="{{ $commerceLandingUrl }}" class="transition hover:text-[#63102a]">
                                    Soy comercio
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="mt-8 flex flex-col gap-4 border-t border-[#63102a]/8 pt-6 text-xs text-[#4f0c22]/60 sm:flex-row sm:items-center sm:justify-between">
                    <p>
                        © {{ date('Y') }} ExploraNeza. Todos los derechos reservados.
                    </p>

                    <div class="flex flex-wrap gap-4">
                        <a href="{{ $frontendUrl }}"
                            class="font-bold text-[#63102a] transition hover:text-[#4f0c22]">
                            Abrir ExploraNeza
                        </a>
                        <a href="#instala" class="font-bold text-[#63102a] transition hover:text-[#4f0c22]">
                            Instalar en celular
                        </a>
                    </div>
                </div>
            </div>
        </footer>


    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const mapElement = document.getElementById('landing-map');
            const loadingElement = document.getElementById('landing-map-loading');
            const countElement = document.getElementById('landing-map-count');
            const filtersElement = document.getElementById('landing-map-filters');

            if (!mapElement || typeof L === 'undefined') {
                return;
            }

            const map = L.map(mapElement, {
                zoomControl: true,
                scrollWheelZoom: false,
            }).setView([19.4006, -99.0148], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap',
            }).addTo(map);

            const colorByCategory = (category) => {
                const normalized = String(category || '').toLowerCase();

                if (normalized.includes('establecimiento')) return '#63102a';
                if (normalized.includes('evento')) return '#bc955c';
                if (normalized.includes('mercado')) return '#235b4e';
                if (normalized.includes('hospital')) return '#d97706';
                return '#8b5cf6';
            };

            const escapeHtml = (value) => String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');

            try {
                const response = await fetch('/api/puntos-mapa', {
                    headers: {
                        Accept: 'application/json',
                    },
                });

                if (!response.ok) {
                    throw new Error('No se pudieron cargar los puntos del mapa.');
                }

                const items = await response.json();
                const validItems = Array.isArray(items) ?
                    items.filter((item) =>
                        Array.isArray(item.position) &&
                        item.position.length === 2 &&
                        Number.isFinite(Number(item.position[0])) &&
                        Number.isFinite(Number(item.position[1]))) : [];

                const bounds = [];
                const markerEntries = [];
                const categories = Array.from(new Set(validItems
                        .map((item) => String(item.category || 'Otros').trim())
                        .filter(Boolean)))
                    .sort((a, b) => a.localeCompare(b, 'es'));

                validItems.forEach((item) => {
                    const lat = Number(item.position[0]);
                    const lng = Number(item.position[1]);
                    const marker = L.marker([lat, lng], {
                        icon: L.divIcon({
                            className: '',
                            html: `<span class="landing-map-marker" style="display:block;background:${colorByCategory(item.category)}"></span>`,
                            iconSize: [16, 16],
                            iconAnchor: [8, 8],
                        }),
                    });

                    const popup = `
                        <div class="space-y-2">
                            <p style="margin:0;font-size:11px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:#bc955c;">
                                ${escapeHtml(item.category || 'Punto de interes')}
                            </p>
                            <h3 style="margin:0;font-size:16px;font-weight:800;color:#201815;">
                                ${escapeHtml(item.name || 'Ubicacion')}
                            </h3>
                            ${item.address ? `<p style="margin:0;font-size:13px;line-height:1.5;color:rgba(79,12,34,.8);">${escapeHtml(item.address)}</p>` : ''}
                            ${item.description ? `<p style="margin:8px 0 0;font-size:13px;line-height:1.5;color:rgba(79,12,34,.7);">${escapeHtml(item.description)}</p>` : ''}
                        </div>
                    `;

                    marker.bindPopup(popup);
                    marker.addTo(map);
                    markerEntries.push({
                        marker,
                        item,
                        category: String(item.category || 'Otros').trim() || 'Otros',
                        position: [lat, lng],
                    });
                    bounds.push([lat, lng]);
                });

                const renderCount = (total) => {
                    if (countElement) {
                        countElement.textContent = `${total} puntos`;
                    }
                };

                const setActiveFilterStyles = (button, active) => {
                    button.className = active ?
                        'flex items-center gap-2 rounded-2xl bg-[#63102a] px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-[#63102a]/20 transition' :
                        'flex items-center gap-2 rounded-2xl border border-[#63102a]/10 bg-white px-4 py-2.5 text-xs font-bold text-[#201815] shadow-sm transition hover:border-[#63102a]/20 hover:bg-[#fff9ef]';
                };

                const applyFilter = (category) => {
                    const filteredEntries = markerEntries.filter((entry) =>
                        category === 'all' || entry.category === category);

                    markerEntries.forEach((entry) => {
                        if (filteredEntries.includes(entry)) {
                            entry.marker.addTo(map);
                        } else {
                            map.removeLayer(entry.marker);
                        }
                    });

                    if (filteredEntries.length > 0) {
                        map.fitBounds(filteredEntries.map((entry) => entry.position), {
                            padding: [40, 40],
                        });
                    }

                    renderCount(filteredEntries.length);

                    if (filtersElement) {
                        Array.from(filtersElement.querySelectorAll('button')).forEach((button) => {
                            setActiveFilterStyles(button, button.dataset.category === category);
                        });
                    }
                };

                if (filtersElement) {
                    filtersElement.innerHTML = '';

                    const createFilterButton = (label, category, color, active = false) => {
                        const button = document.createElement('button');
                        button.type = 'button';
                        button.dataset.category = category;
                        button.innerHTML =
                            `<span class="h-2 w-2 rounded-full" style="background:${color}"></span><span>${escapeHtml(label)}</span>`;
                        setActiveFilterStyles(button, active);
                        button.addEventListener('click', () => applyFilter(category));
                        filtersElement.appendChild(button);
                    };

                    createFilterButton('Todos', 'all', '#f2cf91', true);

                    categories.forEach((category) => {
                        createFilterButton(category, category, colorByCategory(category));
                    });
                }

                if (bounds.length > 0) {
                    map.fitBounds(bounds, {
                        padding: [40, 40],
                    });
                }

                renderCount(validItems.length);
            } catch (error) {
                if (countElement) {
                    countElement.textContent = 'Sin datos';
                }

                if (loadingElement) {
                    loadingElement.textContent = 'No fue posible cargar el mapa por ahora.';
                }

                return;
            }

            if (loadingElement) {
                loadingElement.remove();
            }

            setTimeout(() => {
                map.invalidateSize();
            }, 120);
        });
    </script>
</body>

</html>
