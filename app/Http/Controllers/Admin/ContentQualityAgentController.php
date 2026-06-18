<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ContentQualityAgentController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'content_type' => ['required', 'string', 'max:40'],
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string', 'max:6000'],
        ]);

        $fallback = $this->fallbackResponse($validated);

        return response()->json($this->aiResponse($validated, $fallback));
    }

    private function aiResponse(array $content, array $fallback): array
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
                    'temperature' => 0.25,
                    'max_tokens' => 850,
                    'response_format' => ['type' => 'json_object'],
                    'messages' => [
                        ['role' => 'system', 'content' => $this->systemPrompt()],
                        [
                            'role' => 'user',
                            'content' => json_encode([
                                'content' => $content,
                                'fallback_response' => $fallback,
                            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                        ],
                    ],
                ]);

            if (! $response->successful()) {
                logger()->warning('Admin content quality agent OpenAI request failed.', [
                    'status' => $response->status(),
                    'body' => Str::limit($response->body(), 500),
                ]);

                return $fallback + ['engine' => 'rules'];
            }

            $decoded = json_decode((string) data_get($response->json(), 'choices.0.message.content'), true);

            if (! is_array($decoded) || empty($decoded['summary'])) {
                return $fallback + ['engine' => 'rules'];
            }

            return [
                'summary' => Str::limit((string) $decoded['summary'], 700, ''),
                'score' => max(0, min(100, (int) ($decoded['score'] ?? $fallback['score']))),
                'checks' => $this->sanitizeList($decoded['checks'] ?? $fallback['checks']),
                'suggestions' => $this->sanitizeList($decoded['suggestions'] ?? $fallback['suggestions']),
                'optimized' => [
                    'title' => Str::limit((string) data_get($decoded, 'optimized.title', $fallback['optimized']['title']), 120, ''),
                    'subtitle' => Str::limit((string) data_get($decoded, 'optimized.subtitle', $fallback['optimized']['subtitle']), 260, ''),
                    'body' => Str::limit((string) data_get($decoded, 'optimized.body', $fallback['optimized']['body']), 1200, ''),
                ],
                'engine' => 'openai',
            ];
        } catch (\Throwable $exception) {
            logger()->warning('Admin content quality agent OpenAI exception.', [
                'message' => $exception->getMessage(),
            ]);

            return $fallback + ['engine' => 'rules'];
        }
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
Eres el agente de calidad de contenido del administrador de ExploraNeza.

Tu trabajo es revisar contenido editorial para noticias, eventos, historia y timeline.

Reglas:
- Responde en espanol claro y operativo.
- Conserva el sentido del texto original y no inventes datos, fechas, lugares, cifras, nombres, horarios ni promesas.
- Si falta informacion critica, senalala como pendiente en vez de rellenarla.
- Mejora claridad, ortografia, tono institucional cercano, estructura y llamado a la accion cuando aplique.
- Para historia/timeline cuida precision y evita afirmaciones absolutas si no hay fuente.
- Para eventos revisa que el texto invite a asistir sin prometer disponibilidad.
- Para noticias prioriza titular claro, resumen informativo y lectura rapida.
- No uses emojis.

Devuelve exclusivamente JSON valido:
{
  "summary": "diagnostico breve",
  "score": 0,
  "checks": ["check editorial"],
  "suggestions": ["mejora puntual"],
  "optimized": {
    "title": "titulo sugerido",
    "subtitle": "subtitulo sugerido si aplica",
    "body": "texto principal sugerido"
  }
}
PROMPT;
    }

    private function fallbackResponse(array $content): array
    {
        $title = trim((string) ($content['title'] ?? ''));
        $subtitle = trim((string) ($content['subtitle'] ?? ''));
        $body = trim((string) ($content['body'] ?? ''));
        $type = Str::of((string) $content['content_type'])->ascii()->lower()->toString();
        $wordCount = str_word_count(Str::ascii($body));

        $checks = [];
        $suggestions = [];
        $score = 100;

        if ($title === '') {
            $checks[] = 'Falta titulo.';
            $suggestions[] = 'Agrega un titulo concreto que explique el tema principal.';
            $score -= 25;
        } elseif (strlen($title) > 70) {
            $checks[] = 'El titulo puede ser largo para lectura rapida.';
            $suggestions[] = 'Reduce el titulo a una idea principal, idealmente menor a 70 caracteres.';
            $score -= 10;
        } else {
            $checks[] = 'El titulo tiene una longitud manejable.';
        }

        if ($body === '') {
            $checks[] = 'Falta texto principal.';
            $suggestions[] = 'Agrega una descripcion que responda que ocurre, donde, cuando y por que importa.';
            $score -= 35;
        } elseif ($wordCount < 18) {
            $checks[] = 'El texto principal se percibe muy breve.';
            $suggestions[] = 'Agrega contexto suficiente para que el usuario entienda el valor del contenido.';
            $score -= 15;
        } else {
            $checks[] = 'El texto principal ya aporta contexto basico.';
        }

        if (Str::contains($type, ['evento']) && ! Str::contains(Str::ascii($body), ['fecha', 'hora', 'lugar', 'ubicacion', 'asist'])) {
            $suggestions[] = 'En eventos, verifica que fecha, horario, sede o ubicacion esten claros en el formulario.';
            $score -= 8;
        }

        if (Str::contains($type, ['historia', 'timeline']) && ! Str::contains(Str::ascii($body), ['fuente', 'registro', 'historia', 'archivo', 'memoria'])) {
            $suggestions[] = 'En contenidos historicos, conviene mencionar contexto o fuente cuando este disponible.';
            $score -= 8;
        }

        $optimizedTitle = $this->cleanSentence($title);
        $optimizedBody = $this->cleanSentence($body);
        $optimizedSubtitle = $this->cleanSentence($subtitle);

        return [
            'summary' => $score >= 80
                ? 'El contenido tiene una base clara. Conviene revisar pequenos ajustes de precision, longitud y enfoque antes de publicarlo.'
                : 'El contenido necesita ajustes antes de publicarse. Revisa los puntos marcados para mejorar claridad y utilidad.',
            'score' => max(0, min(100, $score)),
            'checks' => array_values(array_slice($checks, 0, 4)),
            'suggestions' => array_values(array_slice($suggestions ?: ['Revisa ortografia, claridad del primer enunciado y que la imagen corresponda al contenido.'], 0, 4)),
            'optimized' => [
                'title' => $optimizedTitle,
                'subtitle' => $optimizedSubtitle,
                'body' => $optimizedBody,
            ],
        ];
    }

    private function cleanSentence(string $value): string
    {
        $value = trim(preg_replace('/\s+/', ' ', $value) ?? $value);

        if ($value === '') {
            return '';
        }

        return Str::ucfirst($value);
    }

    private function sanitizeList(mixed $items): array
    {
        return collect(is_array($items) ? $items : [])
            ->filter(fn ($item) => is_string($item) && trim($item) !== '')
            ->map(fn (string $item) => Str::limit($item, 180, ''))
            ->take(5)
            ->values()
            ->all();
    }
}
