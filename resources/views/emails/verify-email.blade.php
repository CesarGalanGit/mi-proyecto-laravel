<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifica tu cuenta</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f4f5;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #18181b;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f4f4f5;
            padding-bottom: 40px;
        }
        .content {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            margin-top: 40px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .header {
            padding: 40px 0;
            text-align: center;
            background: linear-gradient(135deg, #18181b 0%, #3f3f46 100%);
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.025em;
        }
        .body {
            padding: 48px 40px;
            text-align: center;
        }
        .body h2 {
            margin-top: 0;
            color: #18181b;
            font-size: 22px;
            font-weight: 700;
        }
        .body p {
            color: #71717a;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 32px;
        }
        .button {
            display: inline-block;
            padding: 16px 32px;
            background-color: #18181b;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            transition: transform 0.2s ease;
        }
        .footer {
            text-align: center;
            padding: 24px;
            color: #a1a1aa;
            font-size: 13px;
        }
        .divider {
            height: 1px;
            background-color: #e4e4e7;
            margin: 32px 0;
        }
        .subtext {
            font-size: 12px;
            color: #d4d4d8;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="content">
            <div class="header">
                <h1>{{ config('app.name') }}</h1>
            </div>
            <div class="body">
                <h2>¡Hola, {{ $user->name }}!</h2>
                <p>Gracias por unirte a nosotros. Estamos emocionados de tenerte a bordo. Para activar todas las funciones de tu cuenta, solo tienes que confirmar que este es tu correo electrónico.</p>
                
                <a href="{{ $url }}" class="button">Verificar mi cuenta</a>
                
                <div class="divider"></div>
                
                <p style="margin-bottom: 0; font-size: 14px;">Si no has creado esta cuenta, puedes ignorar este correo con seguridad.</p>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.</p>
            <p class="subtext">Si el botón no funciona, copia y pega este enlace en tu navegador:<br>{{ $url }}</p>
        </div>
    </div>
</body>
</html>
