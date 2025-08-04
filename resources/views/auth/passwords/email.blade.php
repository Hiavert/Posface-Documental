<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar contraseña - POSFACE</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background: #f4f6f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .reset-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 40px 32px 32px 32px;
            width: 100%;
            max-width: 400px;
        }
        .reset-container h2 {
            color: #003366;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .reset-container p {
            color: #555;
            margin-bottom: 25px;
        }
        .form-group {
            margin-bottom: 18px;
        }
        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #cfd8dc;
            border-radius: 6px;
            font-size: 16px;
        }
        .btn-primary {
            background: #003366;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 10px 0;
            width: 100%;
            font-size: 16px;
            font-weight: 600;
            transition: background 0.2s;
        }
        .btn-primary:hover {
            background: #002244;
        }
        .alert-success {
            background: #e6f4ea;
            color: #256029;
            border: 1px solid #b7e1cd;
            border-radius: 6px;
            padding: 10px 15px;
            margin-bottom: 18px;
            font-size: 15px;
        }
        .text-danger {
            color: #d32f2f;
            font-size: 14px;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 22px;
            color: #003366;
            text-decoration: none;
            font-size: 15px;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .icon-lock {
            font-size: 38px;
            color: #003366;
            display: block;
            text-align: center;
            margin-bottom: 12px;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <span class="icon-lock">
            <i class="bi bi-lock"></i>
        </span>
        <h2>¿Olvidaste tu contraseña?</h2>
        <p>Ingresa tu email institucional y te enviaremos un enlace para restablecerla.</p>
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="form-group">
                <label for="email">email institucional</label>
                <input type="email" name="email" id="email" class="form-control" required autofocus>
                @error('email')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Enviar enlace</button>
        </form>
        <a href="{{ route('login') }}" class="back-link">
            <i class="bi bi-arrow-left"></i> Volver al inicio de sesión
        </a>
    </div>
</body>
</html>
