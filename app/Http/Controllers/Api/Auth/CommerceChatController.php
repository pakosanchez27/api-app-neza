<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CommerceChatController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'messages' => ['required', 'array', 'min:1'],
            'messages.*.role' => ['nullable', 'string'],
            'messages.*.content' => ['required', 'string', 'max:2000'],
        ]);

        $user = $request->user()->load([
            'establecimientos.tipo',
            'establecimientos.contacto',
            'establecimientos.domicilio',
            'establecimientos.horarios',
            'establecimientos.amenidades',
            'establecimientos.documentos.tipoDocumento',
            'establecimientos.cupones.usuariosCupones',
        ]);
        $establishment = $user->establecimientos->first();
        $message = $this->lastUserMessage($validated['messages']);
        $intent = $this->detectIntent($message);
        $fallback = $this->responseForIntent($intent, $establishment);

        return response()->json($this->aiResponse(
            $validated['messages'],
            $intent,
            $fallback,
            $user,
            $establishment
        ));
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
            Str::contains($text, ['horario', 'abrir', 'cerrar', 'dias', 'hora']) => 'horarios',
            Str::contains($text, ['cupon', 'descuento', 'promo', 'promocion', 'redimir', 'escanear']) => 'cupones',
            Str::contains($text, ['pasaporte', 'sellar', 'sello', 'qr', 'ruta gastronomica']) => 'pasaporte',
            Str::contains($text, ['foto', 'galeria', 'imagen', 'menu', 'carta']) => 'galeria',
            Str::contains($text, ['negocio', 'perfil', 'direccion', 'ubicacion', 'amenidad', 'logo', 'descripcion']) => 'negocio',
            Str::contains($text, ['visible', 'visibilidad', 'publicar', 'ocultar', 'aprobado', 'activo']) => 'visibilidad',
            Str::contains($text, ['correo', 'telefono', 'contrasena', 'password', 'cuenta', 'configuracion']) => 'configuracion',
            Str::contains($text, ['estado', 'completo', 'falta', 'pendiente', 'recomendacion', 'mejorar']) => 'diagnostico',
            default => 'general',
        };
    }

    private function responseForIntent(string $intent, mixed $establishment): array
    {
        return match ($intent) {
            'horarios' => [
                'text' => $this->hoursResponseText($establishment),
                'actions' => [['label' => 'Ir a Horarios', 'to' => '/admin/comercio/horarios']],
            ],
            'cupones' => [
                'text' => $this->couponsResponseText($establishment),
                'actions' => [
                    ['label' => 'Gestionar cupones', 'to' => '/admin/comercio/gestion-cupones'],
                    ['label' => 'Escanear cupon', 'to' => '/admin/comercio/escanear-cupon'],
                ],
            ],
            'pasaporte' => [
                'text' => $establishment?->is_route
                    ? 'Tu comercio esta habilitado para la ruta. En Sellar Pasaporte puedes mostrar el QR dinamico y consultar actividad de sellos.'
                    : 'Tu comercio no esta marcado como parte de la ruta gastronomica, por eso no aparece Sellar Pasaporte. Si debe pertenecer a la ruta, solicita la revision con administracion.',
                'actions' => $establishment?->is_route
                    ? [['label' => 'Sellar pasaporte', 'to' => '/admin/comercio/sellar-pasaporte']]
                    : [['label' => 'Ver negocio', 'to' => '/admin/comercio/negocio']],
            ],
            'galeria' => [
                'text' => $this->galleryResponseText($establishment),
                'actions' => [['label' => 'Ir a Galeria', 'to' => '/admin/comercio/galeria']],
            ],
            'negocio' => [
                'text' => $this->businessResponseText($establishment),
                'actions' => [['label' => 'Editar negocio', 'to' => '/admin/comercio/negocio']],
            ],
            'visibilidad' => [
                'text' => $this->visibilityResponseText($establishment),
                'actions' => [['label' => 'Abrir configuracion', 'to' => '/admin/comercio/configuracion']],
            ],
            'configuracion' => [
                'text' => 'Para cambiar correo, telefono, contrasena o visibilidad, entra a Configuracion. Para datos publicos del negocio, usa Mi Negocio.',
                'actions' => [['label' => 'Ir a Configuracion', 'to' => '/admin/comercio/configuracion']],
            ],
            'diagnostico' => [
                'text' => $this->diagnosisResponseText($establishment),
                'actions' => $this->diagnosisActions($establishment),
            ],
            default => [
                'text' => $establishment
                    ? sprintf('Estoy viendo tu panel de %s. Puedo ayudarte con horarios, datos del negocio, galeria, cupones, pasaporte, visibilidad y configuracion sin usar informacion de otros establecimientos.', $establishment->nombre_est)
                    : 'Puedo ayudarte con el panel de comercio, pero no encontre un establecimiento asociado a esta cuenta.',
                'actions' => [
                    ['label' => 'Mi Negocio', 'to' => '/admin/comercio/negocio'],
                    ['label' => 'Horarios', 'to' => '/admin/comercio/horarios'],
                ],
            ],
        };
    }

    private function aiResponse(array $messages, string $intent, array $fallback, mixed $user, mixed $establishment): array
    {
        $apiKey = config('services.openai.api_key');

        if (! $apiKey) {
            return $this->withCoyitoPersonality($fallback) + ['intent' => $intent, 'engine' => 'rules'];
        }

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->timeout(config('services.openai.timeout', 20))
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => config('services.openai.model', 'gpt-4.1-mini'),
                    'temperature' => 0.25,
                    'max_tokens' => 520,
                    'response_format' => ['type' => 'json_object'],
                    'messages' => [
                        ['role' => 'system', 'content' => $this->systemPrompt()],
                        [
                            'role' => 'user',
                            'content' => json_encode([
                                'conversation' => $this->compactMessages($messages),
                                'detected_intent' => $intent,
                                'commerce_context' => $this->commerceContext($user, $establishment),
                                'fallback_response' => $fallback,
                                'allowed_actions' => $this->allowedActions($establishment),
                            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                        ],
                    ],
                ]);

            if (! $response->successful()) {
                return $this->withCoyitoPersonality($fallback) + ['intent' => $intent, 'engine' => 'rules'];
            }

            $content = data_get($response->json(), 'choices.0.message.content');
            $decoded = is_string($content) ? json_decode($content, true) : null;

            if (! is_array($decoded) || empty($decoded['text'])) {
                return $this->withCoyitoPersonality($fallback) + ['intent' => $intent, 'engine' => 'rules'];
            }

            return [
                'intent' => $intent,
                'text' => Str::limit((string) $decoded['text'], 900, ''),
                'actions' => $this->sanitizeActions($decoded['actions'] ?? [], $fallback['actions'] ?? [], $establishment),
                'engine' => 'openai',
            ];
        } catch (\Throwable $exception) {
            logger()->warning('Commerce chat OpenAI exception.', [
                'message' => $exception->getMessage(),
            ]);

            return $this->withCoyitoPersonality($fallback) + ['intent' => $intent, 'engine' => 'rules'];
        }
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
Eres Coyito, el asistente oficial de ExploraNeza, pero en modo panel de comercios.

