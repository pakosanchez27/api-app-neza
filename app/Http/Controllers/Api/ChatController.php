<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cupon;
use App\Models\Establecimiento;
use App\Models\EventoModel;
use App\Models\PuntoMapa;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'messages' => ['required', 'array', 'min:1'],
            'messages.*.role' => ['nullable', 'string'],
            'messages.*.content' => ['required', 'string', 'max:2000'],
            'location' => ['nullable', 'array'],
            'location.latitude' => ['required_with:location', 'numeric', 'between:-90,90'],
            'location.longitude' => ['required_with:location', 'numeric', 'between:-180,180'],
            'location.accuracy' => ['nullable', 'numeric', 'min:0'],
        ]);

        $message = $this->lastUserMessage($validated['messages']);
        $intent = $this->detectIntent($message);
        $userLocation = $this->userLocation($validated);
        $fallback = $this->responseForIntent($intent, $message, $userLocation);

        return response()->json($this->aiResponse($validated['messages'], $intent, $fallback, $userLocation));
    }

    private function lastUserMessage(array $messages): string
    {
        $userMessages = array_values(array_filter(
            $messages,
            fn (array $message) => ($message['role'] ?? 'user') === 'user'
        ));

        $lastMessage = end($userMessages) ?: end($messages);

        return trim((string) ($lastMessage['content'] ?? ''));
    }

    private function detectIntent(string $message): string
    {
        $text = Str::of($message)->ascii()->lower()->toString();

        return match (true) {
            $this->isOutOfAppScope($text) => 'fuera_alcance',
            $this->looksLikePlaceLookup($text) => 'place_lookup',
            Str::contains($text, ['evento', 'agenda', 'concierto', 'actividad', 'que hay hoy']) => 'eventos',
            Str::contains($text, ['cupon', 'descuento', 'promo', 'promocion', 'oferta']) => 'cupones',
            Str::contains($text, ['pasaporte', 'sello', 'sellar', 'qr']) => 'pasaporte',
            Str::contains($text, ['comida', 'comer', 'gastronom', 'restaurante', 'ruta gastronomica']) => 'ruta_gastronomica',
            Str::contains($text, ['mercado', 'hospital', 'clinica', 'bombero', 'policia', 'proteccion civil', 'seguridad', 'emergencia']) => 'puntos_mapa',
            Str::contains($text, ['mapa', 'lugar', 'sitio', 'cerca', 'ubicacion', 'monumento']) => 'mapa',
            Str::contains($text, ['tianguis', 'mercado', 'puestos']) => 'tianguis',
            Str::contains($text, ['historia', 'historico', 'timeline', 'neza']) => 'historia',
            Str::contains($text, ['transporte', 'ruta de transporte', 'camion', 'combi']) => 'transporte',
            Str::contains($text, ['comercio', 'negocio', 'establecimiento', 'registrar mi negocio']) => 'comercios',
            Str::contains($text, ['login', 'cuenta', 'registrarme', 'contrasena', 'password', 'activar']) => 'cuenta',
            default => 'general',
        };
    }

    private function userLocation(array $validated): ?array
    {
        if (! isset($validated['location']['latitude'], $validated['location']['longitude'])) {
            return null;
        }

        return [
            'latitude' => (float) $validated['location']['latitude'],
            'longitude' => (float) $validated['location']['longitude'],
            'accuracy' => isset($validated['location']['accuracy']) ? (float) $validated['location']['accuracy'] : null,
        ];
    }

    private function responseForIntent(string $intent, string $message, ?array $userLocation = null): array
    {
        return match ($intent) {
            'fuera_alcance' => $this->outOfScopeResponse(),
            'eventos' => $this->eventosResponse(),
            'cupones' => $this->cuponesResponse(),
            'pasaporte' => $this->pasaporteResponse(),
            'ruta_gastronomica' => $this->rutaGastronomicaResponse($userLocation),
            'place_lookup' => $this->placeLookupResponse($message, $userLocation),
            'puntos_mapa' => $this->puntosMapaResponse($message, $userLocation),
            'mapa' => $this->mapaResponse(),
            'tianguis' => $this->tianguisResponse(),
            'historia' => $this->historiaResponse(),
            'transporte' => $this->transporteResponse(),
            'comercios' => $this->comerciosResponse(),
            'cuenta' => $this->cuentaResponse(),
            default => $this->generalResponse($message),
        };
    }

    private function looksLikePlaceLookup(string $normalizedText): bool
    {
        if (Str::contains($normalizedText, ['donde esta', 'donde queda', 'direccion de', 'ubicacion de', 'como llego a', 'como llegar a', 'com llego'])) {
            return true;
        }

        if (Str::contains($normalizedText, ['estadio', 'deportivo', 'centro cultural', 'mercado', 'hospital', 'clinica'])) {
            return true;
        }

        return false;
    }

    private function isOutOfAppScope(string $normalizedText): bool
    {
        $appTerms = [
            'neza', 'nezahualcoyotl', 'exploraneza', 'evento', 'agenda', 'cupon', 'pasaporte',
            'sello', 'mapa', 'ruta', 'gastronom', 'comercio', 'establecimiento', 'tianguis',
            'historia de neza', 'punto', 'transporte', 'mercado', 'hospital', 'clinica',
        ];

        if (Str::contains($normalizedText, $appTerms)) {
            return false;
        }

        return Str::contains($normalizedText, [
            'tarea', 'definicion', 'define', 'que es', 'que significa', 'ensayo', 'resumen',
            'matematic', 'geografia', 'biologia', 'fisica', 'quimica', 'ingles', 'traduce',
            'codigo', 'programacion', 'receta', 'dieta', 'medico', 'legal', 'finanzas',
        ]);
    }

    private function aiResponse(array $messages, string $intent, array $fallback, ?array $userLocation): array
    {
        if (in_array($intent, ['place_lookup', 'fuera_alcance'], true)) {
            return $this->withCoyitoPersonality($fallback) + ['engine' => 'rules'];
        }

        $apiKey = config('services.openai.api_key');

        if (! $apiKey) {
            return $this->withCoyitoPersonality($fallback) + ['engine' => 'rules'];
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
                        [
                            'role' => 'system',
                            'content' => $this->systemPrompt(),
                        ],
                        [
                            'role' => 'user',
                            'content' => json_encode([
                                'conversation' => $this->compactMessages($messages),
                                'detected_intent' => $intent,
                                'user_location' => $userLocation,
                                'app_context' => $this->appContext($userLocation),
                                'fallback_response' => $fallback,
                                'allowed_actions' => $this->allowedActions(),
                            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                        ],
                    ],
                ]);

            if (! $response->successful()) {
                logger()->warning('Coyito OpenAI request failed.', [
                    'status' => $response->status(),
                    'body' => Str::limit($response->body(), 500),
                ]);

                return $this->withCoyitoPersonality($fallback) + ['engine' => 'rules'];
            }

            $content = data_get($response->json(), 'choices.0.message.content');
            $decoded = is_string($content) ? json_decode($content, true) : null;

            if (! is_array($decoded) || empty($decoded['text'])) {
                return $this->withCoyitoPersonality($fallback) + ['engine' => 'rules'];
            }

            return [
                'intent' => $intent,
                'text' => Str::limit((string) $decoded['text'], 900, ''),
                'actions' => $this->sanitizeActions($decoded['actions'] ?? [], $fallback['actions'] ?? []),
                'engine' => 'openai',
            ];
        } catch (\Throwable $exception) {
            logger()->warning('Coyito OpenAI exception.', [
                'message' => $exception->getMessage(),
            ]);

            return $this->withCoyitoPersonality($fallback) + ['engine' => 'rules'];
        }
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
Eres Coyito, el asistente oficial de ExploraNeza, una PWA turistica y comercial de Nezahualcoyotl.

