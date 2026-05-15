<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ExploraNeza | Descubre Nezahualcoyotl desde una sola app</title>
    <meta name="description"
        content="Explora eventos, historia, rutas, lugares, cupones y experiencias de Nezahualcoyotl desde ExploraNeza.">
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

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
            width: 0;
            height: 0;
            background: transparent;
        }
    </style>
</head>

<body class="bg-[#fbf5eb] text-[#201815] antialiased">
    @php
        $frontendUrl = rtrim((string) env('FRONTEND_URL', config('app.url')), '/');
        $logoLanding = asset('img/landing/logo-landing.png');
        $heroPhone = asset('img/landing/tel-hero.png');
        $heroPhone2 = asset('img/landing/pasaporte-home.png');
        $heroVector = asset('img/landing/Vector.png');
        $mapPreview = asset('img/landing/mapas.png');
        $installStep1 = asset('img/landing/paso 1.jpeg');
        $installStep2 = asset('img/landing/paso 2.jpeg');
        $installStep3 = asset('img/landing/paso 3.jpeg');
        $installStep4 = asset('img/landing/paso 4.jpeg');
    @endphp

    <div class="relative overflow-hidden bg-[#fbf5eb]">
        <div
            class="pointer-events-none absolute inset-x-0 top-0 h-[680px] bg-[radial-gradient(circle_at_top_left,rgba(188,149,92,0.16),transparent_35%),radial-gradient(circle_at_top_right,rgba(99,16,42,0.12),transparent_30%)]">
        </div>

        <header
            class="sticky top-0 z-40 border-b border-[#63102a]/10 bg-[#ffffff]/88 shadow-[0_10px_30px_rgba(99,16,42,0.05)] backdrop-blur-xl">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('landing') }}"
                    class="flex shrink-0 items-center rounded-2xl px-2 py-1 transition hover:bg-[#f7ecd8]">
                    <img src="{{ $logoLanding }}" alt="ExploraNeza" class="h-12 w-auto sm:h-14">
                </a>

                <nav
                    class="glass-card hidden items-center gap-2 rounded-full border border-[#63102a]/10 bg-[#fbf5eb]/86 p-2 text-[13px] font-semibold text-[#201815] shadow-[0_14px_28px_rgba(99,16,42,0.05)] lg:flex">
                    <a href="#explora"
                        class="rounded-full px-4 py-2 transition hover:bg-[#ffffff] hover:text-[#63102a]">Explora</a>
                    <a href="#mundial-2026"
                        class="rounded-full px-4 py-2 transition hover:bg-[#ffffff] hover:text-[#63102a]">Mundial
                        2026</a>
                    <a href="#funciones"
                        class="rounded-full px-4 py-2 transition hover:bg-[#ffffff] hover:text-[#63102a]">Funciones</a>
                    <a href="#mapa"
                        class="rounded-full px-4 py-2 transition hover:bg-[#ffffff] hover:text-[#63102a]">Mapa</a>
                </nav>

                <div class="flex items-center gap-3">
                    <button type="button"
                        class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-[#63102a]/10 bg-[#fbf5eb] text-[#63102a] lg:hidden"
                        aria-label="Abrir menú">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"
                            aria-hidden="true">
                            <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" />
                        </svg>
                    </button>

                    <a href="{{ $frontendUrl }}"
                        class="shine-wrap inline-flex items-center justify-center rounded-xl bg-[#63102a] px-5 py-3 text-sm font-semibold text-white shadow-[0_14px_28px_rgba(99,16,42,0.22)] transition hover:-translate-y-0.5 hover:bg-[#4f0c22] sm:px-6">
                        Instalar APP
                    </a>
                </div>
            </div>
        </header>

        <main>
            <section id="explora" class="relative overflow-hidden bg-[#63102a] bg-cover bg-center bg-no-repeat"
                style="background-image: url('{{ $heroVector }}');">
                <div
                    class="pointer-events-none absolute left-[6%] top-16 h-32 w-32 rounded-full bg-[#f2cf91]/12 blur-3xl fx-glow">
                </div>
                <div
                    class="pointer-events-none absolute right-[10%] top-20 h-36 w-36 rounded-full bg-white/10 blur-3xl fx-glow">
                </div>
                <div
                    class="mx-auto grid max-w-7xl items-center gap-8 px-4 pb-14 pt-6 sm:px-6 lg:grid-cols-[minmax(0,1fr)_390px] lg:px-8 lg:pb-16 lg:pt-2">
                    <div class="relative z-10 py-10 text-white lg:py-12 fx-appear">
                        <div
                            class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/8 px-4 py-2 text-[15px] font-extrabold tracking-tight text-[#f2cf91] glass-card">
                            <span class="h-2 w-2 rounded-full bg-[#f2cf91]"></span>
                            Por Amor a Neza
                        </div>

                        <h1 class="mt-4 max-w-[560px] text-4xl font-extrabold leading-tight sm:text-5xl lg:text-[51px]">
                            Descubre el corazón de México en Nezahualcóyotl.
                        </h1>

                        <p class="mt-5 max-w-[560px] text-lg leading-8 text-white/92">
                            Tu guía oficial para explorar la historia, la gastronomía y la cultura de Neza. Todo en la
                            palma de tu mano.
                        </p>

                        <a href="{{ $frontendUrl }}"
                            class="shine-wrap mt-8 inline-flex items-center justify-center rounded-xl bg-[#f2cf91] px-5 py-3 text-[15px] font-bold text-[#63102a] shadow-[0_16px_34px_rgba(188,149,92,0.22)] transition hover:-translate-y-0.5 hover:bg-[#bc955c] hover:text-white">
                            Instalar App
                        </a>
                    </div>

                    <div class="relative z-10 flex justify-center lg:justify-end fx-appear-delay">
                        <div
                            class="pointer-events-none absolute inset-x-8 bottom-4 h-12 rounded-full bg-black/30 blur-2xl">
                        </div>
                        <img src="{{ $heroPhone2 }}" alt="Pasaporte gastronómico mundialista en la app ExploraNeza"
                            class="fx-float w-full max-w-[330px] drop-shadow-[0_26px_38px_rgba(0,0,0,0.42)] sm:max-w-[360px] lg:max-w-[390px] lg:translate-y-3">
                    </div>
                </div>
            </section>

            <section id="mundial-2026" class="relative overflow-hidden bg-[#d7ff00] py-24">
                <div
                    class="absolute inset-0 bg-[linear-gradient(135deg,rgba(255,225,32,0.9)_0%,rgba(215,255,0,0.92)_35%,rgba(109,239,221,0.88)_100%)]">
                </div>
                <div class="absolute inset-x-0 top-0 h-6 bg-[#ffe221]"></div>
                <div class="absolute left-0 top-10 h-56 w-56 rounded-full bg-[#2c2875]/20 blur-3xl"></div>
                <div class="absolute right-0 top-0 h-72 w-72 rounded-full bg-[#ff2a54]/20 blur-3xl"></div>
                <div class="absolute bottom-0 right-24 h-48 w-48 rounded-full bg-[#4d4df0]/20 blur-3xl"></div>
                <div
                    class="pointer-events-none absolute -left-8 top-24 h-40 w-[340px] -rotate-[12deg] rounded-full border-[10px] border-[#ff2a54]/55">
                </div>
                <div
                    class="pointer-events-none absolute right-0 top-12 h-44 w-[280px] rotate-[12deg] rounded-full border-[10px] border-[#4d4df0]/45">
                </div>
                <div
                    class="pointer-events-none absolute bottom-10 left-[14%] h-20 w-20 rounded-full bg-[#ffe221]/70 blur-xl fx-glow">
                </div>

                <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="grid gap-10 lg:grid-cols-[minmax(0,1fr)_420px] lg:items-center">
                        <div class="max-w-3xl fx-appear">

                            <div
                                class="inline-flex items-center gap-2 rounded-full border-2 border-[#2c2875]/14 bg-white/70 px-4 py-2 text-sm font-black uppercase tracking-[0.24em] text-[#ff2a54] glass-card">
                                <span class="h-2.5 w-2.5 rounded-full bg-[#ff2a54]"></span>
                                Pasaporte Gastronómico Mundialista
                            </div>
                            <h2
                                class="mt-4 max-w-2xl text-4xl font-black uppercase leading-[0.95] text-[#2c2875] sm:text-5xl lg:text-6xl">
                                Sabores de Neza con espíritu de mundial.
                            </h2>
                            <p class="mt-6 max-w-2xl text-lg leading-8 text-[#2c2875]/90">
                                Recorre puestos, cocinas locales y paradas imperdibles con una experiencia inspirada en
                                la energía del Mundial. Sella tu pasaporte, descubre platillos icónicos y arma tu ruta
                                favorita desde el celular.
                            </p>

                            <div class="mt-8 flex flex-col gap-4 sm:flex-row">
                                <a href="{{ $frontendUrl }}"
                                    class="shine-wrap inline-flex items-center justify-center rounded-2xl bg-[#2c2875] px-6 py-3.5 text-sm font-bold uppercase tracking-[0.14em] text-white shadow-[0_18px_30px_rgba(44,40,117,0.24)] transition hover:-translate-y-0.5 hover:bg-[#242063]">
                                    Mas información
                                </a>

                            </div>

                            <div class="mt-10 grid gap-4 sm:grid-cols-3">
                                <article
                                    class="rounded-[28px] border border-white/30 bg-white/82 p-5 shadow-[0_18px_35px_rgba(44,40,117,0.12)] backdrop-blur-sm transition hover:-translate-y-1 hover:shadow-[0_24px_44px_rgba(44,40,117,0.16)]">
                                    <p class="text-xs font-black uppercase tracking-[0.16em] text-[#ff2a54]">01</p>
                                    <h3 class="mt-3 text-lg font-black text-[#2c2875]">Escanea y participa</h3>
                                    <p class="mt-2 text-sm leading-6 text-[#2c2875]/80">Abre el pasaporte en la app y
                                        registra cada parada gastronómica.</p>
                                </article>
                                <article
                                    class="rounded-[28px] border border-white/10 bg-[#2c2875] p-5 text-white shadow-[0_18px_35px_rgba(44,40,117,0.18)] transition hover:-translate-y-1 hover:shadow-[0_24px_44px_rgba(44,40,117,0.22)]">
                                    <p class="text-xs font-black uppercase tracking-[0.16em] text-[#ffe221]">02</p>
                                    <h3 class="mt-3 text-lg font-black">Colecciona sellos</h3>
                                    <p class="mt-2 text-sm leading-6 text-white/82">Suma visitas, desbloquea insignias y
                                        sigue tu progreso en tiempo real.</p>
                                </article>
                                <article
                                    class="rounded-[28px] border border-white/10 bg-[#ff2a54] p-5 text-white shadow-[0_18px_35px_rgba(255,42,84,0.2)] transition hover:-translate-y-1 hover:shadow-[0_24px_44px_rgba(255,42,84,0.24)]">
                                    <p class="text-xs font-black uppercase tracking-[0.16em] text-[#ffe221]">03</p>
                                    <h3 class="mt-3 text-lg font-black">Descubre favoritos</h3>
                                    <p class="mt-2 text-sm leading-6 text-white/86">Conoce tacos, antojitos y postres
                                        que representan el sabor de Neza.</p>
                                </article>
                            </div>
                        </div>

                        <div class="relative flex justify-center lg:justify-end fx-appear-delay">
                            <div class="fx-float absolute left-6 top-8 h-24 w-24 rounded-full bg-[#ff6a3d]"></div>
                            <div
                                class="fx-float-delayed absolute right-6 top-0 h-28 w-28 rounded-full border-[10px] border-[#4d4df0] bg-transparent">
                            </div>
                            <div
                                class="pointer-events-none absolute bottom-4 left-10 right-10 h-12 rounded-full bg-[#2c2875]/30 blur-2xl">
                            </div>

                            <img src="{{ $heroPhone2 }}" alt="Pasaporte gastronómico mundialista en la app ExploraNeza"
                                class="fx-float relative z-10 w-full max-w-[330px] rotate-[6deg] drop-shadow-[0_28px_50px_rgba(44,40,117,0.35)] sm:max-w-[360px] lg:max-w-[410px]">
                        </div>
                    </div>
                </div>
            </section>
<section id="explora-carrusel" class="bg-[#fbf5eb] py-24 overflow-hidden" x-data="carousel()">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="flex items-end justify-between mb-12">
            <div class="max-w-2xl">
                <span class="text-[#bc955c] text-xs font-black uppercase tracking-[0.2em] block mb-3">Funciones ExploraNeza</span>
                <h2 class="text-4xl md:text-5xl font-black text-[#201815]">Explora cada rincón.</h2>
            </div>

            <div class="hidden lg:flex items-center gap-3">
                <button @click="prev()" class="w-14 h-14 rounded-2xl border border-[#63102a]/10 bg-white shadow-sm flex items-center justify-center text-[#63102a] transition hover:bg-[#63102a] hover:text-white group">
                    <svg class="w-6 h-6 transition-transform group-active:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button @click="next()" class="w-14 h-14 rounded-2xl border border-[#63102a]/10 bg-white shadow-sm flex items-center justify-center text-[#63102a] transition hover:bg-[#63102a] hover:text-white group">
                    <svg class="w-6 h-6 transition-transform group-active:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>

        <div id="slider" x-ref="slider" class="flex overflow-x-auto gap-6 pb-8 no-scrollbar snap-x snap-mandatory scroll-smooth">

            <div class="snap-center shrink-0 w-[85vw] md:w-[650px] bg-white rounded-[40px] border border-[#63102a]/5 shadow-xl p-8 md:p-12">
                <div class="grid md:grid-cols-2 gap-10 items-center h-full">
                    <div>
                        <div class="w-14 h-14 bg-[#63102a] rounded-2xl flex items-center justify-center mb-6 text-[#f2cf91] shadow-lg">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <h3 class="text-3xl font-black text-[#201815] mb-4">Historia de Neza</h3>
                        <p class="text-[#4f0c22]/70 text-base leading-relaxed">Relatos y memoria urbana. Entiende cómo se formó la ciudad con líneas del tiempo y galerías.</p>
                    </div>
                    <div class="flex justify-center md:justify-end h-[420px]">
                        <img src="{{ $heroPhone }}" class="h-full w-auto object-contain drop-shadow-2xl">
                    </div>
                </div>
            </div>

            <div class="snap-center shrink-0 w-[85vw] md:w-[650px] bg-[#f2cf91] rounded-[40px] p-8 md:p-12 text-[#63102a]">
                <div class="grid md:grid-cols-2 gap-10 items-center h-full">
                    <div>
                        <div class="w-14 h-14 bg-[#63102a] rounded-2xl flex items-center justify-center mb-6 text-[#f2cf91] shadow-lg">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                        </div>
                        <h3 class="text-3xl font-black mb-4">Cuponera Digital</h3>
                        <p class="text-[#63102a]/80 text-base leading-relaxed mb-6">Activa beneficios locales. Encuentra descuentos en comercios y restaurantes participantes.</p>
                        <div class="inline-flex px-4 py-2 bg-white/40 rounded-xl text-xs font-bold uppercase tracking-wider">Descuentos exclusivos</div>
                    </div>
                    <div class="flex justify-center md:justify-end h-[420px]">
                        <img src="{{ $heroPhone }}" class="h-full w-auto object-contain drop-shadow-2xl">
                    </div>
                </div>
            </div>

            <div class="snap-center shrink-0 w-[85vw] md:w-[650px] bg-white rounded-[40px] border border-[#63102a]/5 shadow-xl p-8 md:p-12">
                <div class="grid md:grid-cols-2 gap-10 items-center h-full">
                    <div>
                        <div class="w-14 h-14 bg-[#235b4e] rounded-2xl flex items-center justify-center mb-6 text-white shadow-lg">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <h3 class="text-3xl font-black text-[#201815] mb-4">Tianguis de Hoy</h3>
                        <p class="text-[#4f0c22]/70 text-base leading-relaxed">¿Dónde toca hoy? Ubica los mercados locales activos y planifica tu ruta de compras.</p>
                    </div>
                    <div class="flex justify-center md:justify-end h-[420px]">
                        <img src="{{ $heroPhone }}" class="h-full w-auto object-contain drop-shadow-2xl">
                    </div>
                </div>
            </div>

            <div class="snap-center shrink-0 w-[85vw] md:w-[650px] bg-[#63102a] rounded-[40px] p-8 md:p-12 text-white overflow-hidden">
                <div class="grid md:grid-cols-2 gap-10 items-center h-full">
                    <div>
                        <div class="w-14 h-14 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center mb-6 text-[#f2cf91] border border-white/10">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="text-3xl font-black mb-4">Agenda Cultural</h3>
                        <p class="text-white/70 text-base leading-relaxed">No te pierdas nada. Encuentra festivales, eventos deportivos y exposiciones cerca de ti.</p>
                    </div>
                    <div class="flex justify-center md:justify-end h-[420px]">
                        <img src="{{ $heroPhone }}" class="h-full w-auto object-contain drop-shadow-2xl">
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
<script>
    function carousel() {
        return {
            next() {
                const slider = this.$refs.slider;
                if (!slider) return;
                const amount = Math.min(slider.clientWidth * 0.9, 700);
                slider.scrollBy({ left: amount, behavior: 'smooth' });
            },
            prev() {
                const slider = this.$refs.slider;
                if (!slider) return;
                const amount = Math.min(slider.clientWidth * 0.9, 700);
                slider.scrollBy({ left: -amount, behavior: 'smooth' });
            }
        }
    }
</script>

           <section id="mapa" class="relative bg-[#fbf5eb] py-24 overflow-hidden">
    <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-[#63102a]/5 rounded-full blur-[120px] -translate-y-1/2 translate-x-1/2"></div>

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid gap-16 lg:grid-cols-12 lg:items-center">

            <div class="lg:col-span-5">
                <span class="inline-block px-4 py-1.5 rounded-full bg-[#bc955c]/10 text-[#bc955c] text-xs font-bold uppercase tracking-widest mb-6">
                    Geo-Exploración
                </span>
                <h2 class="text-4xl md:text-5xl font-black text-[#201815] leading-tight mb-8">
                    La ciudad entera bajo tu control.
                </h2>
                <p class="text-lg text-[#4f0c22]/70 leading-relaxed mb-10">
                    No es solo un mapa, es tu centro de navegación. Filtra por capas para encontrar exactamente lo que necesitas en tiempo real.
                </p>

                <div class="space-y-8">
                    <div class="flex gap-5">
                        <div class="flex-shrink-0 w-12 h-12 bg-white rounded-2xl shadow-sm border border-[#63102a]/5 flex items-center justify-center text-[#63102a]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-[#201815]">Movilidad Inteligente</h3>
                            <p class="text-sm text-[#4f0c22]/70 mt-1">Rutas optimizadas y estaciones de transporte para moverte sin perderte.</p>
                        </div>
                    </div>

                    <div class="flex gap-5">
                        <div class="flex-shrink-0 w-12 h-12 bg-white rounded-2xl shadow-sm border border-[#63102a]/5 flex items-center justify-center text-[#235b4e]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-[#201815]">Red de Seguridad</h3>
                            <p class="text-sm text-[#4f0c22]/70 mt-1">Ubicación inmediata de servicios de emergencia, policías y bomberos.</p>
                        </div>
                    </div>

                    <div class="flex gap-5">
                        <div class="flex-shrink-0 w-12 h-12 bg-white rounded-2xl shadow-sm border border-[#63102a]/5 flex items-center justify-center text-[#bc955c]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-[#201815]">Capa Gastronómica</h3>
                            <p class="text-sm text-[#4f0c22]/70 mt-1">Filtra los mejores tacos, cafeterías y paradas icónicas de la ciudad.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-7 relative">
                <div class="absolute -top-10 -right-10 w-64 h-64 bg-[#bc955c]/10 blur-[100px] rounded-full"></div>
                <div class="absolute -bottom-10 -left-10 w-64 h-64 bg-[#63102a]/10 blur-[100px] rounded-full"></div>

                <div class="relative bg-white p-3 rounded-[48px] shadow-[0_40px_80px_rgba(99,16,42,0.12)] border border-[#63102a]/5 overflow-hidden">

                    <div class="absolute top-8 left-8 right-8 z-20 flex items-center bg-white/90 backdrop-blur-md border border-[#63102a]/10 rounded-2xl px-5 py-3 shadow-lg">
                        <svg class="w-4 h-4 text-[#bc955c] mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span class="text-sm text-[#4f0c22]/40 font-medium">Buscar en Nezahualcóyotl...</span>
                    </div>

                    <div class="h-[420px] sm:h-[520px] lg:h-[680px] xl:h-[560px] rounded-[36px] overflow-hidden bg-[#f7ecd8]">
                        <img src="{{ $mapPreview }}" alt="Mapa ExploraNeza" class="w-full h-full object-contain">
                    </div>

                    <div class="mt-4 p-2 flex flex-wrap gap-2 justify-center">
                        <button class="flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-[#63102a] text-white text-xs font-bold shadow-lg shadow-[#63102a]/20 transition hover:scale-105">
                            <span class="w-2 h-2 bg-[#f2cf91] rounded-full animate-pulse"></span>
                            Servicios
                        </button>
                        <button class="flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white text-[#201815] text-xs font-bold border border-[#63102a]/10 shadow-sm transition hover:bg-[#fbf5eb]">
                            <svg class="w-4 h-4 text-[#bc955c]" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a1 1 0 00-2 0v7.268a2 2 0 000 3.464V16a1 1 0 102 0v-1.268a2 2 0 000-3.464V4zM11 4a1 1 0 10-2 0v1.268a2 2 0 000 3.464V16a1 1 0 102 0V8.732a2 2 0 000-3.464V4zM16 3a1 1 0 011 1v7.268a2 2 0 010 3.464V16a1 1 0 11-2 0v-1.268a2 2 0 010-3.464V4a1 1 0 011-1z"></path></svg>
                            Historia
                        </button>
                        <button class="flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white text-[#201815] text-xs font-bold border border-[#63102a]/10 shadow-sm transition hover:bg-[#fbf5eb]">
                            <svg class="w-4 h-4 text-[#235b4e]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                            Gastronomía
                        </button>
                    </div>
                </div>


            </div>

        </div>
    </div>
</section>


            <section class="bg-[#fbf5eb] py-24" x-data="{ os: 'android' }">
                <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

                    <div class="text-center mb-20">
                        <h2 class="text-4xl font-black text-[#201815] mb-6">Instala la App en 4 pasos</h2>

                        <div class="inline-flex p-1 bg-black/5 rounded-2xl border border-black/5">
                            <button @click="os = 'android'"
                                :class="os === 'android' ? 'bg-white shadow-md text-[#63102a]' : 'text-gray-500'"
                                class="px-6 py-2 rounded-xl text-sm font-bold transition-all">Android</button>
                            <button @click="os = 'ios'"
                                :class="os === 'ios' ? 'bg-white shadow-md text-[#63102a]' : 'text-gray-500'"
                                class="px-6 py-2 rounded-xl text-sm font-bold transition-all">iPhone</button>
                        </div>
                    </div>

                    <div class="relative">
                        <div
                            class="hidden lg:block absolute left-1/2 top-0 bottom-0 w-px border-l-2 border-dashed border-[#bc955c]/30 -translate-x-1/2">
                        </div>

                        <div class="relative grid lg:grid-cols-2 gap-12 items-center mb-32">
                            <div class="lg:text-right">
                                <span class="text-[#bc955c] font-black text-5xl opacity-20">01</span>
                                <h3 class="text-2xl font-black text-[#201815] mt-2">Escanea el código QR</h3>
                                <p class="mt-4 text-[#4f0c22]/70 text-lg leading-relaxed"
                                    x-text="os === 'android' ? 'Escanea el QR con tu cámara o lector desde Android para abrir directamente ExploraNeza en Chrome.' : 'Escanea el QR con la cámara de tu iPhone para abrir ExploraNeza directamente en Safari.'">
                                </p>
                            </div>
                            <div class="flex justify-center lg:justify-start">
                                <div
                                    class="relative w-full max-w-[280px] bg-white rounded-[2.5rem] p-3 shadow-2xl border-[6px] border-[#201815] overflow-hidden">
                                    <div
                                        class="aspect-[9/19] rounded-[1.8rem] overflow-hidden bg-[#fbf5eb] grid place-items-center">
                                        <div class="text-center px-6">
                                            <div
                                                class="mx-auto h-32 w-32 rounded-[28px] border-2 border-dashed border-[#bc955c] bg-white">
                                            </div>
                                            <p class="mt-5 text-sm font-bold text-[#63102a]">Espacio para colocar el QR
                                                de ExploraNeza</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="relative grid lg:grid-cols-2 gap-12 items-center mb-32">
                            <div class="lg:order-2">
                                <span class="text-[#bc955c] font-black text-5xl opacity-20">02</span>
                                <h3 class="text-2xl font-black text-[#201815] mt-2">Abre ExploraNeza</h3>
                                <p class="mt-4 text-[#4f0c22]/70 text-lg leading-relaxed"
                                    x-text="os === 'android' ? 'Una vez abierto el enlace, asegúrate de que ExploraNeza cargue dentro de Chrome para continuar con la instalación.' : 'Cuando se abra el enlace, verifica que ExploraNeza esté cargado en Safari antes de seguir al siguiente paso.'">
                                </p>
                            </div>
                            <div class="flex justify-center lg:justify-end lg:order-1">
                                <div
                                    class="relative w-full max-w-[280px] bg-white rounded-[2.5rem] p-3 shadow-2xl border-[6px] border-[#201815] overflow-hidden">
                                    <div class="aspect-[9/19] rounded-[1.8rem] overflow-hidden bg-gray-100">
                                        <img :src="os === 'android' ? '{{ $installStep2 }}' : '{{ $installStep2 }}'"
                                            class="w-full h-full object-contain">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="relative grid lg:grid-cols-2 gap-12 items-center mb-32">
                            <div class="lg:text-right">
                                <span class="text-[#bc955c] font-black text-5xl opacity-20">03</span>
                                <h3 class="text-2xl font-black text-[#201815] mt-2">Menú de Opciones</h3>
                                <p class="mt-4 text-[#4f0c22]/70 text-lg leading-relaxed"
                                    x-text="os === 'android' ? 'Toca los tres puntos verticales en la esquina superior derecha para ver las opciones del navegador.' : 'Toca el icono de compartir en Safari para abrir las acciones disponibles.'">
                                </p>
                            </div>
                            <div class="flex justify-center lg:justify-start">
                                <div
                                    class="relative w-full max-w-[280px] bg-white rounded-[2.5rem] p-3 shadow-2xl border-[6px] border-[#201815] overflow-hidden">
                                    <div class="aspect-[9/19] rounded-[1.8rem] overflow-hidden bg-gray-100">
                                        <img :src="os === 'android' ? '{{ $installStep3 }}' : '{{ $installStep3 }}'"
                                            class="w-full h-full object-contain">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="relative grid lg:grid-cols-2 gap-12 items-center">
                            <div class="lg:order-2">
                                <span class="text-[#bc955c] font-black text-5xl opacity-20">04</span>
                                <h3 class="text-2xl font-black text-[#201815] mt-2">Instala y confirma</h3>
                                <p class="mt-4 text-[#4f0c22]/70 text-lg leading-relaxed"
                                    x-text="os === 'android' ? 'Selecciona Instalar aplicación y confirma. El ícono de ExploraNeza aparecerá en tu pantalla principal.' : 'Elige Agregar a pantalla de inicio y confirma. El ícono de ExploraNeza quedará listo en tu inicio.'">
                                </p>
                            </div>
                            <div class="flex justify-center lg:justify-end lg:order-1">
                                <div
                                    class="relative w-full max-w-[280px] bg-white rounded-[2.5rem] p-3 shadow-2xl border-[6px] border-[#201815] overflow-hidden">
                                    <div class="aspect-[9/19] rounded-[1.8rem] overflow-hidden bg-gray-100">
                                        <img :src="os === 'android' ? '{{ $installStep4 }}' : '{{ $installStep4 }}'"
                                            class="w-full h-full object-contain">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="py-20">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div
                        class="shine-wrap overflow-hidden rounded-[36px] bg-[linear-gradient(135deg,#4f0c22,#63102a_55%,#7f173c)] px-6 py-10 text-white shadow-[0_28px_70px_rgba(99,16,42,0.18)] sm:px-10">
                        <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-center">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-[#f2cf91]">Empieza
                                    ahora</p>
                                <h2 class="mt-4 text-4xl font-black tracking-tight">Explora Nezahualcoyotl con una guia
                                    digital hecha para descubrir la ciudad.</h2>
                                <p class="mt-4 max-w-3xl text-base leading-8 text-white/78">Abre ExploraNeza y encuentra
                                    eventos, lugares, historia y experiencias listas para acompañar tu visita.</p>
                            </div>
                            <div class="flex flex-col gap-3 sm:flex-row lg:flex-col">
                                <a href="{{ $frontendUrl }}"
                                    class="inline-flex items-center justify-center rounded-full bg-white px-6 py-3.5 text-sm font-semibold text-[#63102a] transition hover:bg-[#fbf5eb]">
                                    Abrir ExploraNeza
                                </a>
                                <a href="#descubre"
                                    class="inline-flex items-center justify-center rounded-full border border-white/16 bg-white/10 px-6 py-3.5 text-sm font-semibold text-white transition hover:bg-white/16">
                                    Seguir explorando
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-[#63102a]/8 bg-[#ffffff]/82">
            <div
                class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-8 text-sm text-[#4f0c22]/75 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
                <div>
                    <p class="font-semibold text-[#201815]">ExploraNeza</p>
                    <p class="mt-1">Una forma simple de explorar eventos, historia, rutas y experiencias en
                        Nezahualcoyotl.</p>
                </div>
                <div class="flex flex-wrap gap-5">
                    <a href="{{ route('landing') }}" class="transition hover:text-[#63102a]">Inicio</a>
                    <a href="#experiencias" class="transition hover:text-[#63102a]">Experiencias</a>
                    <a href="#faq" class="transition hover:text-[#63102a]">FAQ</a>
                    <a href="{{ $frontendUrl }}" class="transition hover:text-[#63102a]">Abrir app</a>
                </div>
            </div>
        </footer>
    </div>

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

</body>

</html>
