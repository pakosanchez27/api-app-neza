<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class AdminAssistantController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:1200'],
            'context' => ['nullable', 'string', 'max:120'],
        ]);

        $question = trim($validated['question']);
        $intent = $this->detectIntent($question, (string) ($validated['context'] ?? ''));
        $fallback = $this->responseForIntent($intent);
        $actions = $this->availableActions($fallback['actions']);
        $response = $this->aiResponse($question, $intent, $fallback, $actions, (string) ($validated['context'] ?? ''));

        return response()->json([
            'intent' => $intent,
            'text' => $response['text'],
            'actions' => $response['actions'],
            'engine' => $response['engine'],
            'suggestions' => $this->suggestions(),
        ]);
    }

    private function detectIntent(string $question, string $context): string
    {
        $text = Str::of($question . ' ' . $context)->ascii()->lower()->toString();

        return match (true) {
            Str::contains($text, ['crear evento', 'nuevo evento', 'agregar evento', 'publicar evento', 'alta evento']) => 'eventos_crear',
            Str::contains($text, ['editar evento', 'modificar evento', 'borrar evento', 'eliminar evento', 'eventos']) => 'eventos',
            Str::contains($text, ['aprobar comercio', 'aprobar comercios', 'preregistro', 'pre registro', 'rechazar comercio', 'correccion']) => 'aprobar_comercios',
            Str::contains($text, ['crear noticia', 'nueva noticia', 'publicar noticia', 'editar noticia', 'noticias']) => 'noticias',
            Str::contains($text, ['historia', 'dato historico', 'datos historicos', 'relato']) => 'historia',
            Str::contains($text, ['timeline', 'linea del tiempo', 'antes y despues']) => 'timeline',
            Str::contains($text, ['punto mapa', 'puntos mapa', 'mapa', 'ubicacion', 'ubicación', 'lugar']) => 'puntos_mapa',
            Str::contains($text, ['tipo de negocio', 'categoria', 'categorias', 'catalogo', 'catálogo']) => 'catalogos',
            Str::contains($text, ['usuario', 'usuarios', 'activar usuario', 'desactivar usuario', 'contrasena', 'contraseña']) => 'usuarios',
            Str::contains($text, ['comercio', 'comercios', 'visible', 'visibilidad', 'establecimiento']) => 'comercios',
            Str::contains($text, ['dashboard', 'metrica', 'metricas', 'métrica', 'métricas', 'resumen', 'estadistica']) => 'dashboard',
            default => 'general',
        };
    }

    private function responseForIntent(string $intent): array
    {
        return match ($intent) {
            'eventos_crear' => [
                'text' => 'Para crear un evento entra a Eventos, presiona "Crear" o "Nuevo evento", llena titulo, categoria, fecha, horario, ubicacion, descripcion e imagen. Antes de guardar revisa que la fecha y la direccion sean correctas, porque eso impacta lo que ve el usuario en la app.',
                'actions' => [
                    ['label' => 'Crear evento', 'route' => 'admin.eventos.create', 'permission' => 'eventos.ver'],
                    ['label' => 'Ver eventos', 'route' => 'admin.eventos', 'permission' => 'eventos.ver'],
                ],
            ],
            'eventos' => [
                'text' => 'En Eventos puedes consultar la agenda, crear nuevos registros, editar eventos existentes o eliminarlos cuando ya no deban mostrarse. Si vas a editar uno, abre la lista y usa la accion de edicion del evento correspondiente.',
                'actions' => [
                    ['label' => 'Ver eventos', 'route' => 'admin.eventos', 'permission' => 'eventos.ver'],
                    ['label' => 'Crear evento', 'route' => 'admin.eventos.create', 'permission' => 'eventos.ver'],
                ],
            ],
            'aprobar_comercios' => [
                'text' => 'Para aprobar comercios entra a Aprobar comercios, abre el preregistro y revisa datos fiscales/comerciales, contacto, domicilio, documentos y observaciones. Si todo esta correcto puedes aprobar; si falta algo, solicita correccion indicando el motivo.',
                'actions' => [
                    ['label' => 'Aprobar comercios', 'route' => 'admin.aprobar-comercios', 'permission' => 'aprobar.ver'],
                ],
            ],
            'noticias' => [
                'text' => 'Las noticias se gestionan desde Noticias. Para crear una, agrega titulo claro, resumen, contenido, imagen y estado de publicacion. Conviene revisar ortografia y que la imagen corresponda al tema antes de guardarla.',
                'actions' => [
                    ['label' => 'Ver noticias', 'route' => 'admin.noticias', 'permission' => 'noticias.ver'],
                    ['label' => 'Crear noticia', 'route' => 'admin.noticias.create', 'permission' => 'noticias.ver'],
                ],
            ],
            'historia' => [
                'text' => 'Historia de Neza sirve para publicar contenidos historicos con texto, imagenes y fuentes. Cuando captures informacion historica, procura incluir contexto y fuentes para que el contenido sea confiable.',
                'actions' => [
                    ['label' => 'Historia', 'route' => 'admin.historia', 'permission' => 'historia.ver'],
                    ['label' => 'Crear historia', 'route' => 'admin.historia.create', 'permission' => 'historia.ver'],
                ],
            ],
            'timeline' => [
                'text' => 'Antes y Despues funciona como linea del tiempo visual. Desde ahi puedes crear entradas cronologicas y mantener ordenados los momentos historicos que se muestran en la app.',
                'actions' => [
                    ['label' => 'Antes y Despues', 'route' => 'admin.timeline', 'permission' => 'antesydespues.ver'],
                    ['label' => 'Crear entrada', 'route' => 'admin.timeline.create', 'permission' => 'antesydespues.ver'],
                ],
            ],
            'puntos_mapa' => [
                'text' => 'Los puntos del mapa son lugares de interes que aparecen en la app. Para crear uno, entra a Puntos Mapa, captura nombre, categoria, direccion, coordenadas, descripcion e imagen. Revisa bien latitud y longitud antes de guardar.',
                'actions' => [
                    ['label' => 'Puntos Mapa', 'route' => 'admin.puntos-mapa', 'permission' => 'puntos.ver'],
                    ['label' => 'Crear punto', 'route' => 'admin.puntos-mapa.create', 'permission' => 'puntos.ver'],
                ],
            ],
            'catalogos' => [
                'text' => 'Los catalogos organizan informacion base: tipos de negocio, categorias de eventos y categorias de mapa. Usalos cuando necesites que una opcion aparezca en formularios o filtros del sistema.',
                'actions' => [
                    ['label' => 'Tipos de negocio', 'route' => 'admin.catalogos.tipos-negocio', 'permission' => 'catalogos-explora.ver'],
                    ['label' => 'Categorias eventos', 'route' => 'admin.catalogos.categorias-eventos', 'permission' => 'catalogos-explora.ver'],
                    ['label' => 'Categorias mapa', 'route' => 'admin.catalogos.categorias-mapa', 'permission' => 'catalogos-explora.ver'],
                ],
            ],
            'usuarios' => [
                'text' => 'En Usuarios puedes revisar cuentas de la app, editar datos basicos, activar o desactivar accesos y enviar recuperacion de contrasena cuando aplique. Evita cambiar estados sin confirmar el caso.',
                'actions' => [
                    ['label' => 'Ver usuarios', 'route' => 'admin.usuarios', 'permission' => 'usuarios-app.ver'],
                ],
            ],
            'comercios' => [
                'text' => 'En Comercios puedes consultar establecimientos ya registrados, editar datos, revisar visibilidad, activar o desactivar cuentas y enviar recuperacion de contrasena. Si el comercio aun no esta aprobado, revisa primero Aprobar comercios.',
                'actions' => [
                    ['label' => 'Ver comercios', 'route' => 'admin.comercios', 'permission' => 'comercios.ver'],
                    ['label' => 'Aprobar comercios', 'route' => 'admin.aprobar-comercios', 'permission' => 'aprobar.ver'],
                ],
            ],
            'dashboard' => [
                'text' => 'El Dashboard concentra el estado general: usuarios, comercios, visibilidad, rutas, pasaportes y sellos. Si quieres una lectura rapida, pregunta por comercios, pasaportes o sellos y el agente de metricas puede ayudarte a interpretar los indicadores.',
                'actions' => [
                    ['label' => 'Ir al dashboard', 'route' => 'admin.dashboard', 'permission' => 'dashboard.ver'],
                ],
            ],
            default => [
                'text' => 'Puedo ayudarte a ubicar secciones del administrador y explicarte pasos para crear, editar o revisar contenido. Preguntame por eventos, noticias, aprobar comercios, usuarios, comercios, puntos del mapa, historia, timeline, catalogos o dashboard.',
                'actions' => [
                    ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'permission' => 'dashboard.ver'],
                    ['label' => 'Eventos', 'route' => 'admin.eventos', 'permission' => 'eventos.ver'],
                    ['label' => 'Aprobar comercios', 'route' => 'admin.aprobar-comercios', 'permission' => 'aprobar.ver'],
                ],
            ],
        };
    }

    private function availableActions(array $actions): array
    {
        $permissions = collect(session('admin_permissions', []));
        $bypassPermissions = filter_var(env('ADMIN_AUTH_BYPASS', false), FILTER_VALIDATE_BOOL);

        return collect($actions)
            ->filter(fn (array $action) => $bypassPermissions || empty($action['permission']) || $permissions->contains($action['permission']))
            ->filter(fn (array $action) => isset($action['route']) && Route::has($action['route']))
            ->map(fn (array $action) => [
                'label' => Str::limit((string) $action['label'], 34, ''),
                'url' => route($action['route']),
            ])
            ->values()
            ->take(3)
            ->all();
    }

    private function aiResponse(string $question, string $intent, array $fallback, array $actions, string $context): array
    {
        $apiKey = config('services.openai.api_key');

        if (! $apiKey) {
            return [
                'text' => $this->withCoyitoPersonality($fallback['text']),
                'actions' => $actions,
                'engine' => 'rules',
            ];
        }

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->timeout(config('services.openai.timeout', 20))
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => config('services.openai.model', 'gpt-4.1-mini'),
                    'temperature' => 0.35,
                    'max_tokens' => 520,
                    'response_format' => ['type' => 'json_object'],
                    'messages' => [
                        ['role' => 'system', 'content' => $this->systemPrompt()],
                        [
                            'role' => 'user',
                            'content' => json_encode([
                                'question' => $question,
                                'detected_intent' => $intent,
                                'current_route' => $context,
                                'fallback_response' => [
                                    'text' => $fallback['text'],
                                    'actions' => $actions,
                                ],
                                'allowed_actions' => $actions,
                            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                        ],
                    ],
                ]);

            if (! $response->successful()) {
                logger()->warning('Admin assistant OpenAI request failed.', [
                    'status' => $response->status(),
                    'body' => Str::limit($response->body(), 500),
                ]);

                return [
                    'text' => $this->withCoyitoPersonality($fallback['text']),
                    'actions' => $actions,
                    'engine' => 'rules',
                ];
            }

            $content = data_get($response->json(), 'choices.0.message.content');
            $decoded = is_string($content) ? json_decode($content, true) : null;

            if (! is_array($decoded) || empty($decoded['text'])) {
                return [
                    'text' => $this->withCoyitoPersonality($fallback['text']),
                    'actions' => $actions,
                    'engine' => 'rules',
                ];
            }

            return [
                'text' => Str::limit((string) $decoded['text'], 900, ''),
                'actions' => $this->sanitizeActions($decoded['actions'] ?? [], $actions),
                'engine' => 'openai',
            ];
        } catch (\Throwable $exception) {
            logger()->warning('Admin assistant OpenAI exception.', [
                'message' => $exception->getMessage(),
            ]);

            return [
                'text' => $this->withCoyitoPersonality($fallback['text']),
                'actions' => $actions,
                'engine' => 'rules',
            ];
        }
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
Eres Coyito, el asistente oficial de ExploraNeza, en modo administrador.