Reglas:
- Responde en espanol de Mexico, alegre, servicial, cercano e institucional, adecuado para una app de gobierno.
- No uses groserias, palabras altisonantes, doble sentido ni expresiones vulgares o corrientes. Evita frases como "a huevo", "chingada", "chido", "bronca", "no manches", "orale", "que onda", "de volada", "te late" o similares.
- Puedes sonar amable y juvenil, pero siempre con lenguaje respetuoso, claro y apto para todo publico.
- Usa muchos emojis en cada respuesta, especialmente al inicio y al final. Manténlos útiles y alegres: 😄✨📍🚶‍♂️🗺️🙌🌮🎟️.
- Usa solo el contexto proporcionado. No inventes eventos, horarios, cupones, comercios ni ubicaciones.
- No respondas preguntas generales, tareas escolares, definiciones, consejos medicos/legales/financieros, programacion ni temas que no pertenezcan a ExploraNeza.
- Si la pregunta esta fuera de ExploraNeza, responde amablemente que solo puedes ayudar con la app y redirige a funciones disponibles. No expliques el tema externo.
- Para recomendar comida, usa establecimientos activos y visibles del contexto. Si hay distancia, menciona los mas cercanos.
- Para mercados, hospitales, clinicas, policia, bomberos, proteccion civil o emergencias, usa puntos_mapa del contexto. Si hay una accion de Google Maps en fallback_response, incluyela.
- Si falta un dato, dilo y manda a la seccion adecuada de la app.
- Da recomendaciones practicas, maximo 2 parrafos cortos.
- Puedes devolver botones usando solo las acciones permitidas.
- No prometas compras, reservaciones, beneficios, premios ni disponibilidad si no esta en el contexto.
- No te salgas de tu rol de asistente de la app, no ofrezcas ayuda fuera de ella ni digas que puedes hacer cosas que no sean responder preguntas o dar recomendaciones basadas en el contexto.

