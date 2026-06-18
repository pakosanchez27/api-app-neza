<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardMetricsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MetricsAgentController extends Controller
{
    public function store(Request $request, DashboardMetricsService $metrics): JsonResponse
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:1000'],
        ]);

        $context = $this->agentContext($metrics->summary());
        $fallback = $this->fallbackResponse($validated['question'], $context);

        return response()->json($this->aiResponse($validated['question'], $context, $fallback));
    }

    private function aiResponse(string $question, array $context, array $fallback): array
    {
        $apiKey = config('services.openai.api_key');

        if (! $apiKey) {
            return $fallback + ['engine' => 'rules'];
        }

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->timeout(config('services.openai.timeout', 20))
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => config('services.openai.model', 'gpt-4.1-mini'),
                    'temperature' => 0.2,
                    'max_tokens' => 650,
                    'response_format' => ['type' => 'json_object'],
                    'messages' => [
                        ['role' => 'system', 'content' => $this->systemPrompt()],
                        [
                            'role' => 'user',
                            'content' => json_encode([
                                'question' => $question,
                                'dashboard_metrics' => $context,
                                'fallback_response' => $fallback,
                            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                        ],
                    ],
                ]);

            if (! $response->successful()) {
                logger()->warning('Admin metrics agent OpenAI request failed.', [
                    'status' => $response->status(),
                    'body' => Str::limit($response->body(), 500),
                ]);

                return $fallback + ['engine' => 'rules'];
            }

            $content = data_get($response->json(), 'choices.0.message.content');
            $decoded = is_string($content) ? json_decode($content, true) : null;

            if (! is_array($decoded) || empty($decoded['text'])) {
                return $fallback + ['engine' => 'rules'];
            }

            return [
                'text' => Str::limit((string) $decoded['text'], 1200, ''),
                'highlights' => $this->sanitizeHighlights($decoded['highlights'] ?? $fallback['highlights']),
                'recommendations' => $this->sanitizeHighlights($decoded['recommendations'] ?? $fallback['recommendations']),
                'engine' => 'openai',
            ];
        } catch (\Throwable $exception) {
            logger()->warning('Admin metrics agent OpenAI exception.', [
                'message' => $exception->getMessage(),
            ]);

            return $fallback + ['engine' => 'rules'];
        }
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
Eres el agente de metricas del administrador de ExploraNeza.

Reglas:
- Responde en espanol claro, operativo y breve.
- Usa exclusivamente las metricas del contexto. No inventes datos, fechas, tendencias historicas ni causas.
- No reveles correos ni datos personales. Puedes mencionar nombres de usuarios o comercios solo si vienen en rankings del contexto.
- Explica como leer los indicadores y que accion administrativa podria tomar el equipo.
- Si una pregunta pide algo no disponible, dilo y sugiere que dato faltaria medir.
- Evita promesas absolutas. Usa frases como "podria indicar", "conviene revisar", "la senal principal es".

