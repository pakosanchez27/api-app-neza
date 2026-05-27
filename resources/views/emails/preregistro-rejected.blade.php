<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExploraNeza</title>
</head>
<body style="margin:0;background:#f9f9f9;color:#1a1c1c;font-family:Arial,sans-serif;line-height:1.6;padding:24px;">
<div style="max-width:680px;margin:0 auto;background:#ffffff;border:1px solid #d9c0c5;border-radius:14px;box-shadow:0 8px 22px rgba(38,18,28,.08);overflow:hidden;">
    <div style="background:linear-gradient(135deg,#611232,#4d0e28);color:#ffffff;padding:28px 24px;text-align:center;">
        <div style="display:inline-block;background:#ffd175;color:#3a2b00;font-weight:700;font-size:12px;text-transform:uppercase;letter-spacing:.08em;padding:6px 10px;border-radius:999px;margin-bottom:12px;">
            ExploraNeza
        </div>
        <h1 style="margin:0;font-size:28px;line-height:1.2;font-weight:800;">Gracias por tu interes en formar parte de ExploraNeza</h1>
        <p style="margin:10px 0 0;color:#f6e8ec;font-size:15px;">Agradecemos el tiempo y la informacion compartida durante tu registro.</p>
    </div>

    <div style="padding:26px 24px;">
        <p style="margin:0 0 16px;font-size:16px;color:#1a1c1c;">
            @if (!empty($nombreSolicitante))
                Hola, {{ $nombreSolicitante }}.
            @else
                Hola.
            @endif
        </p>

        <div style="background:#f3f3f3;border:1px solid #d9c0c5;border-left:4px solid #795801;border-radius:12px;padding:14px 14px;margin:14px 0 18px;color:#544246;font-size:15px;">
            Hemos concluido la revision de la informacion compartida para <strong>{{ $nombreEstablecimiento ?? 'tu establecimiento' }}</strong> como parte del proceso de integracion a ExploraNeza.
        </div>

        <p style="margin:0 0 16px;font-size:16px;color:#1a1c1c;">
            Valoramos tu interes en pertenecer a ExploraNeza y agradecemos la disposicion para participar en esta iniciativa.
        </p>

        @if (!empty($motivo))
            <div style="background:#fff7f7;border:1px solid #f0c7cf;border-left:4px solid #b00020;border-radius:12px;padding:14px 14px;margin:14px 0 18px;color:#544246;font-size:15px;">
                <strong>Comentarios de seguimiento:</strong><br>
                {!! nl2br(e($motivo)) !!}
            </div>
        @endif

        <p style="margin:0 0 16px;font-size:16px;color:#1a1c1c;">
            Te invitamos a mantenerte atento a futuras oportunidades para integrarte a ExploraNeza. Sera un gusto contar nuevamente con tu participacion.
        </p>

        <p style="margin-top:16px;font-size:12px;color:#b00020;text-align:center;font-weight:700;">
            Si no realizaste este registro, puedes ignorar este mensaje, favor de no contestar este mensaje.
        </p>
    </div>

    <div style="border-top:1px solid #d9c0c5;background:#ffffff;text-align:center;padding:16px 20px 20px;color:#b00020;font-size:12px;font-weight:700;">
        &copy; 2026 H. Ayuntamiento de Nezahualcoyotl. Todos los derechos reservados.
    </div>
</div>
</body>
</html>