Devuelve exclusivamente JSON valido con esta forma:
{
  "text": "respuesta para el usuario",
  "actions": [{"label": "texto del boton", "to": "/ruta"}]
}
PROMPT;
    }

    private function withCoyitoPersonality(array $response): array
    {
        $text = trim((string) ($response['text'] ?? ''));

        if ($text !== '' && ! Str::contains($text, ['😄', '✨', '📍', '🙌'])) {
            $response['text'] = "¡Va que va! 😄✨ {$text} 📍🗺️🙌";
        }

        return $response;
    }

    private function compactMessages(array $messages): array
    {
        return collect($messages)
            ->take(-8)
            ->map(fn (array $message) => [
                'role' => ($message['role'] ?? 'user') === 'assistant' ? 'assistant' : 'user',
                'content' => Str::limit((string) ($message['content'] ?? ''), 700, ''),
            ])
            ->values()
            ->all();
    }

    private function appContext(?array $userLocation = null): array
    {
        return [
            'app_name' => 'ExploraNeza',
            'sections' => [
                'mapa' => 'Sitios de interes y puntos de la ciudad.',
                'eventos' => 'Agenda local con fechas, ubicacion y detalle.',
                'ruta_gastronomica' => 'Comercios participantes para comer y sumar sellos.',
                'cuponera' => 'Promociones activas de comercios locales.',
                'pasaporte' => 'Sellos por visitas a establecimientos participantes.',
                'historia' => 'Relatos, datos historicos y timeline de Nezahualcoyotl.',
                'tianguis' => 'Tianguis disponibles por dia dentro de la PWA.',
                'transporte' => 'Rutas de transporte mostradas en la app.',
            ],
            'eventos_proximos' => $this->contextEventos(),
            'cupones_activos' => $this->contextCupones(),
            'establecimientos_para_comer' => $this->contextEstablecimientosComida($userLocation),
            'ruta_gastronomica' => $this->contextRutaGastronomica($userLocation),
            'puntos_mapa' => $this->contextPuntosMapa($userLocation),
        ];
    }

    private function contextEventos(): array
    {
        return EventoModel::query()
            ->where('estatus', 1)
            ->where(fn ($query) => $query->whereNull('fecha')->orWhere('fecha', '>=', now()->toDateString()))
            ->orderBy('fecha')
            ->orderBy('hora')
            ->limit(5)
            ->get(['id', 'titulo', 'fecha', 'hora', 'calle', 'numero', 'colonia', 'acerca'])
            ->map(fn (EventoModel $evento) => [
                'titulo' => $evento->titulo,
                'fecha' => $evento->fecha,
                'hora' => $evento->hora,
                'ubicacion' => trim(collect([$evento->calle, $evento->numero, $evento->colonia])->filter()->implode(' ')),
                'descripcion' => Str::limit((string) $evento->acerca, 180, ''),
                'accion' => '/eventos',
            ])
            ->values()
            ->all();
    }

    private function contextCupones(): array
    {
        return Cupon::query()
            ->with('establecimiento:id_establecimiento,nombre_est')
            ->where('is_active', true)
            ->where(fn ($query) => $query->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>=', now()))
            ->latest('id')
            ->limit(5)
            ->get()
            ->map(fn (Cupon $cupon) => [
                'titulo' => $cupon->title,
                'descripcion' => Str::limit((string) $cupon->description, 140, ''),
                'establecimiento' => $cupon->establecimiento?->nombre_est,
                'vigencia' => optional($cupon->expires_at)?->toDateString(),
                'accion' => '/cuponera',
            ])
            ->values()
            ->all();
    }

    private function contextEstablecimientosComida(?array $userLocation = null, int $limit = 8): array
    {
        $establecimientos = Establecimiento::query()
            ->with(['domicilio', 'tipo', 'horarios'])
            ->where('estatus', true)
            ->where('is_visible', true)
            ->orderBy('nombre_est')
            ->limit(60)
            ->get(['id_establecimiento', 'nombre_est', 'descripcion', 'id_tipo', 'is_route']);

        return $establecimientos
            ->map(function (Establecimiento $establecimiento) use ($userLocation) {
                $latitud = $establecimiento->domicilio?->latitud;
                $longitud = $establecimiento->domicilio?->longitud;
                $distance = $this->distanceInKm($userLocation, $latitud, $longitud);

                return [
                    'nombre' => $establecimiento->nombre_est,
                    'tipo' => $establecimiento->tipo?->nombre,
                    'descripcion' => Str::limit((string) $establecimiento->descripcion, 150, ''),
                    'direccion' => $this->formatAddress($establecimiento),
                    'colonia' => $establecimiento->domicilio?->colonia,
                    'distancia_km' => $distance,
                    'distancia_texto' => $distance !== null ? number_format($distance, 1) . ' km' : null,
                    'es_ruta_gastronomica' => (bool) $establecimiento->is_route,
                    'horario_hoy' => $this->todaySchedule($establecimiento),
                    'accion' => '/mapa',
                ];
            })
            ->sortBy(fn (array $item) => $item['distancia_km'] ?? 999999)
            ->take($limit)
            ->values()
            ->all();
    }

    private function contextRutaGastronomica(?array $userLocation = null): array
    {
        return collect($this->contextEstablecimientosComida($userLocation, 12))
            ->filter(fn (array $establecimiento) => $establecimiento['es_ruta_gastronomica'])
            ->take(6)
            ->values()
            ->all();
    }

    private function distanceInKm(?array $userLocation, mixed $latitud, mixed $longitud): ?float
    {
        if (! $userLocation || $latitud === null || $longitud === null) {
            return null;
        }

        $lat1 = deg2rad((float) $userLocation['latitude']);
        $lon1 = deg2rad((float) $userLocation['longitude']);
        $lat2 = deg2rad((float) $latitud);
        $lon2 = deg2rad((float) $longitud);
        $deltaLat = $lat2 - $lat1;
        $deltaLon = $lon2 - $lon1;

        $a = sin($deltaLat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($deltaLon / 2) ** 2;

        return round(6371 * 2 * atan2(sqrt($a), sqrt(1 - $a)), 2);
    }

    private function formatAddress(Establecimiento $establecimiento): string
    {
        $domicilio = $establecimiento->domicilio;

        if (! $domicilio) {
            return '';
        }

        return collect([
            $domicilio->calle,
            $domicilio->num_ext ? '#' . $domicilio->num_ext : null,
            $domicilio->colonia,
        ])->filter()->implode(', ');
    }

    private function todaySchedule(Establecimiento $establecimiento): ?string
    {
        $today = (int) now()->dayOfWeekIso;
        $schedule = $establecimiento->horarios->firstWhere('dia_semana', $today);

        if (! $schedule) {
            return null;
        }

        if ($schedule->cerrado) {
            return 'Cerrado hoy';
        }

        return trim("{$schedule->hora_apertura} - {$schedule->hora_cierra}");
    }

    private function rutaGastronomicaResponse(?array $userLocation = null): array
    {
        $establecimientos = collect($this->contextEstablecimientosComida($userLocation, 6));

        if ($establecimientos->isEmpty()) {
            return [
                'intent' => 'ruta_gastronomica',
                'text' => 'No encuentro establecimientos activos y visibles por ahora. Puedes revisar el mapa por si se publicaron nuevos lugares.',
                'actions' => [
                    ['label' => 'Abrir mapa', 'to' => '/mapa'],
                    ['label' => 'Ruta gastronómica', 'to' => '/ruta-gastronomica'],
                ],
            ];
        }

        $items = $establecimientos
            ->take(3)
            ->map(function (array $establecimiento) {
                $distance = $establecimiento['distancia_texto'] ? " a {$establecimiento['distancia_texto']}" : '';
                $tipo = $establecimiento['tipo'] ? " ({$establecimiento['tipo']})" : '';

                return "{$establecimiento['nombre']}{$tipo}{$distance}";
            })
            ->implode('; ');

        $locationNote = $userLocation
            ? ' Te los ordené por cercanía con tu ubicación.'
            : ' Si permites tu ubicación, puedo ordenarlos por cercanía.';

        return [
            'intent' => 'ruta_gastronomica',
            'text' => "Para comer, te recomiendo: {$items}.{$locationNote}",
            'actions' => [
                ['label' => 'Abrir mapa', 'to' => '/mapa'],
                ['label' => 'Ruta gastronómica', 'to' => '/ruta-gastronomica'],
            ],
        ];
    }

    private function puntosMapaResponse(string $message, ?array $userLocation = null): array
    {
        $categoryGroup = $this->detectPointCategoryGroup($message);
        $label = $categoryGroup['label'];

        if (! $userLocation) {
            return [
                'intent' => 'puntos_mapa',
                'text' => "Puedo ayudarte a encontrar {$label} cerca de ti, pero necesito permiso de ubicación para ordenar por distancia real. También puedes abrir el mapa y buscarlo manualmente.",
                'actions' => [
                    ['label' => 'Abrir mapa', 'to' => '/mapa'],
                ],
            ];
        }

        $puntos = PuntoMapa::query()
            ->with('categoria:id,tipo')
            ->where('estatus', 1)
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->get(['id', 'nombre_punto', 'descripcion', 'categoria_id', 'calle', 'numero_exterior', 'numero_interior', 'colonia', 'cp', 'horarios', 'latitud', 'longitud']);

        $nearest = $puntos
            ->filter(function (PuntoMapa $punto) use ($categoryGroup) {
                $category = Str::of((string) $punto->categoria?->tipo)->ascii()->lower()->toString();

                return collect($categoryGroup['categories'])->contains(
                    fn (string $expected) => Str::contains($category, $expected)
                );
            })
            ->map(function (PuntoMapa $punto) use ($userLocation) {
                $punto->distance_km = $this->distanceInKm($userLocation, $punto->latitud, $punto->longitud);

                return $punto;
            })
            ->filter(fn (PuntoMapa $punto) => $punto->distance_km !== null)
            ->sortBy('distance_km')
            ->first();

        if (! $nearest) {
            return [
                'intent' => 'puntos_mapa',
                'text' => "No encontré {$label} con ubicación registrada en el mapa. Puedes abrir el mapa para revisar otras categorías disponibles.",
                'actions' => [
                    ['label' => 'Abrir mapa', 'to' => '/mapa'],
                ],
            ];
        }

        $distanceText = number_format((float) $nearest->distance_km, 1) . ' km';
        $address = $this->formatPointAddress($nearest);
        $hours = $nearest->horarios ? " Horario: {$nearest->horarios}." : '';

        return [
            'intent' => 'puntos_mapa',
            'text' => "El {$label} más cercano que encontré es {$nearest->nombre_punto}, a {$distanceText}. " . ($address ? "Está en {$address}." : '') . $hours,
            'actions' => [
                [
                    'label' => 'Cómo llegar',
                    'url' => $this->googleMapsDirectionsUrl(
                        $userLocation['latitude'],
                        $userLocation['longitude'],
                        (float) $nearest->latitud,
                        (float) $nearest->longitud
                    ),
                    'external' => true,
                ],
                ['label' => 'Abrir mapa', 'to' => '/mapa'],
            ],
        ];
    }

    private function placeLookupResponse(string $message, ?array $userLocation = null): array
    {
        $query = $this->extractPlaceQuery($message);
        $match = $this->findBestPlaceMatch($query, $userLocation);

        if (! $match) {
            return [
                'intent' => 'place_lookup',
                'text' => "No encontré un punto o establecimiento con ese nombre en la base. Puedes abrir el mapa y revisar las categorías disponibles.",
                'actions' => [
                    ['label' => 'Abrir mapa', 'to' => '/mapa'],
                ],
            ];
        }

        $parts = [
            $match['category'] ? "Categoría: {$match['category']}." : null,
            $match['address'] ? "Dirección: {$match['address']}." : null,
            $match['hours'] ? "Horario: {$match['hours']}." : null,
            $match['phone'] ? "Teléfono: {$match['phone']}." : null,
            $match['description'] ? "Detalle: {$match['description']}." : null,
            $match['distance_text'] ? "Está aproximadamente a {$match['distance_text']} de tu ubicación." : null,
        ];

        $actions = [
            ['label' => 'Abrir mapa', 'to' => '/mapa'],
        ];

        if ($match['latitud'] !== null && $match['longitud'] !== null) {
            $actions[] = [
                'label' => 'Cómo llegar',
                'url' => $userLocation
                    ? $this->googleMapsDirectionsUrl($userLocation['latitude'], $userLocation['longitude'], $match['latitud'], $match['longitud'])
                    : $this->googleMapsDestinationUrl($match['latitud'], $match['longitud']),
                'external' => true,
            ];
        }

        return [
            'intent' => 'place_lookup',
            'text' => "{$match['name']} sí está registrado en el mapa. " . implode(' ', array_values(array_filter($parts))),
            'actions' => $actions,
        ];
    }

    private function extractPlaceQuery(string $message): string
    {
        $query = $this->normalizePlaceText($message);
        $query = preg_replace('/\b(donde|esta|queda|quedan|ubicacion|direccion|como|com|llego|llegar|al|a|la|el|del|de|para|ir|voy|puedo)\b/u', ' ', $query) ?? $query;

        return trim(preg_replace('/\s+/', ' ', $query) ?? $query);
    }

    private function findBestPlaceMatch(string $query, ?array $userLocation = null): ?array
    {
        $pointMatches = PuntoMapa::query()
            ->with('categoria:id,tipo')
            ->where('estatus', 1)
            ->get(['id', 'nombre_punto', 'descripcion', 'categoria_id', 'calle', 'numero_exterior', 'numero_interior', 'colonia', 'cp', 'telefono', 'email', 'horarios', 'latitud', 'longitud'])
            ->map(fn (PuntoMapa $punto) => $this->mapPointForLookup($punto, $query, $userLocation));

        $establishmentMatches = Establecimiento::query()
            ->with(['tipo', 'domicilio', 'contacto', 'horarios'])
            ->where('estatus', true)
            ->where('is_visible', true)
            ->get(['id_establecimiento', 'nombre_est', 'descripcion', 'id_tipo'])
            ->map(fn (Establecimiento $establecimiento) => $this->mapEstablishmentForLookup($establecimiento, $query, $userLocation));

        return $pointMatches
            ->concat($establishmentMatches)
            ->filter(fn (array $item) => $item['score'] > 0)
            ->sortByDesc('score')
            ->first();
    }

    private function mapPointForLookup(PuntoMapa $punto, string $query, ?array $userLocation): array
    {
        $distance = $this->distanceInKm($userLocation, $punto->latitud, $punto->longitud);
        $searchText = collect([
            $punto->nombre_punto,
            $punto->categoria?->tipo,
            $punto->calle,
            $punto->colonia,
        ])->filter()->implode(' ');

        return [
            'source' => 'punto_mapa',
            'name' => $punto->nombre_punto,
            'category' => $punto->categoria?->tipo,
            'description' => Str::limit((string) $punto->descripcion, 180, ''),
            'address' => $this->formatPointAddress($punto),
            'hours' => $punto->horarios,
            'phone' => $punto->telefono,
            'latitud' => $punto->latitud !== null ? (float) $punto->latitud : null,
            'longitud' => $punto->longitud !== null ? (float) $punto->longitud : null,
            'distance_text' => $distance !== null ? number_format($distance, 1) . ' km' : null,
            'score' => $this->placeMatchScore($query, $searchText),
        ];
    }

    private function mapEstablishmentForLookup(Establecimiento $establecimiento, string $query, ?array $userLocation): array
    {
        $latitud = $establecimiento->domicilio?->latitud;
        $longitud = $establecimiento->domicilio?->longitud;
        $distance = $this->distanceInKm($userLocation, $latitud, $longitud);

        $searchText = collect([
            $establecimiento->nombre_est,
            $establecimiento->tipo?->nombre,
            $establecimiento->domicilio?->calle,
            $establecimiento->domicilio?->colonia,
        ])->filter()->implode(' ');

        return [
            'source' => 'establecimiento',
            'name' => $establecimiento->nombre_est,
            'category' => $establecimiento->tipo?->nombre,
            'description' => Str::limit((string) $establecimiento->descripcion, 180, ''),
            'address' => $this->formatAddress($establecimiento),
            'hours' => $this->todaySchedule($establecimiento),
            'phone' => $establecimiento->contacto?->telefono,
            'latitud' => $latitud !== null ? (float) $latitud : null,
            'longitud' => $longitud !== null ? (float) $longitud : null,
            'distance_text' => $distance !== null ? number_format($distance, 1) . ' km' : null,
            'score' => $this->placeMatchScore($query, $searchText),
        ];
    }

    private function placeMatchScore(string $query, string $searchText): int
    {
        $normalizedSearchText = $this->normalizePlaceText($searchText);

        if ($query === '' || $normalizedSearchText === '') {
            return 0;
        }

        if ($query === $normalizedSearchText) {
            return 1000;
        }

        if (Str::contains($normalizedSearchText, $query) || Str::contains($query, $normalizedSearchText)) {
            return 800;
        }

        $queryTokens = collect(explode(' ', $query))
            ->filter(fn (string $token) => strlen($token) >= 3 || ctype_digit($token))
            ->unique()
            ->values();
        $searchTokens = collect(explode(' ', $normalizedSearchText))
            ->filter(fn (string $token) => strlen($token) >= 3 || ctype_digit($token))
            ->unique()
            ->values();

        $matches = $queryTokens->filter(function (string $token) use ($searchTokens) {
            return $searchTokens->contains($token)
                || $searchTokens->contains(fn (string $candidate) => levenshtein($token, $candidate) <= 1);
        })->count();

        $requiredMatches = $queryTokens->count() >= 3 ? 2 : 1;

        return $matches >= $requiredMatches ? 400 + ($matches * 80) : 0;
    }

    private function normalizePlaceText(string $value): string
    {
        $normalized = Str::of($value)->ascii()->lower()->toString();
        $normalized = preg_replace('/\b(primero|primer)\b/u', '1', $normalized) ?? $normalized;
        $normalized = preg_replace('/\b(segundo)\b/u', '2', $normalized) ?? $normalized;
        $normalized = preg_replace('/\b(tercero|tercer)\b/u', '3', $normalized) ?? $normalized;
        $normalized = preg_replace('/[^a-z0-9\s]/u', ' ', $normalized) ?? $normalized;

        return trim(preg_replace('/\s+/', ' ', $normalized) ?? $normalized);
    }

    private function detectPointCategoryGroup(string $message): array
    {
        $text = Str::of($message)->ascii()->lower()->toString();

        return match (true) {
            Str::contains($text, ['mercado']) => [
                'label' => 'mercado',
                'categories' => ['mercado'],
            ],
            Str::contains($text, ['clinica']) => [
                'label' => 'clínica',
                'categories' => ['clinica'],
            ],
            Str::contains($text, ['hospital']) => [
                'label' => 'hospital',
                'categories' => ['hospital'],
            ],
            Str::contains($text, ['bombero', 'incendio']) => [
                'label' => 'servicio de bomberos',
                'categories' => ['bomberos'],
            ],
            Str::contains($text, ['policia', 'seguridad']) => [
                'label' => 'servicio de seguridad',
                'categories' => ['policia', 'seguridad ciudadana', 'seguridad publica'],
            ],
            Str::contains($text, ['proteccion civil', 'emergencia']) => [
                'label' => 'servicio de emergencia',
                'categories' => ['proteccion civil', 'bomberos', 'policia', 'seguridad ciudadana', 'seguridad publica', 'hospital', 'clinica'],
            ],
            default => [
                'label' => 'punto de interés',
                'categories' => ['zonas de interes'],
            ],
        };
    }

    private function formatPointAddress(PuntoMapa $punto): string
    {
        return collect([
            $punto->calle,
            $punto->numero_exterior ? '#' . $punto->numero_exterior : null,
            $punto->numero_interior ? 'Int. ' . $punto->numero_interior : null,
            $punto->colonia,
            $punto->cp,
        ])->filter()->implode(', ');
    }

    private function googleMapsDirectionsUrl(float $originLat, float $originLng, float $destinationLat, float $destinationLng): string
    {
        return sprintf(
            'https://www.google.com/maps/dir/?api=1&origin=%F,%F&destination=%F,%F&travelmode=driving',
            $originLat,
            $originLng,
            $destinationLat,
            $destinationLng
        );
    }

    private function googleMapsDestinationUrl(float $destinationLat, float $destinationLng): string
    {
        return sprintf(
            'https://www.google.com/maps/search/?api=1&query=%F,%F',
            $destinationLat,
            $destinationLng
        );
    }

    private function oldRutaGastronomicaResponse(): array
    {
        $establecimientos = Establecimiento::query()
            ->where('estatus', true)
            ->where('is_visible', true)
            ->where('is_route', true)
            ->orderBy('nombre_est')
            ->limit(3)
            ->get(['id_establecimiento', 'nombre_est']);

        $text = $establecimientos->isEmpty()
            ? 'La ruta gastronómica reúne comercios participantes para comer, explorar y sumar sellos de pasaporte.'
            : 'Para comer, puedes empezar por la ruta gastronómica. Algunos participantes: ' . $establecimientos->pluck('nombre_est')->implode(', ') . '.';

        return [
            'intent' => 'ruta_gastronomica',
            'text' => $text,
            'actions' => [
                ['label' => 'Ver mapa de ruta', 'to' => '/ruta-gastronomica'],
                ['label' => 'Ver pasaporte', 'to' => '/pasaporte'],
            ],
        ];
    }

    private function legacyContextRutaGastronomica(): array
    {
        return Establecimiento::query()
            ->with('domicilio')
            ->where('estatus', true)
            ->where('is_visible', true)
            ->where('is_route', true)
            ->orderBy('nombre_est')
            ->limit(6)
            ->get(['id_establecimiento', 'nombre_est', 'descripcion'])
            ->map(fn (Establecimiento $establecimiento) => [
                'nombre' => $establecimiento->nombre_est,
                'descripcion' => Str::limit((string) $establecimiento->descripcion, 150, ''),
                'colonia' => $establecimiento->domicilio?->colonia,
                'accion' => '/ruta-gastronomica',
            ])
            ->values()
            ->all();
    }

    private function contextPuntosMapa(?array $userLocation = null): array
    {
        return PuntoMapa::query()
            ->with('categoria:id,tipo')
            ->where('estatus', 1)
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->orderBy('nombre_punto')
            ->limit(80)
            ->get(['id', 'nombre_punto', 'descripcion', 'categoria_id', 'calle', 'numero_exterior', 'colonia', 'horarios', 'latitud', 'longitud'])
            ->map(function (PuntoMapa $punto) use ($userLocation) {
                $distance = $this->distanceInKm($userLocation, $punto->latitud, $punto->longitud);

                return [
                'nombre' => $punto->nombre_punto,
                'categoria' => $punto->categoria?->tipo,
                'descripcion' => Str::limit((string) $punto->descripcion, 150, ''),
                'direccion' => $this->formatPointAddress($punto),
                'colonia' => $punto->colonia,
                'horarios' => Str::limit((string) $punto->horarios, 90, ''),
                'latitud' => (float) $punto->latitud,
                'longitud' => (float) $punto->longitud,
                'distancia_km' => $distance,
                'distancia_texto' => $distance !== null ? number_format($distance, 1) . ' km' : null,
                'accion' => '/mapa',
                ];
            })
            ->sortBy(fn (array $item) => $item['distancia_km'] ?? 999999)
            ->take(12)
            ->values()
            ->all();
    }

    private function allowedActions(): array
    {
        return [
            ['label' => 'Mapa', 'to' => '/mapa'],
            ['label' => 'Eventos', 'to' => '/eventos'],
            ['label' => 'Cuponera', 'to' => '/cuponera'],
            ['label' => 'Mis cupones', 'to' => '/cupones'],
            ['label' => 'Pasaporte', 'to' => '/pasaporte'],
            ['label' => 'Ruta gastronomica', 'to' => '/ruta-gastronomica'],
            ['label' => 'Tianguis', 'to' => '/tianguis-de-hoy'],
            ['label' => 'Historia de Neza', 'to' => '/historia-de-nezahualcoyotl'],
            ['label' => 'Datos historicos', 'to' => '/historia-de-nezahualcoyotl/datos-historicos'],
            ['label' => 'Transporte', 'to' => '/rutas-de-transporte'],
            ['label' => 'Iniciar sesion', 'to' => '/auth/login'],
            ['label' => 'Crear cuenta', 'to' => '/auth/registro'],
            ['label' => 'Registrar comercio', 'to' => '/auth/comercios/registro'],
            ['label' => 'Entrar como comercio', 'to' => '/auth/comercios/login'],
        ];
    }

    private function sanitizeActions(mixed $actions, array $fallbackActions): array
    {
        $allowed = collect($this->allowedActions())->keyBy('to');

        $clean = collect(is_array($actions) ? $actions : [])
            ->filter(fn ($action) => is_array($action) && isset($action['to']) && $allowed->has($action['to']))
            ->map(function (array $action) use ($allowed) {
                $allowedAction = $allowed->get($action['to']);

                return [
                    'label' => Str::limit((string) ($action['label'] ?? $allowedAction['label']), 28, ''),
                    'to' => $allowedAction['to'],
                ];
            })
            ->unique('to')
            ->take(3)
            ->values()
            ->all();

        $fallbackExternalActions = collect($fallbackActions)
            ->filter(fn ($action) => is_array($action) && isset($action['url']))
            ->map(fn (array $action) => [
                'label' => Str::limit((string) ($action['label'] ?? 'Abrir'), 28, ''),
                'url' => (string) $action['url'],
                'external' => true,
            ])
            ->values()
            ->all();

        return collect($clean ?: $fallbackActions)
            ->concat($fallbackExternalActions)
            ->unique(fn (array $action) => $action['to'] ?? $action['url'] ?? $action['label'])
            ->take(3)
            ->values()
            ->all();
    }

    private function eventosResponse(): array
    {
        $eventos = EventoModel::query()
            ->where('estatus', 1)
            ->where(function ($query) {
                $query->whereNull('fecha')->orWhere('fecha', '>=', now()->toDateString());
            })
            ->orderBy('fecha')
            ->orderBy('hora')
            ->limit(3)
            ->get(['id', 'titulo', 'fecha', 'hora', 'colonia']);

        if ($eventos->isEmpty()) {
            return [
                'intent' => 'eventos',
                'text' => 'Por ahora no veo eventos activos próximos en el sistema. Puedes revisar la agenda para confirmar si ya publicaron nuevos.',
                'actions' => [
                    ['label' => 'Ver eventos', 'to' => '/eventos'],
                ],
            ];
        }

        $items = $eventos
            ->map(fn (EventoModel $evento) => trim(sprintf(
                '%s%s%s',
                $evento->titulo,
                $evento->fecha ? ' - ' . $evento->fecha : '',
                $evento->colonia ? ' en ' . $evento->colonia : ''
            )))
            ->implode('; ');

        return [
            'intent' => 'eventos',
            'text' => "Tengo estos eventos próximos: {$items}. Puedes abrir la agenda para ver detalles, ubicación y marcar interés.",
            'actions' => [
                ['label' => 'Ver eventos', 'to' => '/eventos'],
            ],
        ];
    }

    private function cuponesResponse(): array
    {
        $cupones = Cupon::query()
            ->with('establecimiento:id_establecimiento,nombre_est')
            ->where('is_active', true)
            ->where(fn ($query) => $query->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>=', now()))
            ->latest('id')
            ->limit(3)
            ->get();

        if ($cupones->isEmpty()) {
            return [
                'intent' => 'cupones',
                'text' => 'No encuentro cupones activos en este momento. La cuponera se actualiza conforme los comercios publican promociones.',
                'actions' => [
                    ['label' => 'Abrir cuponera', 'to' => '/cuponera'],
                ],
            ];
        }

        $items = $cupones
            ->map(fn (Cupon $cupon) => trim(sprintf(
                '%s%s',
                $cupon->title,
                $cupon->establecimiento?->nombre_est ? ' en ' . $cupon->establecimiento->nombre_est : ''
            )))
            ->implode('; ');

        return [
            'intent' => 'cupones',
            'text' => "Hay promociones disponibles como: {$items}. Entra a la cuponera para guardar o revisar condiciones.",
            'actions' => [
                ['label' => 'Abrir cuponera', 'to' => '/cuponera'],
                ['label' => 'Mis cupones', 'to' => '/cupones'],
            ],
        ];
    }

    private function pasaporteResponse(): array
    {
        return [
            'intent' => 'pasaporte',
            'text' => 'El pasaporte te permite juntar sellos en comercios participantes. Entra a Pasaporte, visita un establecimiento de la ruta y escanea su QR para registrar tu sello.',
            'actions' => [
                ['label' => 'Ver pasaporte', 'to' => '/pasaporte'],
                ['label' => 'Ruta gastronómica', 'to' => '/ruta-gastronomica'],
            ],
        ];
    }

    private function legacyRutaGastronomicaResponse(): array
    {
        $establecimientos = Establecimiento::query()
            ->where('estatus', true)
            ->where('is_visible', true)
            ->where('is_route', true)
            ->orderBy('nombre_est')
            ->limit(3)
            ->get(['id_establecimiento', 'nombre_est']);

        $text = $establecimientos->isEmpty()
            ? 'La ruta gastronómica reúne comercios participantes para comer, explorar y sumar sellos de pasaporte.'
            : 'Para comer, puedes empezar por la ruta gastronómica. Algunos participantes: ' . $establecimientos->pluck('nombre_est')->implode(', ') . '.';

        return [
            'intent' => 'ruta_gastronomica',
            'text' => $text,
            'actions' => [
                ['label' => 'Ver mapa de ruta', 'to' => '/ruta-gastronomica'],
                ['label' => 'Ver pasaporte', 'to' => '/pasaporte'],
            ],
        ];
    }

    private function mapaResponse(): array
    {
        $puntos = PuntoMapa::query()
            ->where('estatus', 1)
            ->orderBy('nombre_punto')
            ->limit(3)
            ->get(['nombre_punto', 'colonia']);

        $text = $puntos->isEmpty()
            ? 'Puedes explorar sitios de interés desde el mapa y abrir cada punto para ver ubicación y detalles.'
            : 'En el mapa puedes encontrar sitios como: ' . $puntos->map(fn (PuntoMapa $punto) => $punto->colonia ? "{$punto->nombre_punto} en {$punto->colonia}" : $punto->nombre_punto)->implode(', ') . '.';

        return [
            'intent' => 'mapa',
            'text' => $text,
            'actions' => [
                ['label' => 'Abrir mapa', 'to' => '/mapa'],
            ],
        ];
    }

    private function tianguisResponse(): array
    {
        return [
            'intent' => 'tianguis',
            'text' => 'Puedes consultar los tianguis disponibles para hoy con horario y ubicación desde la sección Tianguis de Hoy.',
            'actions' => [
                ['label' => 'Ver tianguis', 'to' => '/tianguis-de-hoy'],
            ],
        ];
    }

    private function historiaResponse(): array
    {
        return [
            'intent' => 'historia',
            'text' => 'En la sección de historia puedes recorrer datos históricos, relatos, imágenes y una línea del tiempo de Nezahualcóyotl.',
            'actions' => [
                ['label' => 'Historia de Neza', 'to' => '/historia-de-nezahualcoyotl'],
                ['label' => 'Datos históricos', 'to' => '/historia-de-nezahualcoyotl/datos-historicos'],
            ],
        ];
    }

    private function transporteResponse(): array
    {
        return [
            'intent' => 'transporte',
            'text' => 'La sección de rutas de transporte te ayuda a ubicar opciones de movilidad dentro de la ciudad.',
            'actions' => [
                ['label' => 'Ver transporte', 'to' => '/rutas-de-transporte'],
            ],
        ];
    }

    private function comerciosResponse(): array
    {
        return [
            'intent' => 'comercios',
            'text' => 'Si tienes un negocio, puedes preregistrarlo, iniciar sesión como comercio y administrar datos, horarios, galería, cupones y sellos de pasaporte.',
            'actions' => [
                ['label' => 'Registrar comercio', 'to' => '/auth/comercios/registro'],
                ['label' => 'Entrar como comercio', 'to' => '/auth/comercios/login'],
            ],
        ];
    }

    private function cuentaResponse(): array
    {
        return [
            'intent' => 'cuenta',
            'text' => 'Para usar funciones como cupones guardados y pasaporte, puedes iniciar sesión o crear una cuenta. Si olvidaste tu contraseña, también puedes recuperarla.',
            'actions' => [
                ['label' => 'Iniciar sesión', 'to' => '/auth/login'],
                ['label' => 'Crear cuenta', 'to' => '/auth/registro'],
            ],
        ];
    }

    private function generalResponse(string $message): array
    {
        return [
            'intent' => 'general',
            'text' => 'Soy Coyito, tu guía dentro de ExploraNeza. Puedo ayudarte con eventos, mapa, ruta gastronómica, tianguis, cupones, pasaporte, historia o registro de comercios.',
            'actions' => [
                ['label' => 'Mapa', 'to' => '/mapa'],
                ['label' => 'Eventos', 'to' => '/eventos'],
                ['label' => 'Cuponera', 'to' => '/cuponera'],
            ],
        ];
    }

    private function outOfScopeResponse(): array
    {
        return [
            'intent' => 'fuera_alcance',
            'text' => 'Soy Coyito y solo puedo ayudarte con ExploraNeza: eventos, mapa, lugares, ruta gastronomica, cupones, pasaporte, historia local, tianguis, transporte o registro de comercios. No puedo resolver tareas ni temas generales fuera de la app.',
            'actions' => [
                ['label' => 'Mapa', 'to' => '/mapa'],
                ['label' => 'Eventos', 'to' => '/eventos'],
                ['label' => 'Historia de Neza', 'to' => '/historia-de-nezahualcoyotl'],
            ],
        ];
    }
}
