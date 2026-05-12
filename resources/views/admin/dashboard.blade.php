@extends('layouts.app')

@section('title', 'Dashboard')
@section('title-section', 'Panel de Control')
@section('description', 'Sigue crecimiento, visibilidad comercial y avance del pasaporte desde un tablero más claro y útil.')

@section('content')
    <div class="space-y-6">
        <section class="overflow-hidden rounded-[30px] bg-[radial-gradient(circle_at_top_left,#c84d79_0%,#8d2048_38%,#63102a_70%,#3e0b1b_100%)] px-6 py-7 text-white shadow-[0_28px_70px_rgba(99,16,42,0.28)]">
            <div class="grid gap-6 xl:grid-cols-[1.4fr,0.9fr]">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.26em] text-white/65">Visión general</p>
                    <div class="mt-6 grid gap-4 md:grid-cols-3">
                        <div class="rounded-[22px] border border-white/12 bg-white/10 px-4 py-4 backdrop-blur-sm">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-white/65">Usuarios totales</p>
                            <p class="mt-2 text-3xl font-bold">{{ number_format($totalUsuarios) }}</p>
                            <p class="mt-2 text-xs leading-5 text-white/70">Total acumulado de cuentas registradas en la plataforma.</p>
                        </div>
                        <div class="rounded-[22px] border border-white/12 bg-white/10 px-4 py-4 backdrop-blur-sm">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-white/65">Comercios totales</p>
                            <p class="mt-2 text-3xl font-bold">{{ number_format($totalComercios) }}</p>
                            <p class="mt-2 text-xs leading-5 text-white/70">Cantidad de establecimientos dados de alta en el sistema.</p>
                        </div>
                        <div class="rounded-[22px] border border-white/12 bg-white/10 px-4 py-4 backdrop-blur-sm">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-white/65">Sellos emitidos</p>
                            <p class="mt-2 text-3xl font-bold">{{ number_format($totalSellos) }}</p>
                            <p class="mt-2 text-xs leading-5 text-white/70">Número total de sellos registrados en los pasaportes.</p>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-1">
                    <div class="rounded-[24px] border border-white/12 bg-white/10 p-5 backdrop-blur-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/65">Usuarios nuevos</p>
                        <div class="mt-4 flex items-end justify-between gap-4">
                            <div>
                                <p class="text-[13px] text-white/70">Esta semana</p>
                                <p class="mt-1 text-4xl font-bold">{{ number_format($usuariosNuevosSemana) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[13px] text-white/70">Este mes</p>
                                <p class="mt-1 text-3xl font-bold">{{ number_format($usuariosNuevosMes) }}</p>
                            </div>
                        </div>
                        <p class="mt-3 text-xs leading-5 text-white/70">Compara la captación reciente de usuarios en ventana semanal y mensual.</p>
                    </div>

                    <div class="rounded-[24px] border border-white/12 bg-white/10 p-5 backdrop-blur-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/65">Pasaporte</p>
                        <div class="mt-4 flex items-end justify-between gap-4">
                            <div>
                                <p class="text-[13px] text-white/70">Creados</p>
                                <p class="mt-1 text-4xl font-bold">{{ number_format($totalPasaportes) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[13px] text-white/70">Completados</p>
                                <p class="mt-1 text-3xl font-bold">{{ number_format($pasaportesCompletados) }}</p>
                            </div>
                        </div>
                        <p class="mt-3 text-xs leading-5 text-white/70">Mide cuántos pasaportes se han iniciado y cuántos ya cerraron ciclo.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-4 xl:grid-cols-[1.2fr,1fr,1fr]">
            <article class="rounded-[26px] border border-[#efe6dd] bg-white p-6 shadow-[0_18px_42px_rgba(32,24,21,0.08)]">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#8b6f79]">Captación</p>
                        <h3 class="mt-2 text-xl font-semibold text-[#201815]">Usuarios nuevos</h3>
                    </div>
                    <div class="rounded-full bg-[#fff3f7] px-3 py-1 text-xs font-semibold text-[#8d2048]">
                        Tendencia reciente
                    </div>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-[22px] bg-[#fff7fa] p-5">
                        <p class="text-sm font-medium text-[#7c5d69]">Últimos 7 días</p>
                        <p class="mt-2 text-4xl font-bold text-[#63102a]">{{ number_format($usuariosNuevosSemana) }}</p>
                        <p class="mt-2 text-xs leading-5 text-[#7c5d69]">Altas creadas desde el inicio de la semana actual.</p>
                    </div>
                    <div class="rounded-[22px] bg-[#f8f8ff] p-5">
                        <p class="text-sm font-medium text-[#6e6c8f]">Mes actual</p>
                        <p class="mt-2 text-4xl font-bold text-[#29338b]">{{ number_format($usuariosNuevosMes) }}</p>
                        <p class="mt-2 text-xs leading-5 text-[#6e6c8f]">Altas registradas desde el primer día del mes.</p>
                    </div>
                </div>
            </article>

            <article class="rounded-[26px] border border-[#efe6dd] bg-white p-6 shadow-[0_18px_42px_rgba(32,24,21,0.08)]">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#8b6f79]">Padrón comercial</p>
                        <h3 class="mt-2 text-xl font-semibold text-[#201815]">Estado de comercios</h3>
                    </div>
                </div>

                <div class="mt-6 space-y-4">
                    <div class="rounded-[22px] bg-[#f2fbf4] p-5">
                        <p class="text-sm font-medium text-[#4c7b5d]">Visibles al público</p>
                        <p class="mt-2 text-4xl font-bold text-[#166534]">{{ number_format($comerciosVisibles) }}</p>
                        <p class="mt-2 text-xs leading-5 text-[#4c7b5d]">Comercios activos y habilitados para mostrarse en la app.</p>
                    </div>
                    <div class="rounded-[22px] bg-[#fff7ed] p-5">
                        <p class="text-sm font-medium text-[#8a5a2d]">Aún incompletos</p>
                        <p class="mt-2 text-4xl font-bold text-[#c2410c]">{{ number_format($comerciosIncompletos) }}</p>
                        <p class="mt-2 text-xs leading-5 text-[#8a5a2d]">Registros de comercio que todavía no terminan su proceso de alta.</p>
                    </div>
                </div>
            </article>

            <article class="rounded-[26px] border border-[#efe6dd] bg-white p-6 shadow-[0_18px_42px_rgba(32,24,21,0.08)]">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#8b6f79]">Actividad</p>
                        <h3 class="mt-2 text-xl font-semibold text-[#201815]">Pasaporte y rutas</h3>
                    </div>
                </div>

                <div class="mt-6 space-y-4">
                    <div class="rounded-[22px] bg-[#faf5ff] p-5">
                        <p class="text-sm font-medium text-[#74528a]">Rutas activas</p>
                        <p class="mt-2 text-4xl font-bold text-[#6b21a8]">{{ number_format($totalRutasActivas) }}</p>
                        <p class="mt-2 text-xs leading-5 text-[#74528a]">Rutas disponibles actualmente para experiencia y pasaporte.</p>
                    </div>
                    <div class="rounded-[22px] bg-[#f0fdfa] p-5">
                        <p class="text-sm font-medium text-[#3f7770]">Sellos emitidos</p>
                        <p class="mt-2 text-4xl font-bold text-[#0f766e]">{{ number_format($totalSellos) }}</p>
                        <p class="mt-2 text-xs leading-5 text-[#3f7770]">Interacciones efectivas de usuarios dentro del sistema de pasaporte.</p>
                    </div>
                </div>
            </article>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.3fr,1fr]">
            <article class="rounded-[28px] border border-[#efe6dd] bg-white p-6 shadow-[0_18px_42px_rgba(32,24,21,0.08)]">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-[#201815]">Top 10 usuarios con más avance en pasaporte</h3>
                        <p class="mt-1 text-sm text-[#6d5a62]">
                            Ordenados por porcentaje de progreso y luego por sellos acumulados.
                        </p>
                    </div>
                </div>

                <div class="mt-5 overflow-x-auto">
                    <table class="min-w-full divide-y divide-[#efe6dd] text-sm">
                        <thead>
                            <tr class="text-left text-[#7d6870]">
                                <th class="py-3 pr-4 font-semibold">Usuario</th>
                                <th class="py-3 pr-4 font-semibold">Progreso</th>
                                <th class="py-3 pr-4 font-semibold">Sellos</th>
                                <th class="py-3 pr-4 font-semibold">Pasaportes</th>
                                <th class="py-3 font-semibold">Completados</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#f3ebe4]">
                            @forelse ($topUsuariosPasaporte as $usuario)
                                <tr class="align-top">
                                    <td class="py-4 pr-4">
                                        <p class="font-semibold text-[#201815]">{{ $usuario['nombre'] }}</p>
                                        <p class="mt-1 text-xs text-[#7d6870]">{{ $usuario['email'] ?: 'Sin correo registrado' }}</p>
                                    </td>
                                    <td class="py-4 pr-4">
                                        <span class="inline-flex rounded-full bg-[#fff5f8] px-3 py-1 text-xs font-semibold text-[#611232]">
                                            {{ number_format($usuario['progreso'], 1) }}%
                                        </span>
                                    </td>
                                    <td class="py-4 pr-4 text-[#201815]">
                                        {{ number_format($usuario['sellos']) }} / {{ number_format($usuario['sellos_posibles']) }}
                                    </td>
                                    <td class="py-4 pr-4 text-[#201815]">{{ number_format($usuario['pasaportes']) }}</td>
                                    <td class="py-4 text-[#201815]">{{ number_format($usuario['pasaportes_completados']) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-sm text-[#7d6870]">
                                        Aún no hay suficiente actividad para construir este ranking.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>

            <article class="rounded-[28px] border border-[#efe6dd] bg-white p-6 shadow-[0_18px_42px_rgba(32,24,21,0.08)]">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <h3 class="text-xl font-semibold text-[#201815]">Top comercios que más sellos generan</h3>
                        <p class="mt-1 text-sm text-[#6d5a62]">
                            Comercios con mayor tracción dentro del flujo del pasaporte.
                        </p>
                    </div>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse ($topComerciosPasaporte as $comercio)
                        <div class="rounded-[22px] border border-[#f0e6de] bg-[#fffdfa] px-4 py-4">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="font-semibold text-[#201815]">{{ $comercio['nombre'] }}</p>
                                    <p class="mt-1 text-xs text-[#7d6870]">{{ $comercio['tipo'] }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-bold text-[#63102a]">{{ number_format($comercio['sellos']) }}</p>
                                    <p class="text-[11px] uppercase tracking-[0.16em] text-[#8b6f79]">sellos</p>
                                </div>
                            </div>
                            <div class="mt-3 flex gap-2 text-xs">
                                <span class="inline-flex rounded-full px-2.5 py-1 font-semibold {{ $comercio['visible'] ? 'bg-[#f2fbf4] text-[#166534]' : 'bg-[#fff1f2] text-[#be123c]' }}">
                                    {{ $comercio['visible'] ? 'Visible' : 'Oculto' }}
                                </span>
                                <span class="inline-flex rounded-full px-2.5 py-1 font-semibold {{ $comercio['activo'] ? 'bg-[#eff6ff] text-[#1d4ed8]' : 'bg-[#fff7ed] text-[#c2410c]' }}">
                                    {{ $comercio['activo'] ? 'Activo' : 'Incompleto' }}
                                </span>
                            </div>
                            <p class="mt-3 text-xs leading-5 text-[#7d6870]">Este ranking muestra qué comercios generan más sellos dentro del pasaporte.</p>
                        </div>
                    @empty
                        <div class="rounded-[22px] border border-[#f0e6de] bg-[#fffdfa] px-4 py-8 text-center text-sm text-[#7d6870]">
                            Aún no hay sellos suficientes para mostrar un top de comercios.
                        </div>
                    @endforelse
                </div>
            </article>
        </section>
    </div>
@endsection
