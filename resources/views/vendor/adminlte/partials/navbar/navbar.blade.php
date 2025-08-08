@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

<nav class="main-header navbar
    {{ config('adminlte.classes_topnav_nav', 'navbar-expand') }}
    {{ config('adminlte.classes_topnav', 'navbar-white navbar-light') }}">

    {{-- Navbar left links --}}
    <ul class="navbar-nav">
        {{-- Left sidebar toggler link --}}
        @include('adminlte::partials.navbar.menu-item-left-sidebar-toggler')

        {{-- Configured left links --}}
        @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-left'), 'item')

        {{-- Custom left links --}}
        @yield('content_top_nav_left')
    </ul>

    {{-- Navbar right links --}}
    <ul class="navbar-nav ml-auto">
        {{-- Custom right links --}}
        @yield('content_top_nav_right')

        {{-- Configured right links --}}
        @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-right'), 'item')

        {{-- Botón de modo oscuro --}}
        <a class="nav-link" href="javascript:void(0)" id="darkModeToggle" title="Modo Oscuro">
            <i class="fas fa-moon"></i>
        </a>

        {{-- Campana de notificaciones --}}
        <li class="nav-item dropdown">
            @php
                $notificacionesNoLeidas = Auth::user()->unreadNotifications->count();
            @endphp
            <a class="nav-link" data-toggle="dropdown" href="#" title="Notificaciones">
                <i class="fas fa-bell"></i>
                @if($notificacionesNoLeidas > 0)
                    <span class="badge badge-danger navbar-badge">{{ $notificacionesNoLeidas }}</span>
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                @forelse(Auth::user()->unreadNotifications as $notification)
                    @php
                        $data = $notification->data;
                        $tipo = $data['tipo'] ?? 'default';
                    @endphp

                    @if ($tipo === 'DocumentoRecibido')
                        <a href="{{ $data['url'] }}" class="dropdown-item">
                            <i class="fas fa-file-alt mr-2 text-primary"></i> 
                            <strong>{{ $data['titulo'] }}</strong>
                            <div class="mt-1">{{ $data['mensaje'] }}</div>
                            <span class="float-right text-muted text-sm">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </a>
                        <div class="dropdown-divider"></div>
                    @elseif ($tipo === 'TareaAsignadaNotification')
                        <a href="{{ route('tareas.show', $data['tarea_id']) }}" class="dropdown-item">
                            <i class="fas fa-tasks mr-2"></i> {{ $data['nombre'] }}
                            <br>
                            <small>{{ $data['descripcion'] ?? '' }}</small>
                            <span class="float-right text-muted text-sm">{{ $notification->created_at->diffForHumans() }}</span>
                        </a>
                    @elseif ($tipo === 'AcuseEnviadoNotification')
                        <a href="{{ $data['url'] ?? route('acuses.index') }}" class="dropdown-item">
                            <i class="fas fa-envelope mr-2"></i> {{ $data['titulo'] ?? 'Nuevo Acuse' }}
                            <br>
                            <small>{{ $data['mensaje'] ?? '' }} de <strong>{{ $data['nombre'] ?? '' }}</strong></small>
                            <span class="float-right text-muted text-sm">{{ $notification->created_at->diffForHumans() }}</span>
                        </a>
                    @elseif ($tipo === 'nuevo-proceso-terna')
                        <a href="{{ $data['url'] }}" class="dropdown-item">
                            <i class="fas fa-file-invoice mr-2"></i> {{ $data['titulo'] }}
                            <br>
                            <small>{{ $data['mensaje'] }}</small>
                            <span class="float-right text-muted text-sm">{{ $notification->created_at->diffForHumans() }}</span>
                        </a>
                    @elseif ($tipo === 'proceso-completado-terna')
                        <a href="{{ $data['url'] }}" class="dropdown-item">
                            <i class="fas fa-check-circle mr-2"></i> {{ $data['titulo'] }}
                            <br>
                            <small>{{ $data['mensaje'] }}</small>
                            <span class="float-right text-muted text-sm">{{ $notification->created_at->diffForHumans() }}</span>
                        </a>
                    @endif
                @empty
                    <span class="dropdown-item text-muted">Sin notificaciones nuevas</span>
                @endforelse

                <div class="dropdown-divider"></div>
                <a href="{{ route('notificaciones.leer_todas') }}" class="dropdown-item dropdown-footer">Marcar todas como leídas</a>
            </div>
        </li>

        {{-- User menu link --}}
        @if(Auth::user())
            @if(config('adminlte.usermenu_enabled'))
                @include('adminlte::partials.navbar.menu-item-dropdown-user-menu')
            @else
                @include('adminlte::partials.navbar.menu-item-logout-link')
            @endif
        @endif

        {{-- Right sidebar toggler link --}}
        @if($layoutHelper->isRightSidebarEnabled())
            @include('adminlte::partials.navbar.menu-item-right-sidebar-toggler')
        @endif
    </ul>
</nav>