Reglas:
- Responde en espanol de Mexico, alegre, servicial, cero acartonado y con vibra chilanga/CDMX.
- Usa jerga ligera como "va que va", "va", "chido", "de volada", "por aca", "te late", sin exagerar ni sonar burlon.
- Usa emojis utiles y alegres en cada respuesta, especialmente al inicio y al final.
- Tu audiencia son administradores de comercios, no visitantes, asi que conserva claridad operativa.
- Ayuda con el uso del panel: negocio, ubicacion, horarios, galeria, menu, cupones, redenciones, pasaporte, visibilidad y configuracion.
- Usa solo el contexto proporcionado. No inventes permisos, ventas, metricas, aprobaciones ni membresias.
- Solo puedes hablar del establecimiento autenticado en commerce_context.establishment.
- No reveles, compares ni infieras informacion de otros establecimientos, otros usuarios o datos globales de la plataforma.
- Si te preguntan por otro establecimiento, responde que solo puedes ayudar con el comercio de esta cuenta.
- No menciones documentos privados, rutas internas de archivos, tokens, IDs internos ni datos sensibles.
- Si el comercio no pertenece a ruta, no indiques que puede sellar pasaporte.
- Responde en maximo 2 parrafos cortos.
- Devuelve botones usando solo las acciones permitidas.