Devuelve exclusivamente JSON valido:
{
  "text": "analisis principal en 1 o 2 parrafos",
  "highlights": ["dato clave 1", "dato clave 2", "dato clave 3"],
  "recommendations": ["accion sugerida 1", "accion sugerida 2"]
}
PROMPT;
    }

    private function agentContext(array $summary): array
    {
        return [
            'totals' => [
                'usuarios' => $summary['totalUsuarios'],
                'usuarios_nuevos_semana' => $summary['usuariosNuevosSemana'],
                'usuarios_nuevos_mes' => $summary['usuariosNuevosMes'],
                'comercios' => $summary['totalComercios'],
                'comercios_visibles' => $summary['comerciosVisibles'],
                'comercios_incompletos' => $summary['comerciosIncompletos'],
                'rutas_activas' => $summary['totalRutasActivas'],
                'pasaportes' => $summary['totalPasaportes'],
                'pasaportes_completados' => $summary['pasaportesCompletados'],
                'sellos' => $summary['totalSellos'],
            ],
            'rates' => $summary['rates'],
            'top_usuarios_pasaporte' => collect($summary['topUsuariosPasaporte'])
                ->map(fn (array $usuario) => [
                    'nombre' => $usuario['nombre'],
                    'sellos' => $usuario['sellos'],
                    'sellos_posibles' => $usuario['sellos_posibles'],
                    'progreso' => $usuario['progreso'],
                    'pasaportes' => $usuario['pasaportes'],
                    'pasaportes_completados' => $usuario['pasaportes_completados'],
                ])
                ->values()
                ->all(),
            'top_comercios_pasaporte' => collect($summary['topComerciosPasaporte'])
                ->map(fn (array $comercio) => [
                    'nombre' => $comercio['nombre'],
                    'tipo' => $comercio['tipo'],
                    'sellos' => $comercio['sellos'],
                    'visible' => $comercio['visible'],
                    'activo' => $comercio['activo'],
                ])
                ->values()
                ->all(),
        ];
    }

    private function fallbackResponse(string $question, array $context): array
    {
        $totals = $context['totals'];
        $rates = $context['rates'];
        $topUser = $context['top_usuarios_pasaporte'][0] ?? null;
        $topCommerce = $context['top_comercios_pasaporte'][0] ?? null;

        $signals = [];
        $recommendations = [];

        if ($totals['pasaportes'] === 0) {
            $signals[] = 'Todavia no hay pasaportes iniciados; primero hay que activar participacion en rutas.';
            $recommendations[] = 'Promocionar la ruta activa y revisar que los comercios puedan mostrar su QR.';
        } else {
            $signals[] = "El {$rates['pasaportes_completados']}% de pasaportes iniciados esta completado.";
            $signals[] = "Hay {$rates['sellos_por_pasaporte']} sellos por pasaporte en promedio.";
        }

        if ($totals['comercios'] > 0) {
            $signals[] = "El {$rates['comercios_visibles']}% de comercios esta visible al publico.";

            if ($rates['comercios_incompletos'] > 20) {
                $recommendations[] = 'Dar seguimiento a comercios incompletos para aumentar la oferta visible.';
            }
        }

        if ($topUser) {
            $signals[] = "Usuario con mas actividad: {$topUser['nombre']} con {$topUser['sellos']} sellos.";
        }

        if ($topCommerce) {
            $signals[] = "Comercio con mas sellos: {$topCommerce['nombre']} con {$topCommerce['sellos']} validaciones.";
        }

        if ($totals['sellos'] <= 0) {
            $recommendations[] = 'Revisar si los comercios de ruta tienen QR disponible y si la experiencia se esta comunicando en la app.';
        } else {
            $recommendations[] = 'Comparar comercios con mas sellos contra los que no generan actividad para detectar buenas practicas.';
        }

        $recommendations[] = 'Usar el top de usuarios para entender participacion real y el top de comercios para ubicar puntos de mayor traccion.';

        return [
            'text' => $this->fallbackText($question, $totals, $rates),
            'highlights' => array_values(array_slice($signals, 0, 4)),
            'recommendations' => array_values(array_slice($recommendations, 0, 3)),
        ];
    }

    private function fallbackText(string $question, array $totals, array $rates): string
    {
        $normalized = Str::of($question)->ascii()->lower()->toString();

        if (Str::contains($normalized, ['pasaporte', 'sello', 'sellos', 'ruta'])) {
            return "La lectura principal del pasaporte es actividad real: hay {$totals['sellos']} sellos, {$totals['pasaportes']} pasaportes iniciados y {$totals['pasaportes_completados']} completados. Si los pasaportes suben pero los sellos no, podria indicar que los usuarios empiezan la experiencia pero no estan validando visitas.";
        }

        if (Str::contains($normalized, ['comercio', 'comercios', 'visibles', 'incompletos'])) {
            return "En comercios, el dato clave es la relacion entre registrados y visibles: {$totals['comercios_visibles']} de {$totals['comercios']} estan visibles. Los {$totals['comercios_incompletos']} incompletos son una oportunidad operativa porque pueden limitar la oferta que ve el usuario.";
        }

        return "El tablero muestra una foto operativa: {$totals['usuarios']} usuarios, {$totals['comercios']} comercios, {$totals['sellos']} sellos y {$totals['pasaportes']} pasaportes. La senal mas importante no es solo el registro, sino que los sellos y pasaportes completados confirmen participacion real.";
    }

    private function sanitizeHighlights(mixed $items): array
    {
        return collect(is_array($items) ? $items : [])
            ->filter(fn ($item) => is_string($item) && trim($item) !== '')
            ->map(fn (string $item) => Str::limit($item, 180, ''))
            ->take(4)
            ->values()
            ->all();
    }
}
