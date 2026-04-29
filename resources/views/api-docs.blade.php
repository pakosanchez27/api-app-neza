<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neza Turismo API Docs</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #f5f7fb;
            --panel: #ffffff;
            --panel-soft: #f9fbff;
            --border: #dbe2ea;
            --text: #17212b;
            --muted: #5c6977;
            --accent: #ef4444;
            --accent-soft: rgba(239, 68, 68, 0.12);
            --green: #0f766e;
            --amber: #b45309;
            --code: #0f172a;
            --shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
            --radius: 22px;
            --sidebar-width: 280px;
            --font-sans: "Instrument Sans", "Inter", "Segoe UI", sans-serif;
            --font-mono: "JetBrains Mono", "Fira Code", "Cascadia Code", monospace;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            font-family: var(--font-sans);
            background:
                radial-gradient(circle at top left, rgba(239, 68, 68, 0.08), transparent 24rem),
                radial-gradient(circle at top right, rgba(15, 118, 110, 0.08), transparent 28rem),
                var(--bg);
            color: var(--text);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .layout {
            display: grid;
            grid-template-columns: var(--sidebar-width) minmax(0, 1fr);
            min-height: 100vh;
        }

        .sidebar {
            position: sticky;
            top: 0;
            align-self: start;
            height: 100vh;
            overflow-y: auto;
            padding: 28px 22px;
            border-right: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(16px);
        }

        .brand {
            display: grid;
            gap: 10px;
            margin-bottom: 28px;
        }

        .brand-badge {
            display: inline-flex;
            width: fit-content;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: var(--accent-soft);
            color: #991b1b;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .brand h1 {
            margin: 0;
            font-size: 24px;
            line-height: 1.1;
        }

        .brand p {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.7;
        }

        .nav-group {
            display: grid;
            gap: 8px;
            margin-top: 22px;
        }

        .nav-title {
            margin: 0 0 6px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .nav-link {
            display: block;
            padding: 10px 12px;
            border-radius: 14px;
            color: #314050;
            font-size: 14px;
            transition: background 0.18s ease, color 0.18s ease, transform 0.18s ease;
        }

        .nav-link:hover {
            background: #eef3f8;
            color: #111827;
            transform: translateX(2px);
        }

        .content {
            padding: 34px;
        }

        .hero {
            padding: 34px;
            border: 1px solid rgba(255, 255, 255, 0.72);
            border-radius: 30px;
            background:
                linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.92)),
                linear-gradient(120deg, rgba(239, 68, 68, 0.04), rgba(15, 118, 110, 0.04));
            box-shadow: var(--shadow);
        }

        .hero-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.3fr) minmax(300px, 0.8fr);
            gap: 24px;
            align-items: start;
        }

        .eyebrow {
            margin: 0 0 10px;
            color: var(--accent);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .hero h2 {
            margin: 0;
            font-size: clamp(34px, 4vw, 50px);
            line-height: 1.02;
            letter-spacing: -0.04em;
        }

        .hero p {
            margin: 18px 0 0;
            max-width: 70ch;
            color: var(--muted);
            font-size: 16px;
            line-height: 1.8;
        }

        .hero-card {
            padding: 22px;
            border: 1px solid var(--border);
            border-radius: 24px;
            background: var(--panel-soft);
        }

        .hero-card strong {
            display: block;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .hero-card ul {
            margin: 0;
            padding-left: 18px;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.9;
        }

        .section {
            margin-top: 28px;
        }

        .section-header {
            margin-bottom: 18px;
        }

        .section-header h3 {
            margin: 0;
            font-size: 28px;
            letter-spacing: -0.03em;
        }

        .section-header p {
            margin: 8px 0 0;
            color: var(--muted);
            line-height: 1.8;
        }

        .grid {
            display: grid;
            gap: 18px;
        }

        .card {
            padding: 24px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: var(--panel);
            box-shadow: var(--shadow);
        }

        .endpoint-head {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            margin-bottom: 14px;
        }

        .method {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 70px;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .method.get {
            background: rgba(15, 118, 110, 0.12);
            color: var(--green);
        }

        .method.post {
            background: rgba(180, 83, 9, 0.12);
            color: var(--amber);
        }

        .endpoint-path {
            font-family: var(--font-mono);
            font-size: 15px;
            word-break: break-word;
        }

        .endpoint-desc {
            margin: 0;
            color: var(--muted);
            line-height: 1.8;
        }

        .meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 16px 0 0;
        }

        .meta span {
            padding: 8px 11px;
            border-radius: 999px;
            background: #f2f6fa;
            color: #425266;
            font-size: 12px;
            font-weight: 700;
        }

        .doc-block {
            margin-top: 18px;
            padding-top: 18px;
            border-top: 1px solid var(--border);
        }

        .doc-block h4 {
            margin: 0 0 10px;
            font-size: 15px;
        }

        .doc-block p,
        .doc-block li {
            color: var(--muted);
            font-size: 14px;
            line-height: 1.8;
        }

        .doc-block ul {
            margin: 0;
            padding-left: 18px;
        }

        pre {
            margin: 0;
            padding: 18px;
            overflow-x: auto;
            border-radius: 18px;
            background: var(--code);
            color: #e5eef8;
            font-family: var(--font-mono);
            font-size: 13px;
            line-height: 1.7;
        }

        code {
            font-family: var(--font-mono);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            overflow: hidden;
            border-radius: 16px;
            border: 1px solid var(--border);
        }

        .table th,
        .table td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            text-align: left;
            vertical-align: top;
            font-size: 14px;
        }

        .table th {
            background: #f8fafc;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #475569;
        }

        .table tr:last-child td {
            border-bottom: 0;
        }

        .footer-note {
            margin-top: 28px;
            padding: 18px 20px;
            border: 1px dashed var(--border);
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.7);
            color: var(--muted);
            line-height: 1.8;
        }

        @media (max-width: 1120px) {
            .layout {
                grid-template-columns: 1fr;
            }

            .sidebar {
                position: relative;
                height: auto;
                border-right: 0;
                border-bottom: 1px solid var(--border);
            }

            .hero-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 720px) {
            .content {
                padding: 18px;
            }

            .sidebar {
                padding: 20px 18px;
            }

            .hero,
            .card {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
@php
    $catalogEndpoints = [
        [
            'id' => 'roles',
            'method' => 'GET',
            'path' => '/api/roles',
            'summary' => 'Devuelve el catálogo de roles disponibles.',
            'auth' => 'Pública',
            'response' => <<<'JSON'
[
  {
    "id_role": 1,
    "nombre": "AdminComercios"
  }
]
JSON,
        ],
        [
            'id' => 'tipos',
            'method' => 'GET',
            'path' => '/api/tipos',
            'summary' => 'Catálogo de tipos de establecimiento.',
            'auth' => 'Pública',
            'response' => <<<'JSON'
[
  {
    "id_tipo": 1,
    "nombre": "Restaurante"
  }
]
JSON,
        ],
        [
            'id' => 'amenidades',
            'method' => 'GET',
            'path' => '/api/amenidades',
            'summary' => 'Catálogo de amenidades que puede seleccionar un establecimiento.',
            'auth' => 'Pública',
            'response' => <<<'JSON'
[
  {
    "id_amenidades": 3,
    "nombre": "Wi-Fi",
    "descripcion": null
  }
]
JSON,
        ],
        [
            'id' => 'tipo-documentos',
            'method' => 'GET',
            'path' => '/api/tipo-documentos',
            'summary' => 'Catálogo de tipos documentales del sistema.',
            'auth' => 'Pública',
            'response' => <<<'JSON'
[
  {
    "id_tipo_documento": 1,
    "nombre": "ine"
  }
]
JSON,
        ],
    ];

    $commerceEndpoints = [
        [
            'id' => 'establecimientos-index',
            'method' => 'GET',
            'path' => '/api/establecimientos',
            'summary' => 'Lista los establecimientos con sus relaciones principales.',
            'auth' => 'Pública',
            'notes' => [
                'Incluye tipo, contacto, domicilio, horarios, amenidades y documentos.',
                'Se usa para consumo público o paneles que muestran fichas completas.',
            ],
        ],
        [
            'id' => 'establecimientos-show',
            'method' => 'GET',
            'path' => '/api/establecimientos/{id}',
            'summary' => 'Devuelve un establecimiento específico por identificador.',
            'auth' => 'Pública',
            'notes' => [
                'Responde 404 si el establecimiento no existe.',
                'También incluye la relación del usuario y su rol.',
            ],
        ],
    ];

    $authEndpoints = [
        [
            'id' => 'login',
            'method' => 'POST',
            'path' => '/api/auth/comercios/login',
            'summary' => 'Autentica al administrador de comercios y entrega token Sanctum.',
            'auth' => 'Pública',
            'request' => <<<'JSON'
{
  "email": "admin@comercio.mx",
  "password": "Secret123!"
}
JSON,
            'response' => <<<'JSON'
{
  "message": "Inicio de sesion exitoso.",
  "token": "1|sanctum-token",
  "token_type": "Bearer",
  "must_change_password": true,
  "user": {
    "id": 4,
    "name": "Comercio Demo",
    "email": "admin@comercio.mx",
    "role": "AdminComercios",
    "establecimientos": []
  }
}
JSON,
        ],
        [
            'id' => 'me',
            'method' => 'GET',
            'path' => '/api/auth/comercios/me',
            'summary' => 'Recupera el perfil autenticado con toda la información del comercio.',
            'auth' => 'Bearer',
        ],
        [
            'id' => 'logout',
            'method' => 'POST',
            'path' => '/api/auth/comercios/logout',
            'summary' => 'Cierra la sesión actual eliminando el token activo.',
            'auth' => 'Bearer',
        ],
    ];

    $phaseExample = <<<'JSON'
{
  "phase": 5,
  "finalize": false,
  "payload": {
    "horarios": {
      "lunes": { "closed": false, "open": "09:00", "close": "18:00" },
      "martes": { "closed": false, "open": "09:00", "close": "18:00" },
      "miercoles": { "closed": false, "open": "09:00", "close": "18:00" },
      "jueves": { "closed": false, "open": "09:00", "close": "18:00" },
      "viernes": { "closed": false, "open": "09:00", "close": "18:00" },
      "sabado": { "closed": true, "open": null, "close": null },
      "domingo": { "closed": true, "open": null, "close": null }
    }
  }
}
JSON;

    $phaseMatrix = [
        ['phase' => 1, 'title' => 'Cambio de contrasena', 'payload' => 'newPassword, confirmPassword'],
        ['phase' => 2, 'title' => 'Informacion general del negocio', 'payload' => 'nombreEstablecimiento, tipo, aforo, descripcionCorta, telefonoPrincipal, logo'],
        ['phase' => 3, 'title' => 'Domicilio', 'payload' => 'calle, colonia, numeroExterior, numeroInterior, localidad, codigoPostal, latitud, longitud'],
        ['phase' => 4, 'title' => 'Contacto publico', 'payload' => 'telefonoNegocio, correoNegocio, facebook, instagram, tiktok'],
        ['phase' => 5, 'title' => 'Horarios', 'payload' => 'horarios.{dia}.closed, open, close'],
        ['phase' => 6, 'title' => 'Amenidades', 'payload' => 'amenidades[]'],
        ['phase' => 7, 'title' => 'Menu y galeria', 'payload' => 'menu, galeria[], existingMenu, existingGalleryCount'],
    ];
@endphp
<div class="layout">
    <aside class="sidebar">
        <div class="brand">
            <span class="brand-badge">Neza Turismo API</span>
            <h1>Documentación interna</h1>
            <p>Referencia operativa de los endpoints disponibles en el backend Laravel del proyecto.</p>
        </div>

        <div class="nav-group">
            <p class="nav-title">Resumen</p>
            <a class="nav-link" href="#overview">Overview</a>
            <a class="nav-link" href="#auth-model">Autenticación</a>
        </div>

        <div class="nav-group">
            <p class="nav-title">Catálogos</p>
            @foreach ($catalogEndpoints as $endpoint)
                <a class="nav-link" href="#{{ $endpoint['id'] }}">{{ $endpoint['path'] }}</a>
            @endforeach
        </div>

        <div class="nav-group">
            <p class="nav-title">Establecimientos</p>
            @foreach ($commerceEndpoints as $endpoint)
                <a class="nav-link" href="#{{ $endpoint['id'] }}">{{ $endpoint['path'] }}</a>
            @endforeach
        </div>

        <div class="nav-group">
            <p class="nav-title">Auth Comercios</p>
            @foreach ($authEndpoints as $endpoint)
                <a class="nav-link" href="#{{ $endpoint['id'] }}">{{ $endpoint['path'] }}</a>
            @endforeach
            <a class="nav-link" href="#registro-establecimiento">/api/auth/comercios/registro-establecimiento</a>
        </div>
    </aside>

    <main class="content">
        <section class="hero" id="overview">
            <div class="hero-grid">
                <div>
                    <p class="eyebrow">API Reference</p>
                    <h2>Una vista de documentación pensada para operación real.</h2>
                    <p>
                        Esta página resume la API disponible actualmente en el backend Laravel. Está organizada por catálogos,
                        consulta pública, autenticación de comercios y registro por fases, que es el endpoint central del panel
                        administrativo.
                    </p>
                </div>
                <div class="hero-card">
                    <strong>Puntos clave</strong>
                    <ul>
                        <li>La autenticación de comercio usa tokens Bearer generados con Sanctum.</li>
                        <li>El endpoint de registro unifica actualización de negocio, domicilio, contacto, horarios, amenidades y media.</li>
                        <li>Las respuestas no autenticadas del API regresan JSON con código 401.</li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="section" id="auth-model">
            <div class="section-header">
                <h3>Modelo de autenticación</h3>
                <p>Para endpoints protegidos, envía el encabezado <code>Authorization: Bearer &lt;token&gt;</code>. El login invalida sesiones previas del mismo usuario.</p>
            </div>
            <div class="card">
                <div class="doc-block" style="margin-top:0;padding-top:0;border-top:0;">
                    <h4>Headers recomendados</h4>
                    <pre>Accept: application/json
Authorization: Bearer 1|sanctum-token</pre>
                </div>
                <div class="doc-block">
                    <h4>Códigos comunes</h4>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Status</th>
                            <th>Significado</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><code>200</code></td>
                            <td>Operación exitosa.</td>
                        </tr>
                        <tr>
                            <td><code>401</code></td>
                            <td>No autenticado o token inválido.</td>
                        </tr>
                        <tr>
                            <td><code>403</code></td>
                            <td>Cuenta inactiva o rol sin permiso.</td>
                        </tr>
                        <tr>
                            <td><code>422</code></td>
                            <td>Error de validación.</td>
                        </tr>
                        <tr>
                            <td><code>404</code></td>
                            <td>Recurso no encontrado.</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="section-header">
                <h3>Catálogos públicos</h3>
                <p>Endpoints de solo lectura para poblar selects, formularios y filtros del frontend.</p>
            </div>
            <div class="grid">
                @foreach ($catalogEndpoints as $endpoint)
                    <article class="card" id="{{ $endpoint['id'] }}">
                        <div class="endpoint-head">
                            <span class="method {{ strtolower($endpoint['method']) }}">{{ $endpoint['method'] }}</span>
                            <code class="endpoint-path">{{ $endpoint['path'] }}</code>
                        </div>
                        <p class="endpoint-desc">{{ $endpoint['summary'] }}</p>
                        <div class="meta">
                            <span>Acceso: {{ $endpoint['auth'] }}</span>
                        </div>
                        <div class="doc-block">
                            <h4>Respuesta ejemplo</h4>
                            <pre>{{ $endpoint['response'] }}</pre>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="section">
            <div class="section-header">
                <h3>Consulta pública de establecimientos</h3>
                <p>Endpoints destinados a mostrar fichas y listados de negocios publicados o disponibles en el sistema.</p>
            </div>
            <div class="grid">
                @foreach ($commerceEndpoints as $endpoint)
                    <article class="card" id="{{ $endpoint['id'] }}">
                        <div class="endpoint-head">
                            <span class="method {{ strtolower($endpoint['method']) }}">{{ $endpoint['method'] }}</span>
                            <code class="endpoint-path">{{ $endpoint['path'] }}</code>
                        </div>
                        <p class="endpoint-desc">{{ $endpoint['summary'] }}</p>
                        <div class="meta">
                            <span>Acceso: {{ $endpoint['auth'] }}</span>
                            <span>Incluye relaciones</span>
                        </div>
                        <div class="doc-block">
                            <h4>Notas</h4>
                            <ul>
                                @foreach ($endpoint['notes'] as $note)
                                    <li>{{ $note }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="section">
            <div class="section-header">
                <h3>Autenticación de comercios</h3>
                <p>Endpoints para login, recuperación de perfil autenticado y cierre de sesión.</p>
            </div>
            <div class="grid">
                @foreach ($authEndpoints as $endpoint)
                    <article class="card" id="{{ $endpoint['id'] }}">
                        <div class="endpoint-head">
                            <span class="method {{ strtolower($endpoint['method']) }}">{{ $endpoint['method'] }}</span>
                            <code class="endpoint-path">{{ $endpoint['path'] }}</code>
                        </div>
                        <p class="endpoint-desc">{{ $endpoint['summary'] }}</p>
                        <div class="meta">
                            <span>Acceso: {{ $endpoint['auth'] }}</span>
                        </div>
                        @isset($endpoint['request'])
                            <div class="doc-block">
                                <h4>Body ejemplo</h4>
                                <pre>{{ $endpoint['request'] }}</pre>
                            </div>
                        @endisset
                        @isset($endpoint['response'])
                            <div class="doc-block">
                                <h4>Respuesta ejemplo</h4>
                                <pre>{{ $endpoint['response'] }}</pre>
                            </div>
                        @endisset
                    </article>
                @endforeach
            </div>
        </section>

        <section class="section" id="registro-establecimiento">
            <div class="section-header">
                <h3>Registro de establecimiento por fases</h3>
                <p>Este es el endpoint operativo principal del panel. Se reutiliza para guardar cada bloque del registro y del administrador.</p>
            </div>
            <article class="card">
                <div class="endpoint-head">
                    <span class="method post">POST</span>
                    <code class="endpoint-path">/api/auth/comercios/registro-establecimiento</code>
                </div>
                <p class="endpoint-desc">
                    Actualiza la información del comercio según la fase enviada. Puede recibir archivos en <code>multipart/form-data</code>
                    y admite finalización del registro con <code>finalize=true</code>.
                </p>
                <div class="meta">
                    <span>Acceso: Bearer</span>
                    <span>Content-Type: multipart/form-data</span>
                    <span>Phases: 1 - 7</span>
                </div>

                <div class="doc-block">
                    <h4>Estructura general</h4>
                    <pre>{{ $phaseExample }}</pre>
                </div>

                <div class="doc-block">
                    <h4>Matriz de fases</h4>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Phase</th>
                            <th>Bloque</th>
                            <th>Campos principales</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($phaseMatrix as $row)
                            <tr>
                                <td><code>{{ $row['phase'] }}</code></td>
                                <td>{{ $row['title'] }}</td>
                                <td><code>{{ $row['payload'] }}</code></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="doc-block">
                    <h4>Archivos soportados</h4>
                    <ul>
                        <li><code>logo</code>: imagen, hasta 5 MB.</li>
                        <li><code>menu</code>: jpg, jpeg, png o pdf, hasta 10 MB.</li>
                        <li><code>galeria[]</code>: arreglo de imágenes, hasta 5 elementos.</li>
                    </ul>
                </div>

                <div class="doc-block">
                    <h4>Respuesta típica</h4>
                    <pre>{
  "message": "Fase guardada correctamente.",
  "phase": 5,
  "user": {
    "...": "perfil actualizado con establecimientos y relaciones"
  }
}</pre>
                </div>
            </article>
        </section>

        <div class="footer-note">
            Esta documentación es una vista interna montada directamente en Laravel. Si después quieres, el siguiente paso natural es
            convertirla en una documentación mantenible por configuración o generar un esquema OpenAPI para sincronizar frontend y backend.
        </div>
    </main>
</div>
</body>
</html>
