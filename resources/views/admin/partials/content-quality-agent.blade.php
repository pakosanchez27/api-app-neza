@php
    $agentId = 'content-quality-agent-' . substr(md5(($contentType ?? 'contenido') . ($titleField ?? '') . ($bodyField ?? '')), 0, 8);
    $subtitleField = $subtitleField ?? null;
@endphp

<div
    id="{{ $agentId }}"
    class="content-quality-agent md:col-span-2 rounded-[22px] border border-[#f0e6de] bg-[#fffdfa] p-5 shadow-[0_12px_28px_rgba(32,24,21,0.05)]"
    data-content-type="{{ $contentType ?? 'contenido' }}"
    data-title-field="{{ $titleField ?? 'titulo' }}"
    data-subtitle-field="{{ $subtitleField }}"
    data-body-field="{{ $bodyField ?? 'resumen' }}"
    data-endpoint="{{ route('admin.contenido.agente-calidad') }}"
>
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <div class="flex items-center gap-2">
                <h3 class="text-base font-semibold text-[#201815]">Agente de calidad de contenido</h3>
                <span class="rounded-full bg-[#fff5f8] px-3 py-1 text-xs font-semibold text-[#63102a]">IA</span>
            </div>
            <p class="mt-1 text-xs leading-5 text-[#8b6f79]">
                Revisa claridad, ortografia, tono, estructura y datos faltantes antes de publicar. Puede sugerir una version mejorada sin guardar cambios automaticamente.
            </p>
        </div>

        <button type="button" data-quality-run
            class="inline-flex items-center justify-center rounded-full bg-[#63102a] px-4 py-2.5 text-sm font-semibold text-white shadow-[0_10px_22px_rgba(99,16,42,0.18)] transition hover:bg-[#4f0c22]">
            Revisar contenido
        </button>
    </div>

    <div data-quality-empty class="mt-4 rounded-[18px] border border-dashed border-[#d8c7bd] bg-white px-4 py-5 text-sm leading-6 text-[#8b6f79]">
        Completa el titulo y el texto principal, luego ejecuta la revision para recibir recomendaciones editoriales.
    </div>

    <div data-quality-result class="mt-4 hidden space-y-4">
        <div class="rounded-[18px] bg-white p-4">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <p data-quality-summary class="text-sm leading-6 text-[#201815]"></p>
                <span data-quality-score class="shrink-0 rounded-full bg-[#fff5f8] px-3 py-1 text-xs font-semibold text-[#63102a]"></span>
            </div>
        </div>

        <div class="grid gap-3 lg:grid-cols-2">
            <div class="rounded-[18px] bg-white p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#bc955c]">Checklist</p>
                <ul data-quality-checks class="mt-2 space-y-2 text-sm text-[#6d5a62]"></ul>
            </div>

            <div class="rounded-[18px] bg-white p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#bc955c]">Sugerencias</p>
                <ul data-quality-suggestions class="mt-2 space-y-2 text-sm text-[#6d5a62]"></ul>
            </div>
        </div>

        <div class="rounded-[18px] bg-white p-4">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#bc955c]">Version sugerida</p>
                <div class="flex flex-wrap gap-2">
                    <button type="button" data-quality-apply="title" class="rounded-full border border-[#63102a]/25 px-3 py-1.5 text-xs font-semibold text-[#63102a] transition hover:bg-[#fff5f8]">Aplicar titulo</button>
                    @if ($subtitleField)
                        <button type="button" data-quality-apply="subtitle" class="rounded-full border border-[#63102a]/25 px-3 py-1.5 text-xs font-semibold text-[#63102a] transition hover:bg-[#fff5f8]">Aplicar subtitulo</button>
                    @endif
                    <button type="button" data-quality-apply="body" class="rounded-full border border-[#63102a]/25 px-3 py-1.5 text-xs font-semibold text-[#63102a] transition hover:bg-[#fff5f8]">Aplicar texto</button>
                </div>
            </div>
            <div class="mt-3 space-y-3 text-sm leading-6 text-[#201815]">
                <div data-quality-optimized-title class="rounded-[14px] bg-[#fffdfa] px-3 py-2"></div>
                @if ($subtitleField)
                    <div data-quality-optimized-subtitle class="rounded-[14px] bg-[#fffdfa] px-3 py-2"></div>
                @endif
                <div data-quality-optimized-body class="rounded-[14px] bg-[#fffdfa] px-3 py-2 whitespace-pre-line"></div>
            </div>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const renderList = (target, items) => {
                    target.innerHTML = '';
                    (items || []).forEach((item) => {
                        const li = document.createElement('li');
                        li.className = 'rounded-[12px] bg-[#fffdfa] px-3 py-2';
                        li.textContent = item;
                        target.appendChild(li);
                    });
                };

                document.querySelectorAll('.content-quality-agent').forEach((agent) => {
                    const runButton = agent.querySelector('[data-quality-run]');
                    const empty = agent.querySelector('[data-quality-empty]');
                    const result = agent.querySelector('[data-quality-result]');
                    const summary = agent.querySelector('[data-quality-summary]');
                    const score = agent.querySelector('[data-quality-score]');
                    const checks = agent.querySelector('[data-quality-checks]');
                    const suggestions = agent.querySelector('[data-quality-suggestions]');
                    const optimizedTitle = agent.querySelector('[data-quality-optimized-title]');
                    const optimizedSubtitle = agent.querySelector('[data-quality-optimized-subtitle]');
                    const optimizedBody = agent.querySelector('[data-quality-optimized-body]');
                    let lastOptimized = {};

                    const field = (id) => id ? document.getElementById(id) : null;

                    const readPayload = () => ({
                        content_type: agent.dataset.contentType || 'contenido',
                        title: field(agent.dataset.titleField)?.value || '',
                        subtitle: field(agent.dataset.subtitleField)?.value || '',
                        body: field(agent.dataset.bodyField)?.value || '',
                    });

                    const setField = (id, value) => {
                        const target = field(id);
                        if (!target || !value) return;
                        target.value = value;
                        target.dispatchEvent(new Event('input', { bubbles: true }));
                        target.focus();
                    };

                    agent.querySelectorAll('[data-quality-apply]').forEach((button) => {
                        button.addEventListener('click', () => {
                            const key = button.dataset.qualityApply;
                            if (key === 'title') setField(agent.dataset.titleField, lastOptimized.title);
                            if (key === 'subtitle') setField(agent.dataset.subtitleField, lastOptimized.subtitle);
                            if (key === 'body') setField(agent.dataset.bodyField, lastOptimized.body);
                        });
                    });

                    runButton?.addEventListener('click', async () => {
                        const payload = readPayload();

                        if (!payload.title && !payload.body) {
                            empty.textContent = 'Agrega al menos titulo o texto principal para poder revisar el contenido.';
                            return;
                        }

                        runButton.disabled = true;
                        runButton.textContent = 'Revisando...';
                        empty.classList.add('hidden');
                        result.classList.remove('hidden');
                        summary.textContent = 'Analizando calidad editorial...';
                        score.textContent = '';
                        renderList(checks, []);
                        renderList(suggestions, []);
                        optimizedTitle.textContent = '';
                        if (optimizedSubtitle) optimizedSubtitle.textContent = '';
                        optimizedBody.textContent = '';

                        try {
                            const response = await fetch(agent.dataset.endpoint, {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || '',
                                },
                                body: JSON.stringify(payload),
                            });

                            const data = await response.json();

                            if (!response.ok) {
                                throw new Error(data.message || 'No fue posible revisar el contenido.');
                            }

                            lastOptimized = data.optimized || {};
                            summary.textContent = data.summary || 'Revision completada.';
                            score.textContent = `Calidad ${data.score ?? 0}/100`;
                            renderList(checks, data.checks || []);
                            renderList(suggestions, data.suggestions || []);
                            optimizedTitle.textContent = lastOptimized.title ? `Titulo: ${lastOptimized.title}` : 'Titulo: sin sugerencia';
                            if (optimizedSubtitle) {
                                optimizedSubtitle.textContent = lastOptimized.subtitle ? `Subtitulo: ${lastOptimized.subtitle}` : 'Subtitulo: sin sugerencia';
                            }
                            optimizedBody.textContent = lastOptimized.body ? `Texto: ${lastOptimized.body}` : 'Texto: sin sugerencia';
                        } catch (error) {
                            summary.textContent = error.message || 'No fue posible revisar el contenido.';
                            renderList(suggestions, ['Intenta de nuevo o revisa la configuracion del servicio de IA.']);
                        } finally {
                            runButton.disabled = false;
                            runButton.textContent = 'Revisar contenido';
                        }
                    });
                });
            });
        </script>
    @endpush
@endonce
