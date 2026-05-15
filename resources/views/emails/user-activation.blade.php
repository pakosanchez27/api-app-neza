<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Activa tu cuenta</title>
</head>
<body style="margin:0;padding:24px;background:#f6efe8;font-family:Arial,sans-serif;color:#23171c;">
    <div style="max-width:640px;margin:0 auto;background:#ffffff;border-radius:18px;padding:32px;border:1px solid #eadfd2;">
        <p style="margin:0 0 8px;font-size:12px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:#7b5b63;">
            ExploraNeza
        </p>
        <h1 style="margin:0 0 16px;font-size:28px;line-height:1.1;">
            Activa tu cuenta
        </h1>
        <p style="margin:0 0 16px;font-size:15px;line-height:1.7;">
            Hola {{ $user->nombre_p }}, gracias por registrarte. Para activar tu cuenta y poder iniciar sesion, escribe este codigo de 6 digitos en la pantalla de verificacion.
        </p>
        <div style="margin:24px 0;padding:18px 20px;border-radius:18px;background:#f6efe8;border:1px solid #eadfd2;text-align:center;">
            <p style="margin:0 0 8px;font-size:12px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:#7b5b63;">
                Codigo de activacion
            </p>
            <p style="margin:0;font-size:34px;line-height:1;font-weight:700;letter-spacing:0.32em;color:#10312b;">
                {{ $activationCode }}
            </p>
        </div>
        <p style="margin:0 0 8px;font-size:14px;line-height:1.7;">
            Si no solicitaste esta cuenta, puedes ignorar este mensaje.
        </p>
        <p style="margin:0;font-size:14px;line-height:1.7;color:#7f173c;">
            Este codigo se usa una sola vez para confirmar tu correo.
        </p>
    </div>
</body>
</html>
