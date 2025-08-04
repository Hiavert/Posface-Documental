<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - POSFACE</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
    <div class="container">
        <!-- Panel Izquierdo -->
        <div class="left-panel">
            <img src="{{ asset('Imagen/logo-unah.png') }}" alt="Logo POSFACE" class="logo-img" />
            <h2>POSFACE - UNAH</h2>
            <p>Formamos profesionales con valores y visión gerencial para el desarrollo económico del país.</p>
            <div class="socials">
                <a href="https://posface.unah.edu.hn/" target="_blank" class="info-link">
                    <i class="bi bi-globe"></i>
                </a>
                <a href="https://www.instagram.com/posface_oficial" target="_blank" class="info-link">
                    <i class="bi bi-instagram"></i>
                </a>
            
            </div>
        </div>

        <!-- Panel Derecho -->
        <div class="right-panel">
            <p class="small-text">Bienvenido al sistema de gestión académica</p>
            <h2>Registro</h2>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="input-group">
                    <span>
                        <i class="bi bi-person"></i>
                    </span>
                    <input type="text" name="nombres" placeholder="Nombres" required />
                </div>

                <div class="input-group">
                    <span>
                        <i class="bi bi-person"></i>
                    </span>
                    <input type="text" name="apellidos" placeholder="Apellidos" required />
                </div>

                <div class="input-group">
                    <span>
                        <i class="bi bi-envelope"></i>
                    </span>
                    <input type="email" name="email" placeholder="email electrónico" required />
                </div>

                <div class="input-group">
                    <span>
                        <i class="bi bi-card-text"></i>
                    </span>
                    <input type="text" name="identidad" placeholder="Identidad" required />
                </div>

                <div class="input-group">
                    <span>
                        <i class="bi bi-person-badge"></i>
                    </span>
                    <select name="rol" class="form-control" required>
                        <option value="">Seleccione un rol</option>
                        @foreach($roles as $rol)
                            <option value="{{ $rol->id_rol }}">{{ $rol->nombre_rol }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="input-group">
                    <span>
                        <i class="bi bi-toggle-on"></i>
                    </span>
                    <select name="estado" class="form-control" required>
                        <option value="0" selected>Inactivo (por defecto)</option>
                        <option value="1">Activo</option>
                    </select>
                </div>

                <button type="submit" class="btn">Registrarse</button>
            </form>

            <p style="margin-top: 15px;">
                ¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión</a>
            </p>
        </div>
    </div>
</body>
</html>
