<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Establecer nueva contraseña - POSFACE</title>
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
            padding: 32px 28px 24px 28px;
            border-radius: 8px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 400px;
        }
        .reset-container h2 {
            color: #003366;
            margin-bottom: 18px;
            text-align: center;
        }
        .reset-container .input-group {
            margin-bottom: 18px;
            display: flex;
            align-items: center;
        }
        .reset-container .input-group span {
            width: 40px;
            text-align: center;
            color: #003366;
            background: #e9ecef;
            border-radius: 4px 0 0 4px;
            border: 1px solid #d1d5db;
            border-right: none;
        }
        .reset-container input {
            flex: 1;
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 0 4px 4px 0;
            outline: none;
            background: #f8f9fa;
            color: #003366;
        }
        .reset-container input:focus {
            background: #fff;
            border-color: #007bff;
        }
        .reset-container .btn {
            width: 100%;
            padding: 10px;
            background: #ffb300;
            color: #003366;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            margin-top: 10px;
            transition: background 0.2s;
        }
        .reset-container .btn:hover {
            background: #ffa000;
        }
        .alert-danger {
            margin-bottom: 18px;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <h2>Establecer nueva contraseña</h2>
        @if ($errors->any())
            <div class="alert alert-danger" style="color:#c00;background:#fff;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">
            <div class="input-group">
                <span><i class="bi bi-lock"></i></span>
                <input type="password" name="password" placeholder="Nueva contraseña" required minlength="8">
            </div>
            <div class="input-group">
                <span><i class="bi bi-lock"></i></span>
                <input type="password" name="password_confirmation" placeholder="Confirmar contraseña" required minlength="8">
            </div>
            <button type="submit" class="btn">Guardar contraseña</button>
        </form>
    </div>
</body>
</html>
