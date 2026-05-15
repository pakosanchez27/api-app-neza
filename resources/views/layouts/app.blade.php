<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Neza Admin - @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body
    class="min-h-screen bg-[radial-gradient(circle_at_top_left,rgba(99,16,42,0.14),transparent_30%),radial-gradient(circle_at_top_right,rgba(188,149,92,0.18),transparent_28%),linear-gradient(180deg,#fffdfa_0%,#fbf5eb_100%)] font-sans text-[#201815]">
    @php
        $isDashboard = request()->routeIs('admin.dashboard');
        $isNoticias = request()->routeIs('admin.noticias*');
        $isEventos = request()->routeIs('admin.eventos*');
        $isHistoria = request()->routeIs('admin.historia*');
        $isTimeline = request()->routeIs('admin.timeline*');

        $navItemClasses = function (bool $isActive) {
            return $isActive
                ? 'flex items-center gap-2.5 rounded-[14px] bg-white px-3.5 py-2 text-[13px] font-medium text-[#63102a] shadow-[0_10px_22px_rgba(0,0,0,0.14)]'
                : 'flex items-center gap-2.5 rounded-[14px] px-3.5 py-2 text-[13px] font-medium text-white/88 transition hover:bg-white/12 hover:text-white';
        };

        $navDotClasses = function (bool $isActive) {
            return $isActive
                ? 'grid h-3.5 w-3.5 place-items-center rounded-full bg-current text-[8px] text-white'
                : 'grid h-3.5 w-3.5 place-items-center rounded-full bg-white/30 text-[8px] text-transparent';
        };
    @endphp

    <header class="bg-[#63102a] px-4 py-2 shadow-sm md:hidden">
        <div class="mx-auto flex max-w-6xl items-center justify-between">
            <button type="button" aria-label="Abrir menu" id="mobile-menu-open"
                class="flex h-10 w-10 flex-col items-center justify-center gap-1.5 rounded-full transition hover:bg-white/10">
                <span class="h-1 w-8 rounded-full bg-white"></span>
                <span class="h-1 w-8 rounded-full bg-white"></span>
                <span class="h-1 w-8 rounded-full bg-white"></span>
            </button>

            <a href="#" class="m-0 text-3xl font-bold text-white">ExploraNeza</a>

            <button type="button"
                class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-white/70 bg-[linear-gradient(135deg,#bc955c,#f2cf91)] text-sm font-semibold text-[#63102a]">
                NA
            </button>
        </div>
    </header>

    <div id="mobile-menu-overlay" class="fixed inset-0 z-40 hidden bg-[#190710]/55 backdrop-blur-[2px] md:hidden"></div>

    <aside id="mobile-menu-panel"
        class="fixed inset-y-0 left-0 z-50 flex w-[285px] max-w-[85vw] -translate-x-full flex-col bg-[linear-gradient(180deg,#63102a_0%,#7f173c_100%)] px-5 py-6 text-white shadow-[0_18px_45px_rgba(19,7,16,0.34)] transition-transform duration-300 md:hidden">
        <div class="flex items-center justify-between border-b border-white/15 pb-5">
            <a href="#" class="block">
                <p class="text-4xl font-bold text-white">ExploraNeza</p>
            </a>
            <button type="button" id="mobile-menu-close" aria-label="Cerrar menu"
                class="flex h-10 w-10 items-center justify-center rounded-full transition hover:bg-white/10">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <nav aria-label="Navegacion del panel movil" class="mt-4 space-y-0.5">
            <a href="{{ route('admin.dashboard') }}" class="{{ $navItemClasses($isDashboard) }}">
                <span class="{{ $navDotClasses($isDashboard) }}">•</span>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.noticias') }}" class="{{ $navItemClasses($isNoticias) }}">
                <span class="{{ $navDotClasses($isNoticias) }}">•</span>
                <span>Noticias</span>
            </a>
            <a href="{{ route('admin.eventos') }}" class="{{ $navItemClasses($isEventos) }}">
                <span class="{{ $navDotClasses($isEventos) }}">•</span>
                <span>Eventos</span>
            </a>
            <a href="{{ route('admin.historia') }}" class="{{ $navItemClasses($isHistoria) }}">
                <span class="{{ $navDotClasses($isHistoria) }}">•</span>
                <span>Historia de Neza</span>
            </a>
            <a href="{{ route('admin.timeline') }}" class="{{ $navItemClasses($isTimeline) }}">
                <span class="{{ $navDotClasses($isTimeline) }}">•</span>
                <span>Antes y Después</span>
            </a>
            {{-- <a href="#"
                class="flex items-center gap-2.5 rounded-[14px] px-3.5 py-2 text-[13px] font-medium text-white/88 transition hover:bg-white/12 hover:text-white">
                <span class="grid h-3.5 w-3.5 place-items-center rounded-full bg-white/30 text-[8px] text-transparent">•</span>
                <span>Configuracion</span>
            </a> --}}
        </nav>

        <div class="mt-auto pt-6">
            <div class="rounded-[18px] border border-white/12 bg-white/10 p-3 text-white backdrop-blur-sm">
                <p class="text-xs font-semibold text-white">Administrador</p>
                <p class="text-[11px] text-white/72">Panel administrativo</p>
                <div class="mt-3 overflow-hidden rounded-[16px] bg-white text-[#23171C] shadow-[0_16px_32px_rgba(35,23,28,0.16)]">
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit"
                            class="block w-full px-4 py-3 text-left text-[14px] font-medium text-[#7a2144] transition hover:bg-[#fff5f8]">
                            Cerrar sesion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <div class="w-full p-2 sm:p-3 md:p-4">
        <div
            class="grid min-h-[calc(100vh-16px)] grid-cols-1 gap-3 rounded-[22px] bg-white/85 p-2 shadow-[0_20px_60px_rgba(97,18,50,0.08)] backdrop-blur-[10px] sm:min-h-[calc(100vh-24px)] sm:gap-4 sm:rounded-[24px] sm:p-3 md:min-h-[calc(100vh-32px)] md:grid-cols-[224px_minmax(0,1fr)] md:rounded-[28px] md:p-4">
            <aside
                class="hidden h-full flex-col rounded-[24px] bg-[linear-gradient(180deg,#63102a_0%,#7f173c_100%)] px-5 py-6 text-white md:flex">
                <a href="#" class="block border-b border-white/15 pb-5">
                    <p class="text-4xl font-bold text-white">ExploraNeza</p>
                </a>

                <nav aria-label="Navegacion del panel" class="mt-4 space-y-0.5">
                    <a href="{{ route('admin.dashboard') }}" class="{{ $navItemClasses($isDashboard) }}">
                        <span class="{{ $navDotClasses($isDashboard) }}">•</span>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('admin.noticias') }}" class="{{ $navItemClasses($isNoticias) }}">
                        <span class="{{ $navDotClasses($isNoticias) }}">•</span>
                        <span>Noticias</span>
                    </a>
                    <a href="{{ route('admin.eventos') }}" class="{{ $navItemClasses($isEventos) }}">
                        <span class="{{ $navDotClasses($isEventos) }}">•</span>
                        <span>Eventos</span>
                    </a>
                    <a href="{{ route('admin.historia') }}" class="{{ $navItemClasses($isHistoria) }}">
                        <span class="{{ $navDotClasses($isHistoria) }}">•</span>
                        <span>Historia de Neza</span>
                    </a>
                    <a href="{{ route('admin.timeline') }}" class="{{ $navItemClasses($isTimeline) }}">
                        <span class="{{ $navDotClasses($isTimeline) }}">•</span>
                        <span>Antes y Después</span>
                    </a>

                    {{-- <a href="#"
                        class="flex items-center gap-2.5 rounded-[14px] px-3.5 py-2 text-[13px] font-medium text-white/88 transition hover:bg-white/12 hover:text-white">
                        <span
                            class="grid h-3.5 w-3.5 place-items-center rounded-full bg-white/30 text-[8px] text-transparent">•</span>
                        <span>Configuracion</span>
                    </a> --}}
                </nav>

                <div class="mt-auto pt-6">
                    <div class="rounded-[18px] border border-white/12 bg-white/10 p-3 text-white backdrop-blur-sm">
                        <div class="flex items-center gap-3">
                            <div>
                                <p class="text-xs font-semibold text-white">
                                    Administrador
                                </p>
                                <p class="text-[11px] text-white/72">
                                    Panel administrativo
                                </p>
                            </div>
                        </div>
                        <div
                            class="mt-3 overflow-hidden rounded-[16px] bg-white text-[#23171C] shadow-[0_16px_32px_rgba(35,23,28,0.16)]">
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit"
                                    class="block w-full px-4 py-3 text-left text-[14px] font-medium text-[#7a2144] transition hover:bg-[#fff5f8]">
                                    Cerrar sesion
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </aside>

            <main class="min-w-0 pb-3 sm:pb-4">


                <div class="mt-4 sm:mt-5 md:mt-6">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const openButton = document.getElementById('mobile-menu-open');
            const closeButton = document.getElementById('mobile-menu-close');
            const overlay = document.getElementById('mobile-menu-overlay');
            const panel = document.getElementById('mobile-menu-panel');

            if (!openButton || !closeButton || !overlay || !panel) {
                return;
            }

            const openMenu = () => {
                overlay.classList.remove('hidden');
                panel.classList.remove('-translate-x-full');
                document.body.classList.add('overflow-hidden');
            };

            const closeMenu = () => {
                overlay.classList.add('hidden');
                panel.classList.add('-translate-x-full');
                document.body.classList.remove('overflow-hidden');
            };

            openButton.addEventListener('click', openMenu);
            closeButton.addEventListener('click', closeMenu);
            overlay.addEventListener('click', closeMenu);

            panel.querySelectorAll('a').forEach((link) => {
                link.addEventListener('click', closeMenu);
            });
        });
    </script>
</body>

</html>
