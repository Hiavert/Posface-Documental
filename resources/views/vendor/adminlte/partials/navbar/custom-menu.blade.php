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
            <a href="#" class="dropdown-item">
                <i class="fas fa-tasks mr-2"></i> {{ $notification->data['nombre'] }}
                <span class="float-right text-muted text-sm">{{ $notification->created_at->diffForHumans() }}</span>
            </a>
        @empty
            <span class="dropdown-item text-muted">Sin notificaciones nuevas</span>
        @endforelse
        <div class="dropdown-divider"></div>
        <a href="{{ route('notificaciones.leer_todas') }}" class="dropdown-item dropdown-footer">Marcar todas como leídas</a>
    </div>
</li> 