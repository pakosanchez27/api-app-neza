<?php

$parseCsv = static fn (?string $value): array => array_values(array_filter(array_map(
    static fn (string $item): string => trim($item),
    explode(',', (string) $value)
)));

$defaultOrigins = [
    'http://localhost:5173',
    'http://localhost:5174',
    'https://exploraneza.digitalneza.com',
    'https://explora.digitalneza.com',
];

$configuredOrigins = $parseCsv(env('FRONTEND_URLS'));

if ($configuredOrigins === []) {
    $configuredOrigins = $parseCsv(env('FRONTEND_URL'));
}

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => array_values(array_unique(array_merge(
        $defaultOrigins,
        $configuredOrigins,
    ))),
    'allowed_origins_patterns' => $parseCsv(env('FRONTEND_URL_PATTERNS')),
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
