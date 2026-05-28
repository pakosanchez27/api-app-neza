<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estado de tu cuenta</title>
</head>
<body style="margin:0;padding:24px;background:#f6efe8;font-family:Arial,sans-serif;color:#23171c;">
    <div style="max-width:640px;margin:0 auto;background:#ffffff;border-radius:18px;padding:32px;border:1px solid #eadfd2;">
        <p style="margin:0 0 8px;font-size:12px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:#7b5b63;">
            ExploraNeza
        </p>
        <h1 style="margin:0 0 16px;font-size:28px;line-height:1.1;">
            Tu cuenta sera desactivada
        </h1>
        <p style="margin:0 0 16px;font-size:15px;line-height:1.7;">
            Hola {{ $user->nombre_p ?: ($user->name ?: 'usuario') }}, te informamos que un administrador ha marcado tu cuenta para desactivacion.
        </p>
        <div style="margin:24px 0;padding:18px 20px;border-radius:18px;background:#f6efe8;border:1px solid #eadfd2;">
            <p style="margin:0 0 8px;font-size:12px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:#7b5b63;">
                Motivo
            </p>
            <p style="margin:0;font-size:15px;line-height:1.8;color:#10312b;">
                {{ $reason }}
            </p>
        </div>
        <p style="margin:0 0 8px;font-size:14px;line-height:1.7;">
            Si consideras que esto es un error, ponte en contacto con el equipo de soporte o con la administracion correspondiente.
        </p>
        <p style="margin:0;font-size:14px;line-height:1.7;color:#7f173c;">
            Gracias por formar parte de ExploraNeza.
        </p>
    </div>
</body>
</html>
