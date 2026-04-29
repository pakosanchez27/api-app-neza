<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Activa tu cuenta</title>
</head>
<body style="margin:0;padding:24px;background:#f6efe8;font-family:Arial,sans-serif;color:#23171c;">
    <div style="max-width:640px;margin:0 auto;background:#ffffff;border-radius:18px;padding:32px;border:1px solid #eadfd2;">
        <p style="margin:0 0 8px;font-size:12px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:#7b5b63;">
            NezaGo
        </p>
        <h1 style="margin:0 0 16px;font-size:28px;line-height:1.1;">
            Activa tu cuenta
        </h1>
        <p style="margin:0 0 16px;font-size:15px;line-height:1.7;">
            Hola {{ $user->nombre_p }}, gracias por registrarte. Para activar tu cuenta y poder iniciar sesion, haz clic en el siguiente boton.
        </p>
        <p style="margin:24px 0;">
            <a
                href="{{ $activationUrl }}"
                style="display:inline-block;background:#10312b;color:#ffffff;text-decoration:none;padding:14px 22px;border-radius:999px;font-weight:700;"
            >
                Activar cuenta
            </a>
        </p>
        <p style="margin:0 0 8px;font-size:14px;line-height:1.7;">
            Si el boton no funciona, copia y pega este enlace en tu navegador:
        </p>
        <p style="margin:0;font-size:14px;line-height:1.7;word-break:break-all;color:#7f173c;">
            {{ $activationUrl }}
        </p>
    </div>
</body>
</html>
