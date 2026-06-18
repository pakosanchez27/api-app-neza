<div id="admin-assistant" class="fixed bottom-4 right-4 z-30 sm:bottom-6 sm:right-6">
    <style>
        @keyframes admin-assistant-typing {
            0%, 80%, 100% {
                transform: translateY(0);
                opacity: 0.35;
            }

            40% {
                transform: translateY(-3px);
                opacity: 1;
            }
        }

        .admin-assistant-typing-dot {
            animation: admin-assistant-typing 1.25s infinite ease-in-out;
        }

        .admin-assistant-typing-dot:nth-child(2) {
            animation-delay: 0.16s;
        }

        .admin-assistant-typing-dot:nth-child(3) {
            animation-delay: 0.32s;
        }
    </style>

    <section id="admin-assistant-panel"
        class="mb-3 hidden w-[calc(100vw-2rem)] max-w-[390px] overflow-hidden rounded-[18px] border border-[#ead8bd] bg-white shadow-[0_22px_60px_rgba(32,24,21,0.22)]">
        <header class="flex items-center justify-between bg-[#63102a] px-4 py-3 text-white">
            <div class="flex items-center gap-3">
                <img src="{{ asset('img/coyito2.png') }}" alt="Coyito"
                    class="h-11 w-11 rounded-full border-2 border-white/80 bg-white object-cover shadow-sm">
                <div>
                    <p class="text-sm font-semibold">Coyito admin</p>
                    <p class="text-[11px] text-white/72">Soporte del panel</p>
                </div>
            </div>
            <button type="button" id="admin-assistant-close"
                class="grid h-9 w-9 place-items-center rounded-full text-white/80 transition hover:bg-white/10 hover:text-white"
                aria-label="Cerrar asistente">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                    stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </header>

        <div id="admin-assistant-messages" class="max-h-[360px] space-y-3 overflow-y-auto bg-[#fffaf2] p-4">
            <div class="flex items-start gap-2.5">
                <img src="{{ asset('img/coyito2.png') }}" alt="" aria-hidden="true"
                    class="mt-1 h-8 w-8 shrink-0 rounded-full bg-white object-cover shadow-sm">
                <div class="rounded-[16px] bg-white px-3 py-2.5 text-sm leading-5 text-[#2a211d] shadow-sm">
                    Va que va 😄✨ Soy Coyito en modo admin. Te ayudo a encontrar secciones del panel y explicarte pasos para crear, editar o revisar informacion. 🙌
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button" class="admin-assistant-suggestion rounded-full border border-[#d8bd8f] px-3 py-1.5 text-xs font-semibold text-[#63102a] transition hover:bg-[#fff2d8]"
                    data-question="Como creo un evento?">Crear evento</button>
                <button type="button" class="admin-assistant-suggestion rounded-full border border-[#d8bd8f] px-3 py-1.5 text-xs font-semibold text-[#63102a] transition hover:bg-[#fff2d8]"
                    data-question="Como apruebo un comercio?">Aprobar comercio</button>
                <button type="button" class="admin-assistant-suggestion rounded-full border border-[#d8bd8f] px-3 py-1.5 text-xs font-semibold text-[#63102a] transition hover:bg-[#fff2d8]"
                    data-question="Donde edito un punto del mapa?">Punto mapa</button>
            </div>
        </div>

        <form id="admin-assistant-form" class="border-t border-[#ead8bd] bg-white p-3">
            <div class="flex items-end gap-2">
                <label for="admin-assistant-question" class="sr-only">Pregunta al asistente</label>
                <textarea id="admin-assistant-question" rows="1"
                    class="min-h-11 flex-1 resize-none rounded-[14px] border border-[#e2cfad] px-3 py-2 text-sm text-[#2a211d] outline-none transition placeholder:text-[#8f7d72] focus:border-[#63102a] focus:ring-2 focus:ring-[#63102a]/12"
                    placeholder="Pregunta algo del panel..."></textarea>
                <button type="submit"
                    class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-[#63102a] text-white shadow-sm transition hover:bg-[#7f173c]"
                    aria-label="Enviar pregunta">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                        stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L6 12Zm0 0h7.5" />
                    </svg>
                </button>
            </div>
        </form>
    </section>

    <button type="button" id="admin-assistant-toggle"
        class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-full border-2 border-white bg-white text-white shadow-[0_16px_38px_rgba(99,16,42,0.34)] transition hover:scale-[1.03]"
        aria-label="Abrir asistente admin">
        <img src="{{ asset('img/coyito2.png') }}" alt="Abrir Coyito admin" class="h-full w-full object-cover">
    </button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const root = document.getElementById('admin-assistant');
        const panel = document.getElementById('admin-assistant-panel');
        const toggle = document.getElementById('admin-assistant-toggle');
        const close = document.getElementById('admin-assistant-close');
        const form = document.getElementById('admin-assistant-form');
        const input = document.getElementById('admin-assistant-question');
        const messages = document.getElementById('admin-assistant-messages');

        if (!root || !panel || !toggle || !close || !form || !input || !messages) {
            return;
        }

        const avatarUrl = '{{ asset('img/coyito2.png') }}';
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        const endpoint = '{{ route('admin.asistente') }}';
        const currentContext = '{{ request()->route()?->getName() }}';

        const scrollToBottom = () => {
            messages.scrollTop = messages.scrollHeight;
        };

        const appendBubble = (text, type = 'assistant') => {
            const row = document.createElement('div');
            row.className = type === 'user' ? 'flex justify-end' : 'flex items-start gap-2.5';

            if (type !== 'user') {
                const avatar = document.createElement('img');
                avatar.src = avatarUrl;
                avatar.alt = '';
                avatar.setAttribute('aria-hidden', 'true');
                avatar.className = 'mt-1 h-8 w-8 shrink-0 rounded-full bg-white object-cover shadow-sm';
                row.appendChild(avatar);
            }

            const bubble = document.createElement('div');
            bubble.className = type === 'user'
                ? 'max-w-[86%] rounded-[16px] bg-[#63102a] px-3 py-2.5 text-sm leading-5 text-white'
                : 'max-w-[90%] rounded-[16px] bg-white px-3 py-2.5 text-sm leading-5 text-[#2a211d] shadow-sm';
            bubble.textContent = text;
            row.appendChild(bubble);
            messages.appendChild(row);
            scrollToBottom();
            return bubble;
        };

        const appendTypingBubble = () => {
            const row = document.createElement('div');
            row.className = 'flex items-start gap-2.5';

            const avatar = document.createElement('img');
            avatar.src = avatarUrl;
            avatar.alt = '';
            avatar.setAttribute('aria-hidden', 'true');
            avatar.className = 'mt-1 h-8 w-8 shrink-0 rounded-full bg-white object-cover shadow-sm';
            row.appendChild(avatar);

            const bubble = document.createElement('div');
            bubble.className = 'max-w-[90%] rounded-[16px] bg-white px-3 py-3 text-sm leading-5 text-[#2a211d] shadow-sm';
            bubble.innerHTML = `
                <span class="flex items-center gap-1.5" aria-label="Coyito esta escribiendo">
                    <span class="admin-assistant-typing-dot h-2 w-2 rounded-full bg-[#8f7d72]"></span>
                    <span class="admin-assistant-typing-dot h-2 w-2 rounded-full bg-[#8f7d72]"></span>
                    <span class="admin-assistant-typing-dot h-2 w-2 rounded-full bg-[#8f7d72]"></span>
                </span>
            `;
            row.appendChild(bubble);
            messages.appendChild(row);
            scrollToBottom();

            return {
                row,
                replaceWithText(text) {
                    bubble.textContent = text;
                },
            };
        };

        const appendActions = (actions) => {
            if (!Array.isArray(actions) || actions.length === 0) {
                return;
            }

            const wrap = document.createElement('div');
            wrap.className = 'flex flex-wrap gap-2';

            actions.forEach((action) => {
                if (!action.url || !action.label) {
                    return;
                }

                const link = document.createElement('a');
                link.href = action.url;
                link.className = 'rounded-full bg-[#bc955c] px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-[#a47e48]';
                link.textContent = action.label;
                wrap.appendChild(link);
            });

            if (wrap.children.length > 0) {
                messages.appendChild(wrap);
                scrollToBottom();
            }
        };

        const ask = async (question) => {
            const cleanQuestion = question.trim();

            if (!cleanQuestion) {
                return;
            }

            appendBubble(cleanQuestion, 'user');
            input.value = '';
            input.style.height = 'auto';

            const loading = appendTypingBubble();

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify({
                        question: cleanQuestion,
                        context: currentContext,
                    }),
                });

                if (!response.ok) {
                    throw new Error('Request failed');
                }

                const data = await response.json();
                loading.replaceWithText(data.text || 'Va que va 😄✨ No encontre una respuesta para eso. Prueba preguntando por eventos, comercios, usuarios o dashboard. 🙌');
                appendActions(data.actions || []);
            } catch (error) {
                loading.replaceWithText('Va, tuve un atoron respondiendo 😅 Revisa tu sesion e intenta otra vez. 🙌');
            }

            scrollToBottom();
        };

        toggle.addEventListener('click', () => {
            panel.classList.toggle('hidden');
            if (!panel.classList.contains('hidden')) {
                input.focus();
                scrollToBottom();
            }
        });

        close.addEventListener('click', () => {
            panel.classList.add('hidden');
        });

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            ask(input.value);
        });

        input.addEventListener('input', () => {
            input.style.height = 'auto';
            input.style.height = `${Math.min(input.scrollHeight, 120)}px`;
        });

        input.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                form.requestSubmit();
            }
        });

        root.querySelectorAll('.admin-assistant-suggestion').forEach((button) => {
            button.addEventListener('click', () => ask(button.dataset.question || button.textContent || ''));
        });
    });
</script>
