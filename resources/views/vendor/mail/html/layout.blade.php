<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <style>
        :root {
            --primary: #0b2e59;
            --secondary: #1a5a8d;
            --accent: #ffb300;
            --bg: #f4f6f9;
            --text: #333333;
            --muted: #718096;
        }

        body {
            font-family: 'Segoe UI', 'Roboto', Arial, sans-serif;
            background-color: var(--bg);
            margin: 0;
            padding: 0;
            color: var(--text);
        }

        .email-wrapper {
            width: 100%;
            padding: 20px;
            background: var(--bg);
        }

        .email-container {
            max-width: 650px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
        }

        .header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            text-align: center;
            padding: 40px 20px 25px 20px;
            color: white;
        }

        .header img {
            max-width: 150px;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 26px;
            font-weight: 700;
            margin: 0;
        }

        .banner {
            background: var(--accent);
            color: white;
            text-align: center;
            padding: 10px;
            font-weight: bold;
            font-size: 14px;
            letter-spacing: 1px;
        }

        .content {
            padding: 30px;
            font-size: 16px;
            line-height: 1.7;
        }

        .content h2 {
            color: var(--secondary);
            margin-top: 0;
        }

        .button {
            display: inline-block;
            background: linear-gradient(to right, var(--secondary), var(--primary));
            color: white !important;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 6px;
            font-weight: bold;
            margin: 25px 0;
            box-shadow: 0 4px 12px rgba(26, 90, 141, 0.3);
            transition: all 0.3s ease;
        }

        .button:hover {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            transform: translateY(-2px);
        }

        .footer {
            background: var(--bg);
            padding: 15px 20px;
            text-align: center;
            font-size: 13px;
            color: var(--muted);
            border-top: 1px solid #e5e5e5;
        }

        .footer p {
            margin: 5px 0;
        }

        .signature {
            border-top: 1px solid #eee;
            margin-top: 25px;
            padding-top: 15px;
            font-size: 14px;
            color: var(--muted);
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- ENCABEZADO -->
            <div class="header">
                <img src="{{ asset('Imagen/Posface_logo.jpeg') }}" alt="POSFACE Logo">
                <h1>Sistema de Gestión Académica</h1>
            </div>

            <!-- BANNER -->
            <div class="banner">
                🔔 NOTIFICACIÓN AUTOMÁTICA - POSFACE
            </div>

            <!-- CONTENIDO DINÁMICO -->
            <div class="content">
                {!! $slot !!}
            </div>

            <!-- FOOTER -->
            <div class="footer">
                <p>&copy; {{ date('Y') }} POSFACE - Universidad Nacional Autónoma de Honduras</p>
                <p>Formamos profesionales con valores y visión gerencial para el desarrollo económico del país.</p>
            </div>
        </div>
    </div>
</body>
</html>