Reglas:
- Responde en espanol de Mexico, alegre, servicial, cero acartonado y con vibra chilanga/CDMX.
- Usa jerga ligera como "va que va", "va", "chido", "de volada", "por aca", "te late", sin exagerar ni sonar burlon.
- Usa emojis utiles y alegres en cada respuesta, especialmente al inicio y al final.
- Tu audiencia son administradores de la app, asi que conserva claridad operativa.
- Ayuda a navegar y usar el panel: dashboard, noticias, eventos, comercios, usuarios, aprobar comercios, puntos mapa, catalogos, historia y timeline.
- Usa solo la informacion del contexto. No inventes permisos, datos, metricas, usuarios, comercios, eventos ni rutas.
- No prometas ejecutar acciones ni modificar datos. Tu trabajo es explicar pasos y mandar a la ruta correcta.
- Si el usuario pide crear, editar, aprobar o revisar algo, explica el proceso en pasos breves y conserva los botones permitidos.
- Devuelve botones usando solo allowed_actions. No inventes URLs.
- Responde en maximo 2 parrafos cortos.

Devuelve exclusivamente JSON valido:
{
  "text": "respuesta para el administrador",
  "actions": [{"label": "texto del boton", "url": "url permitida"}]
}
PROMPT;
    }

    private function withCoyitoPersonality(string $text): string
    {
        $text = trim($text);

        if ($text === '') {
            return 'Va que va 😄✨ Puedo ayudarte a encontrar secciones del administrador y explicarte el paso a paso. 🙌';
        }

        return "Va que va 😄✨ {$text} 🙌";
    }

    private function sanitizeActions(mixed $actions, array $fallbackActions): array
    {
        $allowed = collect($fallbackActions)->keyBy('url');

        $clean = collect(is_array($actions) ? $actions : [])
            ->filter(fn ($action) => is_array($action) && isset($action['url']) && $allowed->has($action['url']))
            ->map(function (array $action) use ($allowed) {
                $allowedAction = $allowed->get($action['url']);

                return [
                    'label' => Str::limit((string) ($action['label'] ?? $allowedAction['label']), 34, ''),
                    'url' => $allowedAction['url'],
                ];
            })
            ->unique('url')
            ->values()
            ->take(3)
            ->all();

        return $clean ?: $fallbackActions;
    }

    private function suggestions(): array
    {
        return [
            'Como creo un evento?',
            'Como apruebo un comercio?',
            'Donde edito un punto del mapa?',
            'Que reviso en el dashboard?',
        ];
    }
}