Devuelve exclusivamente JSON valido:
{
  "text": "respuesta para el comercio",
  "actions": [{"label": "texto del boton", "to": "/ruta"}]
}
PROMPT;
    }

    private function withCoyitoPersonality(array $response): array
    {
        $text = trim((string) ($response['text'] ?? ''));

        if ($text !== '' && ! Str::contains($text, ['😄', '✨', '📍', '🙌', '🧾', '🏪'])) {
            $response['text'] = "¡Va que va! 😄✨ {$text} 🏪🙌";
        }

        return $response;
    }

    private function commerceContext(mixed $user, mixed $establishment): array
    {
        return [
            'commerce_user' => [
                'name' => $user?->name,
                'email' => $user?->email,
                'temporary_password' => (bool) ($user?->is_password_templ ?? false),
            ],
            'establishment' => $establishment ? [
                'name' => $establishment->nombre_est,
                'business_name' => $establishment->razon_social,
                'type' => $establishment->tipo?->nombre,
                'description' => $establishment->descripcion,
                'capacity' => $establishment->aforo,
                'approved' => (bool) $establishment->estatus,
                'visible' => (bool) ($establishment->is_visible ?? true),
                'is_route' => (bool) $establishment->is_route,
                'has_logo' => ! empty($establishment->logo),
                'has_menu' => $this->hasMenu($establishment),
                'gallery_count' => $this->galleryCount($establishment),
                'contact' => $this->contactContext($establishment),
                'address' => $this->addressContext($establishment),
                'schedules' => $this->scheduleContext($establishment),
                'amenities' => $this->amenityContext($establishment),
                'coupons' => $this->couponContext($establishment),
                'profile_completion' => $this->completionContext($establishment),
            ] : null,
        ];
    }

    private function businessResponseText(mixed $establishment): string
    {
        if (! $establishment) {
            return 'No encontre un establecimiento asociado a esta cuenta. Revisa tu sesion o contacta a soporte.';
        }

        $missing = $this->missingProfileFields($establishment);

        if (! $missing) {
            return sprintf('%s se ve bastante completo: tiene datos base, contacto, ubicacion, horarios, logo, menu o galeria y amenidades. Si quieres pulirlo mas, revisa descripcion y fotos.', $establishment->nombre_est);
        }

        return sprintf('%s ya esta cargado en tu panel. Lo que conviene completar ahora es: %s.', $establishment->nombre_est, implode(', ', $missing));
    }

    private function hoursResponseText(mixed $establishment): string
    {
        $count = $establishment?->horarios?->count() ?? 0;

        if ($count === 0) {
            return 'Todavia no veo horarios registrados para tu comercio. Agregalos para que las personas sepan cuando pueden visitarte.';
        }

        return sprintf('Veo %d dias configurados en tus horarios. Si cambias turnos, descansos o cierres especiales, actualizalos desde Horarios para mantener tu perfil confiable.', $count);
    }

    private function galleryResponseText(mixed $establishment): string
    {
        $galleryCount = $this->galleryCount($establishment);
        $hasMenu = $this->hasMenu($establishment);

        if ($galleryCount === 0 && ! $hasMenu) {
            return 'Aun no veo galeria ni menu cargados. Sube fotos claras del lugar o productos y agrega tu menu para que el perfil luzca mas completo.';
        }

        return sprintf('Veo %d foto(s) en galeria y el menu %s cargado. Puedes mejorar el perfil renovando fotos de temporada o agregando imagenes mas claras.', $galleryCount, $hasMenu ? 'si esta' : 'aun no esta');
    }

    private function couponsResponseText(mixed $establishment): string
    {
        $coupons = $establishment?->cupones;
        $total = $coupons?->count() ?? 0;
        $active = $coupons?->where('is_active', true)->count() ?? 0;

        if ($total === 0) {
            return 'No veo cupones creados para tu comercio. Puedes crear uno con vigencia, stock y condiciones desde Gestion de Cupones.';
        }

        return sprintf('Tu comercio tiene %d cupon(es), de los cuales %d estan activos. Para validar codigos usa Escanear, y para ajustar vigencia o stock entra a Gestion de Cupones.', $total, $active);
    }

    private function visibilityResponseText(mixed $establishment): string
    {
        if (! $establishment) {
            return 'No encontre el establecimiento de esta cuenta para revisar visibilidad.';
        }

        $visible = (bool) ($establishment->is_visible ?? true);
        $approved = (bool) $establishment->estatus;

        return sprintf(
            'Tu comercio esta %s y su estado de aprobacion es %s. Puedes cambiar la visibilidad desde Configuracion.',
            $visible ? 'visible' : 'oculto',
            $approved ? 'aprobado' : 'pendiente'
        );
    }

    private function diagnosisResponseText(mixed $establishment): string
    {
        if (! $establishment) {
            return 'No encontre un establecimiento asociado a esta cuenta.';
        }

        $completion = $this->completionContext($establishment);

        if (! $completion['missing']) {
            return sprintf('%s esta muy bien armado. Tienes lo principal completo; ahora conviene mantener fotos, horarios y cupones actualizados.', $establishment->nombre_est);
        }

        return sprintf(
            '%s va al %d%% de perfil completo. Te recomiendo atender primero: %s.',
            $establishment->nombre_est,
            $completion['percent'],
            implode(', ', array_slice($completion['missing'], 0, 3))
        );
    }

    private function diagnosisActions(mixed $establishment): array
    {
        $missing = $this->missingProfileFields($establishment);

        if (in_array('horarios', $missing, true)) {
            return [['label' => 'Completar horarios', 'to' => '/admin/comercio/horarios']];
        }

        if (in_array('galeria o menu', $missing, true)) {
            return [['label' => 'Subir galeria', 'to' => '/admin/comercio/galeria']];
        }

        return [['label' => 'Editar negocio', 'to' => '/admin/comercio/negocio']];
    }

    private function contactContext(mixed $establishment): array
    {
        $contact = $establishment?->contacto;

        return [
            'has_contact' => (bool) $contact,
            'phone' => $contact?->telefono,
            'email' => $contact?->correo,
            'facebook' => $contact?->facebook,
            'instagram' => $contact?->instagram,
            'tiktok' => $contact?->tiktok,
        ];
    }

    private function addressContext(mixed $establishment): array
    {
        $address = $establishment?->domicilio;

        return [
            'has_address' => (bool) $address,
            'street' => $address?->calle,
            'exterior_number' => $address?->num_ext,
            'neighborhood' => $address?->colonia,
            'locality' => $address?->localidad,
            'postal_code' => $address?->cp,
            'market' => $address?->referencias,
            'has_coordinates' => (bool) ($address?->latitud && $address?->longitud),
        ];
    }

    private function scheduleContext(mixed $establishment): array
    {
        $dayNames = [
            1 => 'lunes',
            2 => 'martes',
            3 => 'miercoles',
            4 => 'jueves',
            5 => 'viernes',
            6 => 'sabado',
            7 => 'domingo',
        ];

        return ($establishment?->horarios ?? collect())
            ->sortBy('dia_semana')
            ->map(fn ($schedule) => [
                'day' => $dayNames[(int) $schedule->dia_semana] ?? (string) $schedule->dia_semana,
                'closed' => (bool) $schedule->cerrado,
                'open' => $schedule->hora_apertura ? substr((string) $schedule->hora_apertura, 0, 5) : null,
                'close' => $schedule->hora_cierra ? substr((string) $schedule->hora_cierra, 0, 5) : null,
            ])
            ->values()
            ->all();
    }

    private function amenityContext(mixed $establishment): array
    {
        return ($establishment?->amenidades ?? collect())
            ->pluck('nombre')
            ->filter()
            ->values()
            ->all();
    }

    private function couponContext(mixed $establishment): array
    {
        $coupons = $establishment?->cupones ?? collect();

        return [
            'total' => $coupons->count(),
            'active' => $coupons->where('is_active', true)->count(),
            'expired' => $coupons->filter(fn ($coupon) => $coupon->expires_at && $coupon->expires_at->isPast())->count(),
            'redemptions' => $coupons->sum(fn ($coupon) => $coupon->usuariosCupones?->count() ?? 0),
            'sample' => $coupons
                ->take(5)
                ->map(fn ($coupon) => [
                    'title' => $coupon->title,
                    'active' => (bool) $coupon->is_active,
                    'stock' => $coupon->stock,
                    'expires_at' => $coupon->expires_at?->toDateString(),
                ])
                ->values()
                ->all(),
        ];
    }

    private function completionContext(mixed $establishment): array
    {
        $missing = $this->missingProfileFields($establishment);
        $total = 8;
        $complete = max(0, $total - count($missing));

        return [
            'percent' => (int) round(($complete / $total) * 100),
            'missing' => $missing,
        ];
    }

    private function missingProfileFields(mixed $establishment): array
    {
        if (! $establishment) {
            return ['establecimiento'];
        }

        $missing = [];

        if (! $establishment->nombre_est || ! $establishment->tipo || ! $establishment->descripcion) {
            $missing[] = 'datos del negocio';
        }

        if (! $establishment->domicilio || ! $establishment->domicilio->latitud || ! $establishment->domicilio->longitud) {
            $missing[] = 'ubicacion';
        }

        if (! $establishment->contacto || (! $establishment->contacto->telefono && ! $establishment->contacto->correo)) {
            $missing[] = 'contacto';
        }

        if (($establishment->horarios?->count() ?? 0) === 0) {
            $missing[] = 'horarios';
        }

        if (! $establishment->logo) {
            $missing[] = 'logo';
        }

        if (! $this->hasMenu($establishment) && $this->galleryCount($establishment) === 0) {
            $missing[] = 'galeria o menu';
        }

        if (($establishment->amenidades?->count() ?? 0) === 0) {
            $missing[] = 'amenidades';
        }

        if (($establishment->cupones?->count() ?? 0) === 0) {
            $missing[] = 'cupones';
        }

        return $missing;
    }

    private function hasMenu(mixed $establishment): bool
    {
        if (! $establishment) {
            return false;
        }

        if (! empty($establishment->menu)) {
            return true;
        }

        return ($establishment->documentos ?? collect())->contains(function ($document) {
            $type = Str::of((string) $document->tipoDocumento?->nombre)->ascii()->lower()->toString();

            return Str::contains($type, ['menu', 'carta']);
        });
    }

    private function galleryCount(mixed $establishment): int
    {
        return ($establishment?->documentos ?? collect())->filter(function ($document) {
            $type = Str::of((string) $document->tipoDocumento?->nombre)->ascii()->lower()->toString();

            return Str::contains($type, ['galeria', 'foto_establecimiento']);
        })->count();
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

    private function allowedActions(mixed $establishment): array
    {
        $actions = [
            '/admin/comercio',
            '/admin/comercio/negocio',
            '/admin/comercio/horarios',
            '/admin/comercio/galeria',
            '/admin/comercio/configuracion',
            '/admin/comercio/gestion-cupones',
            '/admin/comercio/escanear-cupon',
        ];

        if ($establishment?->is_route) {
            $actions[] = '/admin/comercio/sellar-pasaporte';
        }

        return $actions;
    }

    private function sanitizeActions(array $actions, array $fallbackActions, mixed $establishment): array
    {
        $allowed = $this->allowedActions($establishment);
        $clean = collect($actions)
            ->filter(fn ($action) => is_array($action) && in_array($action['to'] ?? '', $allowed, true))
            ->map(fn ($action) => [
                'label' => Str::limit((string) ($action['label'] ?? 'Abrir'), 40, ''),
                'to' => $action['to'],
            ])
            ->values()
            ->take(3)
            ->all();

        return $clean ?: $fallbackActions;
    }
}
