@extends('layouts.app')

@section('title', 'Dashboard')
@section('title-section', 'Panel de Control')
@section('description', 'Sigue crecimiento, visibilidad comercial y avance del pasaporte desde un tablero claro y util.')

@section('content')
    @php
        $completionRate = $totalPasaportes > 0 ? round(($pasaportesCompletados / $totalPasaportes) * 100) : 0;
        $visibleCommerceRate = $totalComercios > 0 ? round(($comerciosVisibles / $totalComercios) * 100) : 0;
        $passportActivityRate = $totalPasaportes > 0 ? round(min(100, ($totalSellos / max(1, $totalPasaportes)) * 18)) : 0;
        $chartBars = [
            ['label' => 'U', 'value' => max(18, min(100, $usuariosNuevosSemana * 12)), 'solid' => false],
            ['label' => 'C', 'value' => max(28, min(100, $comerciosVisibles * 5)), 'solid' => true],
            ['label' => 'S', 'value' => max(34, min(100, $totalSellos * 4)), 'solid' => true, 'light' => true],
            ['label' => 'P', 'value' => max(30, min(100, $totalPasaportes * 8)), 'solid' => true, 'dark' => true],
            ['label' => 'R', 'value' => max(22, min(100, $totalRutasActivas * 22)), 'solid' => false],
            ['label' => 'M', 'value' => max(20, min(100, $usuariosNuevosMes * 5)), 'solid' => false],
            ['label' => 'A', 'value' => max(26, min(100, $passportActivityRate)), 'solid' => false],
        ];
    @endphp

    <div class="space-y-4">
        <section class="flex flex-col gap-4 px-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-semibold tracking-normal text-[#201815] sm:text-4xl">Dashboard</h1>
                <p class="mt-2 text-sm text-[#8b6f79]">Monitorea usuarios, comercios y actividad del pasaporte.</p>
            </div>

        </section>

        <section class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-[22px] bg-[linear-gradient(145deg,#63102a_0%,#0f6b43_100%)] p-5 text-white shadow-[0_18px_42px_rgba(99,16,42,0.22)]">
                <div class="flex items-start justify-between">
                    <p class="text-sm font-semibold">Usuarios</p>
                </div>
                <p class="mt-4 text-4xl font-semibold leading-none">{{ number_format($totalUsuarios) }}</p>
                <p class="mt-3 inline-flex items-center rounded-full bg-[#f2cf91]/18 px-2 py-1 text-[11px] font-medium text-[#f8e7bd]">
                    {{ number_format($usuariosNuevosSemana) }} nuevos esta semana
                </p>
                <p class="mt-3 text-xs leading-5 text-white/72">Lectura: si este numero sube pero los sellos no, hay registro sin participacion en pasaporte.</p>
            </article>

            <article class="rounded-[22px] border border-[#f0e6de] bg-white p-5 shadow-[0_16px_34px_rgba(32,24,21,0.07)]">
                <div class="flex items-start justify-between">
                    <p class="text-sm font-semibold text-[#201815]">Comercios</p>
                </div>
                <p class="mt-4 text-4xl font-semibold leading-none text-[#201815]">{{ number_format($totalComercios) }}</p>
                <p class="mt-3 text-xs text-[#6d5a62]">{{ number_format($comerciosVisibles) }} visibles al publico</p>
                <p class="mt-3 text-xs leading-5 text-[#8b6f79]">Lectura: compara total contra visibles para saber cuantos comercios ya aparecen en la app.</p>
            </article>

            <article class="rounded-[22px] border border-[#f0e6de] bg-white p-5 shadow-[0_16px_34px_rgba(32,24,21,0.07)]">
                <div class="flex items-start justify-between">
                    <p class="text-sm font-semibold text-[#201815]">Sellos</p>
                </div>
                <p class="mt-4 text-4xl font-semibold leading-none text-[#201815]">{{ number_format($totalSellos) }}</p>
                <p class="mt-3 text-xs text-[#6d5a62]">Interacciones del pasaporte</p>
                <p class="mt-3 text-xs leading-5 text-[#8b6f79]">Lectura: cada sello representa una visita validada; es el mejor indicador de uso real de rutas.</p>
            </article>

            <article class="rounded-[22px] border border-[#f0e6de] bg-white p-5 shadow-[0_16px_34px_rgba(32,24,21,0.07)]">
                <div class="flex items-start justify-between">
                    <p class="text-sm font-semibold text-[#201815]">Pasaportes</p>
                </div>
                <p class="mt-4 text-4xl font-semibold leading-none text-[#201815]">{{ number_format($totalPasaportes) }}</p>
                <p class="mt-3 text-xs text-[#6d5a62]">{{ number_format($pasaportesCompletados) }} completados</p>
                <p class="mt-3 text-xs leading-5 text-[#8b6f79]">Lectura: mide cuantas personas iniciaron una ruta y cuantas llegaron al final.</p>
            </article>
        </section>

        <section class="grid gap-3 xl:grid-cols-[1.1fr_0.8fr_0.9fr]">
            <article class="rounded-[22px] border border-[#f0e6de] bg-white p-5 shadow-[0_16px_34px_rgba(32,24,21,0.07)]">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-base font-semibold text-[#201815]">Actividad general</h2>
                    <span class="rounded-full border border-[#63102a]/25 px-3 py-1 text-xs font-semibold text-[#63102a]">Pasaporte</span>
                </div>
                <p class="mt-2 text-xs leading-5 text-[#8b6f79]">
                    Las barras comparan senales clave del sistema: U usuarios nuevos, C comercios visibles, S sellos, P pasaportes, R rutas, M altas del mes y A actividad estimada.
                </p>

                <div class="mt-5 flex h-36 items-end gap-4">
                    @foreach ($chartBars as $bar)
                        <div class="flex flex-1 flex-col items-center gap-2">
                            <div class="flex h-28 w-full items-end">
                                <div
                                    class="w-full rounded-full {{ $bar['solid'] ?? false ? (($bar['dark'] ?? false) ? 'bg-[#63102a]' : (($bar['light'] ?? false) ? 'bg-[#5fbf96]' : 'bg-[#1f8f61]')) : '' }}"
                                    style="height: {{ $bar['value'] }}%; {{ $bar['solid'] ?? false ? '' : 'background: repeating-linear-gradient(135deg, rgba(99,16,42,0.55) 0 3px, transparent 3px 7px);' }}">
                                </div>
                            </div>
                            <span class="text-xs font-medium text-[#9a8a82]">{{ $bar['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="rounded-[22px] border border-[#f0e6de] bg-white p-5 shadow-[0_16px_34px_rgba(32,24,21,0.07)]">
                <h2 class="text-base font-semibold text-[#201815]">Estado comercial</h2>
                <p class="mt-2 text-xs leading-5 text-[#8b6f79]">Un porcentaje alto de visibles significa que el catalogo esta listo para consulta publica; los incompletos requieren seguimiento.</p>
                <div class="mt-5 space-y-4">
                    <div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-medium text-[#6d5a62]">Visibles</span>
                            <span class="font-semibold text-[#63102a]">{{ $visibleCommerceRate }}%</span>
                        </div>
                        <div class="mt-2 h-3 rounded-full bg-[#f3ebe4]">
                            <div class="h-3 rounded-full bg-[#63102a]" style="width: {{ $visibleCommerceRate }}%"></div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-[18px] bg-[#fff7ed] p-4">
                            <p class="text-3xl font-semibold text-[#bc955c]">{{ number_format($comerciosIncompletos) }}</p>
                            <p class="mt-1 text-xs text-[#7d6870]">Incompletos</p>
                        </div>
                        <div class="rounded-[18px] bg-[#fff5f8] p-4">
                            <p class="text-3xl font-semibold text-[#63102a]">{{ number_format($totalRutasActivas) }}</p>
                            <p class="mt-1 text-xs text-[#7d6870]">Rutas activas</p>
                        </div>
                    </div>
                </div>
            </article>

            <article class="overflow-hidden rounded-[22px] bg-[#63102a] p-5 text-white shadow-[0_18px_42px_rgba(99,16,42,0.22)]">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-semibold">Progreso pasaporte</h2>
                    <span class="rounded-full bg-white/12 px-3 py-1 text-xs font-semibold text-[#f2cf91]">Global</span>
                </div>
                <p class="mt-2 text-xs leading-5 text-white/68">El porcentaje muestra pasaportes completados contra pasaportes iniciados. Si baja, hay personas empezando rutas pero sin terminarlas.</p>
                <div class="mt-6 flex items-center justify-center">
                    <div class="grid h-40 w-40 place-items-center rounded-full" style="background: conic-gradient(#f2cf91 {{ $completionRate }}%, rgba(255,255,255,0.18) 0);">
                        <div class="grid h-28 w-28 place-items-center rounded-full bg-[#63102a]">
                            <div class="text-center">
                                <p class="text-4xl font-semibold leading-none">{{ $completionRate }}%</p>
                                <p class="mt-1 text-xs text-white/68">Completados</p>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        </section>

        <section class="grid gap-3 xl:grid-cols-[1.3fr_0.9fr]">
            <article class="rounded-[22px] border border-[#f0e6de] bg-white p-5 shadow-[0_16px_34px_rgba(32,24,21,0.07)]">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-[#201815]">Top 10 usuarios con mas avance en pasaporte</h2>
                        <p class="mt-1 text-sm text-[#8b6f79]">Solo usuarios con al menos un sello, ordenados por sellos acumulados.</p>
                        <p class="mt-1 text-xs leading-5 text-[#9a8a82]">Usalo para identificar a quienes mas participan; el porcentaje indica que tan cerca estan de completar sus sellos posibles.</p>
                    </div>
                </div>

                <div class="mt-4 space-y-3">
                    @forelse ($topUsuariosPasaporte as $usuario)
                        @php $progressWidth = min(100, max(0, $usuario['progreso'])); @endphp
                        <div class="rounded-[18px] border border-[#f2e8df] bg-[#fffdfa] p-4">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-[#201815]">{{ $usuario['nombre'] }}</p>
                                    <p class="mt-1 truncate text-xs text-[#8b6f79]">{{ $usuario['email'] ?: 'Sin correo registrado' }}</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="rounded-full bg-[#fff5f8] px-3 py-1 text-xs font-semibold text-[#63102a]">{{ number_format($usuario['sellos']) }} sellos</span>
                                    <span class="text-sm font-semibold text-[#201815]">{{ number_format($usuario['progreso'], 1) }}%</span>
                                </div>
                            </div>
                            <div class="mt-3 h-2 rounded-full bg-[#f3ebe4]">
                                <div class="h-2 rounded-full bg-[#63102a]" style="width: {{ $progressWidth }}%"></div>
                            </div>
                            <div class="mt-2 flex justify-between text-xs text-[#8b6f79]">
                                <span>{{ number_format($usuario['sellos']) }} / {{ number_format($usuario['sellos_posibles']) }} sellos posibles</span>
                                <span>{{ number_format($usuario['pasaportes_completados']) }} completados</span>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-[18px] border border-[#f2e8df] bg-[#fffdfa] px-4 py-8 text-center text-sm text-[#8b6f79]">
                            Aun no hay suficiente actividad para construir este ranking.
                        </div>
                    @endforelse
                </div>
            </article>

            <article class="rounded-[22px] border border-[#f0e6de] bg-white p-5 shadow-[0_16px_34px_rgba(32,24,21,0.07)]">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-semibold text-[#201815]">Comercios con mas sellos</h2>
                    <span class="rounded-full border border-[#63102a]/25 px-3 py-1 text-xs font-semibold text-[#63102a]">Top</span>
                </div>
                <p class="mt-2 text-xs leading-5 text-[#8b6f79]">Este listado muestra donde se estan generando mas validaciones. Ayuda a ubicar comercios con mejor traccion en las rutas.</p>

                <div class="mt-4 space-y-3">
                    @forelse ($topComerciosPasaporte as $comercio)
                        <div class="flex items-center gap-3 rounded-[18px] bg-[#fffdfa] p-3">
                            <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-[#f2cf91]/35 text-sm font-semibold text-[#63102a]">
                                {{ substr($comercio['nombre'], 0, 1) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-[#201815]">{{ $comercio['nombre'] }}</p>
                                <p class="truncate text-xs text-[#8b6f79]">{{ $comercio['tipo'] }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-semibold text-[#63102a]">{{ number_format($comercio['sellos']) }}</p>
                                <p class="text-[11px] text-[#8b6f79]">sellos</p>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-[18px] border border-[#f2e8df] bg-[#fffdfa] px-4 py-8 text-center text-sm text-[#8b6f79]">
                            Aun no hay sellos suficientes para mostrar un top de comercios.
                        </div>
                    @endforelse
                </div>
            </article>
        </section>

        <section class="grid gap-3 xl:grid-cols-[0.9fr_1.1fr]">
            <article class="rounded-[22px] border border-[#f0e6de] bg-white p-5 shadow-[0_16px_34px_rgba(32,24,21,0.07)]">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-base font-semibold text-[#201815]">Agente de metricas</h2>
                        <p class="mt-1 text-xs leading-5 text-[#8b6f79]">
                            Analiza las estadisticas actuales del dashboard y traduce los numeros en lectura operativa: que va bien, que podria requerir seguimiento y que acciones conviene priorizar.
                        </p>
                    </div>
                    <span class="rounded-full bg-[#fff5f8] px-3 py-1 text-xs font-semibold text-[#63102a]">IA</span>
                </div>

                <form id="metrics-agent-form" class="mt-4 space-y-3">
                    @csrf
                    <textarea
                        id="metrics-agent-question"
                        name="question"
                        rows="3"
                        maxlength="1000"
                        class="w-full resize-none rounded-[18px] border border-[#eadfd6] bg-[#fffdfa] px-4 py-3 text-sm text-[#201815] outline-none transition placeholder:text-[#b0a19a] focus:border-[#63102a]/55 focus:ring-4 focus:ring-[#63102a]/8"
                        placeholder="Ej. Que me dicen los sellos y pasaportes de la actividad actual?"
                    ></textarea>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" data-agent-question="Que lectura general tienen las metricas de hoy?" class="metrics-agent-prompt rounded-full border border-[#63102a]/25 px-3 py-1.5 text-xs font-semibold text-[#63102a] transition hover:bg-[#fff5f8]">
                            Lectura general
                        </button>
                        <button type="button" data-agent-question="Que debo revisar en pasaporte y sellos?" class="metrics-agent-prompt rounded-full border border-[#63102a]/25 px-3 py-1.5 text-xs font-semibold text-[#63102a] transition hover:bg-[#fff5f8]">
                            Pasaporte
                        </button>
                        <button type="button" data-agent-question="Como van los comercios visibles e incompletos?" class="metrics-agent-prompt rounded-full border border-[#63102a]/25 px-3 py-1.5 text-xs font-semibold text-[#63102a] transition hover:bg-[#fff5f8]">
                            Comercios
                        </button>
                    </div>
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-[#63102a] px-4 py-3 text-sm font-semibold text-white shadow-[0_14px_28px_rgba(99,16,42,0.18)] transition hover:bg-[#4f0c22] sm:w-auto">
                        Analizar metricas
                    </button>
                </form>
            </article>

            <article class="rounded-[22px] border border-[#f0e6de] bg-[#fffdfa] p-5 shadow-[0_16px_34px_rgba(32,24,21,0.07)]">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-base font-semibold text-[#201815]">Respuesta del agente</h2>
                    <span id="metrics-agent-engine" class="hidden rounded-full bg-white px-3 py-1 text-xs font-semibold text-[#8b6f79]"></span>
                </div>

                <div id="metrics-agent-empty" class="mt-5 rounded-[18px] border border-dashed border-[#d8c7bd] bg-white px-4 py-8 text-center text-sm leading-6 text-[#8b6f79]">
                    Escribe una pregunta o usa una sugerencia para recibir una lectura operativa de las estadisticas.
                </div>

                <div id="metrics-agent-result" class="mt-4 hidden space-y-4">
                    <p id="metrics-agent-text" class="text-sm leading-6 text-[#201815]"></p>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#bc955c]">Datos clave</p>
                        <ul id="metrics-agent-highlights" class="mt-2 space-y-2 text-sm text-[#6d5a62]"></ul>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#bc955c]">Recomendaciones</p>
                        <ul id="metrics-agent-recommendations" class="mt-2 space-y-2 text-sm text-[#6d5a62]"></ul>
                    </div>
                </div>
            </article>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('metrics-agent-form');
            const question = document.getElementById('metrics-agent-question');
            const emptyState = document.getElementById('metrics-agent-empty');
            const result = document.getElementById('metrics-agent-result');
            const text = document.getElementById('metrics-agent-text');
            const highlights = document.getElementById('metrics-agent-highlights');
            const recommendations = document.getElementById('metrics-agent-recommendations');
            const engine = document.getElementById('metrics-agent-engine');
            const submit = form?.querySelector('button[type="submit"]');

            if (!form || !question || !submit) {
                return;
            }

            const renderList = (target, items) => {
                target.innerHTML = '';
                (items || []).forEach((item) => {
                    const li = document.createElement('li');
                    li.className = 'rounded-[14px] bg-white px-3 py-2';
                    li.textContent = item;
                    target.appendChild(li);
                });
            };

            document.querySelectorAll('.metrics-agent-prompt').forEach((button) => {
                button.addEventListener('click', () => {
                    question.value = button.dataset.agentQuestion || '';
                    question.focus();
                });
            });

            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                const value = question.value.trim();

                if (!value) {
                    question.focus();
                    return;
                }

                submit.disabled = true;
                submit.textContent = 'Analizando...';
                emptyState.classList.add('hidden');
                result.classList.remove('hidden');
                text.textContent = 'Revisando las metricas del dashboard...';
                highlights.innerHTML = '';
                recommendations.innerHTML = '';
                engine.classList.add('hidden');

                try {
                    const response = await fetch(@json(route('admin.metricas.agente')), {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                        },
                        body: JSON.stringify({ question: value }),
                    });

                    const payload = await response.json();

                    if (!response.ok) {
                        throw new Error(payload.message || 'No fue posible consultar el agente.');
                    }

                    text.textContent = payload.text || 'No se genero una lectura para esta pregunta.';
                    renderList(highlights, payload.highlights || []);
                    renderList(recommendations, payload.recommendations || []);

                    if (payload.engine) {
                        engine.textContent = payload.engine === 'openai' ? 'OpenAI' : 'Reglas';
                        engine.classList.remove('hidden');
                    }
                } catch (error) {
                    text.textContent = error.message || 'No fue posible consultar el agente.';
                    renderList(highlights, []);
                    renderList(recommendations, ['Intenta de nuevo o revisa la configuracion del servicio de IA.']);
                } finally {
                    submit.disabled = false;
                    submit.textContent = 'Analizar metricas';
                }
            });
        });
    </script>
@endpush
